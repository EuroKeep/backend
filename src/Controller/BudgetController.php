<?php
declare(strict_types=1);

/**
 * EuroKeep - Your Money. Your Data. (https://eurokeep.io)
 * Copyright (c) 2026 IT Solutions Gleim (https://nox.kiwi)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * that was distributed with this source code.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2026 IT Solutions Gleim (https://nox.kiwi)
 * @link      https://eurokeep.io EuroKeep
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Entity\Budget;
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
        // Without any budget limit there is nothing to relate the spending to.
        if ($total['total'] > 0) {
            $total['spentPercent'] = min(100, round((-$total['spentValue']) / $total['total'] * 100, 2));
            $total['freePercent'] = 100 - $total['spentPercent'];
        } else {
            $total['spentPercent'] = 0;
            $total['freePercent'] = 0;
        }

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

        // Category ids (incl. children) already covered by one of the real Budgets above -
        // anything outside of this set is what the virtual "Unbudgetiert" entry below picks up.
        $budgetedCategoryIds = [];

        /** @var \eurokeep\Model\Entity\Budget $budget */
        foreach ($items as $budget) {
            $budget->category->transactions = $budget->getTransactions($start, $end);
            $budget->spent = TransactionsTable::summarize($budget->category->transactions);

            // Type issue here
            $budget->limit = (float)$budget->limit;

            $budgetedCategoryIds[] = $budget->category_id;
            foreach ($budget->category->getChildren(false)->extract('id') as $childCategoryId) {
                $budgetedCategoryIds[] = $childCategoryId;
            }
        }

        //$items[] = $this->getUnbudgetedEntry($start, $end, array_unique($budgetedCategoryIds));

        $this->set('items', $items);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success', 'total']);
    }

    /**
     * I will build a virtual, unsaved Budget that groups together every Transaction of the given
     * time range whose Category is not covered (neither directly nor via a parent/child Category)
     * by any of the User's real Budgets. This surfaces "Restbudget" spending that would otherwise
     * not show up under any Budget at all.
     *
     * @param Chronos $start Start of the time range to watch for unbudgeted Transactions.
     * @param Chronos $end End of the time range to watch for unbudgeted Transactions.
     * @param array<int> $budgetedCategoryIds Ids of every Category already covered by a real Budget.
     */
    private function getUnbudgetedEntry(Chronos $start, Chronos $end, array $budgetedCategoryIds): Budget
    {
        $accountIds = TableRegistry::getTableLocator()
            ->get('Account')
            ->find()
            ->all()
            ->extract('id')
            ->toArray();

        if (empty($accountIds)) {
            $transactions = new ResultSet([]);
        } else {
            $transactionsQuery = TableRegistry::getTableLocator()
                ->get('Transactions')
                ->find()
                ->where([
                    'Account.id IN' => $accountIds,
                    'Transactions.created >=' => $start,
                    'Transactions.created <' => $end,
                ])
                ->contain(['Account', 'Categories']);

            if (!empty($budgetedCategoryIds)) {
                $transactionsQuery->andWhere(fn(QueryExpression $exp) => $exp->or([
                    'Transactions.category_id IS' => null,
                    'Transactions.category_id NOT IN' => $budgetedCategoryIds,
                ]));
            }

            $transactions = $transactionsQuery
                ->orderBy(['Transactions.created' => 'DESC'])
                ->all();
        }

        /** @var Budget $unbudgeted */
        $unbudgeted = $this->Budget->newEmptyEntity();
        $unbudgeted->name = 'Unbudgetiert';
        $unbudgeted->limit = 0.0;
        $unbudgeted->currency = 'eur';
        $unbudgeted->virtual = true;

        $unbudgeted->category = $this->Budget->Categories->newEmptyEntity();
        $unbudgeted->category->name = 'Unbudgetiert';
        $unbudgeted->category->emoji = '❓';
        $unbudgeted->category->transactions = $transactions;

        $unbudgeted->spent = TransactionsTable::summarize($transactions);

        return $unbudgeted;
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
