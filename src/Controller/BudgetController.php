<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use eurokeep\Model\Table\TransactionsTable;

/**
 * Budget Controller
 *
 * @property \eurokeep\Model\Table\BudgetTable $Budget
 * @method \eurokeep\Controller\Budget[]|\eurokeep\Controller\ResultSetInterface paginate($object = null, array $settings = [])
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

    public function total(): void
    {

        $this->request->allowMethod(['get']);
        [$start, $end] = $this->getRange();

        $budgetQuery = $this
            ->Budget
            ->find()
            ->contain(['Categories']);
        $items = $budgetQuery->all()->toArray();

        // TOTALS
        $total = [
            'spentValue' => 0,
            'total' => 0,
            'freeValue' => 0,
        ];

        /** @var \eurokeep\Model\Entity\Budget $budget */
        foreach ($items as $budget) {
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

        $this->set('item', $total);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success', 'item']);
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
        $items = $budgetQuery->all()->toArray();

        /** @var \eurokeep\Model\Entity\Budget $budget */
        foreach ($items as $budget) {
            $budget->category->transactions = $budget->getTransactions($start, $end);
            $budget->spent = TransactionsTable::summarize($budget->category->transactions);

            // Type issue here
            $budget->limit = (float)$budget->limit;
        }

        $this->set('items', $items);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success', 'total']);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->Budget, ['user_id' => $this->user->id]);
    }

    /**
     * View method
     *
     * @param mixed $budgetId Identifier of the budget. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($budgetId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Budget, $budgetId, 'Budget', ['contain' => []]);
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
        $this->request->allowMethod(['patch', 'PUT']);
        $this->editEntity($this->Budget, $budgetId, 'Budget');
    }

    /**
     * Delete method
     *
     * @param mixed $budgetId Identifier of the budget. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError. Ownership is enforced by
     *   OwnedTable::get() (throws UnauthorizedException, caught as a 401 by deleteEntity()),
     *   so no separate ownership check is needed here.
     */
    public function delete($budgetId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Budget, $budgetId, 'Budget');
    }
}
