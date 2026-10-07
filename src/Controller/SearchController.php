<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\AccountTable;
use eurokeep\Model\Table\BudgetTable;
use eurokeep\Model\Table\PiggybankTable;
use eurokeep\Model\Table\SubscriptionTable;
use eurokeep\Model\Table\TransactionsTable;

/**
 * Budget Controller
 *
 * @property \eurokeep\Model\Table\BudgetTable $Budget
 * @method \eurokeep\Controller\Budget[]|\eurokeep\Controller\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SearchController extends AuthenticatedController
{
    public function everywhere(): void
    {
        $this->request->allowMethod(['get']);

        $query = $this->request->getQuery()['query'] ?? '';


        $items = [];
        $items['accounts'] = $this->searchAccounts($query);
        $items['transactions'] = $this->searchTransactions($query);
        $items['budgets'] = $this->searchBudgets($query);
        $items['piggybanks'] = $this->searchPiggybanks($query);
        $items['subscriptions'] = $this->searchSubscriptions($query);

        $this->set('items', $items);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    private function searchAccounts(string $search): array
    {
        /** @var AccountTable $AccountsTable */
        $AccountsTable = TableRegistry::getTableLocator()->get('Account');

        return $AccountsTable
            ->find()
            ->where([
                'user_id' => $this->user->id,
                'name LIKE ' => "%$search%",
            ])
            ->orderByAsc('name')
            ->toArray();
    }

    private function searchTransactions(string $search): array
    {
        /** @var TransactionsTable $TransactionsTable */
        $TransactionsTable = TableRegistry::getTableLocator()->get('Transactions');

        return $TransactionsTable
            ->find()
            ->where([
                'comment LIKE ' => "%$search%",
            ])
            ->orderByDesc('created')
            ->toArray();
    }

    private function searchBudgets(string $search): array
    {
        /** @var BudgetTable $BudgetTable */
        $BudgetTable = TableRegistry::getTableLocator()->get('Budget');

        return $BudgetTable
            ->find()
            ->where([
                'name LIKE ' => "%$search%",
            ])
            ->toArray();
    }

    private function searchPiggybanks(string $search): array
    {
        /** @var PiggybankTable $PiggybankTable */
        $PiggybankTable = TableRegistry::getTableLocator()->get('Piggybank');

        return $PiggybankTable
            ->find()
            ->where([
                'name LIKE ' => "%$search%",
            ])
            ->toArray();
    }

    private function searchSubscriptions(string $search): array
    {
        /** @var SubscriptionTable $SubscriptionTable */
        $SubscriptionTable = TableRegistry::getTableLocator()->get('Subscription');

        return $SubscriptionTable
            ->find()
            ->where([
                'name LIKE ' => "%$search%",
            ])
            ->toArray();
    }
}
