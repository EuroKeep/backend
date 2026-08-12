<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use eurokeep\Model\Entity\Budget;
use eurokeep\Model\Table\BudgetTable;
use eurokeep\Model\Table\TransactionsTable;

/**
 * Budget Controller
 *
 * @property BudgetTable $Budget
 * @method \eurokeep\Controller\Budget[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class BudgetController extends AuthenticatedController
{
    /**
     * Index method
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $budgetQuery = $this
            ->Budget
            ->find();
        $budgetQuery->contain([
            'Categories',
        ]);

        $filters = $this->getRequest()->getQueryParams()['filter'] ?? [];
        foreach ($filters as $fieldname => $value) {
            $budgetQuery->where(["$fieldname LIKE" => "$value%",]);
        }

        $budgets = $budgetQuery->all()->toArray();

        $this->set('items', $budgets);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    /**
     * I will list the Budgets for the API
     */
    public function list(): void
    {
        $this->request->allowMethod(['get']);
        [$start, $end] = $this->getRange();

        $budgetQuery = $this
            ->Budget
            ->find()
            ->contain(['Categories']);
        $budgets = $budgetQuery->all()->toArray();

        // TOTALS
        $total = [
            'spentValue' => 0,
            'total' => 0,
            'freeValue' => 0,
        ];

        /** @var Budget $budget */
        foreach ($budgets as $budget) {
            $budget->category->transactions = $budget->getTransactions($start, $end);
            $budget->spent = TransactionsTable::summarize($budget->category->transactions);

            // Type issue here
            $budget->limit = (float)$budget->limit;

            $total['spentValue'] += round($budget->spent, 2);
            $total['total'] += $budget->limit;
        }

        $total['freeValue'] += max(0, $total['total'] + $total['spentValue']);
        $total['spentPercent'] = min(100, round((-$total['spentValue']) / $total['total'] * 100, 2));
        $total['freePercent'] = 100 - $total['spentPercent'];

        $this->set('budgets', $budgets);
        $this->set('total', $total);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['budgets', 'success', 'total']);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success']);

        $data = $this->request->getData();
        $data['user_id'] = $this->user->id;
        $budgetTable = $this->Budget;
        $entity = $budgetTable->newEmptyEntity();
        $entity = $budgetTable->patchEntity($entity, $data);
        $budgetTable->save($entity);

        if ($entity->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('error', $entity->getErrors());
            $this->viewBuilder()->setOption('serialize', ['error']);

            return;
        }
        $this->set('budget', $entity);
        $this->viewBuilder()->setOption('serialize', ['success', 'budget']);
    }

    /**
     * View method
     *
     * @param int|null $budgetId Budget id.
     * @throws RecordNotFoundException When record not found.
     */
    public function view(?int $budgetId = null): void
    {
        $this->request->allowMethod(['get']);
        $budget = $this->Budget->get($budgetId, [
            'contain' => [],
        ]);

        $this->set('budget', $budget);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['budget', 'success']);
    }

    /**
     * Edit method
     *
     * @param mixed $budgetId Identifier of the budget. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function edit($budgetId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$budgetId)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Missing or invalid budget id']);

            return;
        }

        // Malformed JSON / non-object body (BodyParserMiddleware returns null on invalid JSON)
        $data = $this->request->getData();
        if (!is_array($data)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Invalid JSON body']);

            return;
        }

        try {
            $item = $this->Budget->get((int)$budgetId);
        } catch (RecordNotFoundException) {
            $this->response = $this->response->withStatus(404);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Budget not found']);

            return;
        }

        // Apply the new data
        $item = $this->Budget->patchEntity($item, $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', $item->getErrors());

            return;
        }

        // Cannot save?
        if (!$this->Budget->save($item)) {
            $this->response = $this->response->withStatus(500);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Internal error']);

            return;
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * Delete method
     *
     * @param int|null $budgetId Budget id.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete(?int $budgetId = null): void
    {
        $this->request->allowMethod(['delete']);
        $budget = $this->Budget->get($budgetId);

        if ($budget->user_id !== $this->user->id) {
            $this->http401('This is not your budget.');
        }

        if (!$this->Budget->delete($budget)) {
            $this->addError('Error', 'Budget not deleted.');
        }
    }
}
