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
use Cake\ORM\TableRegistry;
use DateTime;

/**
 * Account Controller
 *
 * @property \eurokeep\Model\Table\AccountTable $Account
 * @method \eurokeep\Controller\Account[]|\eurokeep\Controller\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SummaryController extends AuthenticatedController
{
    /**
     * @throws \DateMalformedStringException
     */
    public function complete(): void
    {
        $this->request->allowMethod(['get']);
        $start = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['start']))->startOfMonth();
        $end = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['end']))->startOfMonth();
        /** @var \eurokeep\Model\Table\AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        /** @var \eurokeep\Model\Table\TransactionsTable $transactionTable */
        $transactionTable = TableRegistry::getTableLocator()->get('Transactions');

        $accounts = $this->user->settings['SUMMARY_ACCOUNTS'] ?? [];

        $accounts = $accountTable
            ->find()
            ->where([
                'id IN' => $accounts,
            ])
            ->orderBy(['FIELD(id, ' . implode(',', $accounts) . ')'])
            ->all()
            ->toArray();

        $accountIds = array_column($accounts, 'id');

        $transactions = $transactionTable
            ->find()
            ->contain(['Categories'])
            ->where([
                'account_id IN' => $accountIds,
                'Transactions.created >= ' => $start,
                'Transactions.created <= ' => $end,
            ])
            ->orderByAsc('Transactions.created')
            ->all()
            ->toArray();
        $query = $transactionTable->find();
        $query
            ->select([
                'account_id',
                'previous' => $query->expr(
                    'SUM(CASE WHEN created < :start THEN balance_value ELSE 0 END)',
                ),
                'current' => $query->expr(
                    'SUM(CASE WHEN created >= :start AND created <= :end THEN balance_value ELSE 0 END)',
                ),
            ])
            ->where([
                'account_id IN' => $accountIds,
                'created <=' => $end,
            ])
            ->bind(':start', $start, 'datetime')
            ->bind(':end', $end, 'datetime')
            ->groupBy('account_id');
        $balances = [];

        foreach ($query->all() as $row) {
            $balances[$row->account_id] = $row;
        }
        foreach ($accounts as $account) {
            if (!isset($balances[$account->id])) {
                $account->balance_value_closing = 0;
                $account->balance_value = 0;
                continue;
            }

            $account->balance_value_closing = $balances[$account->id]->previous;
            $account->balance_value = $balances[$account->id]->previous + $balances[$account->id]->current;
        }
        $summary = [
            'Accounts' => $accounts,
            'Transactions' => $transactions,
        ];
        $this->set('summary', $summary);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['summary', 'success']);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function closing(): void
    {
        $this->request->allowMethod(['get']);
        $start = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['start']))->startOfMonth();
        $end = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['end']))->startOfMonth();
        /** @var \eurokeep\Model\Table\AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        /** @var \eurokeep\Model\Table\TransactionsTable $transactionTable */
        $transactionTable = TableRegistry::getTableLocator()->get('Transactions');

        $query = $transactionTable->find();

        $accounts = $this->user->settings['SUMMARY_ACCOUNTS'] ?? [];

        $accounts = $accountTable
            ->find()
            ->where([
                'user_id' => $this->user->id,
                'id IN' => $accounts,
            ])
            ->orderBy(['FIELD(id, ' . implode(',', $accounts) . ')'])
            ->all()
            ->toArray();

        $accountIds = array_column($accounts, 'id');

        $query
            ->select([
                'account_id',
                'year' => $query->func()->year(['created' => 'identifier']),
                'month' => $query->func()->month(['created' => 'identifier']),
                'balance_value' => $query->func()->sum('balance_value'),
            ])
            ->where([
                'account_id IN' => $accountIds,
                'created >=' => $start,
                'created <=' => $end,
            ])
            ->groupBy([
                'account_id',
                $query->func()->year(['created' => 'identifier']),
                $query->func()->month(['created' => 'identifier']),
            ]);

        // one row per month in the requested range, one column per account, prefilled with 0
        $months = [];
        for ($cursor = $start; $cursor->lessThanOrEquals($end); $cursor = $cursor->addMonths(1)) {
            $key = $cursor->format('Y-m');
            $months[$key] = ['year' => (int)$cursor->format('Y'), 'month' => (int)$cursor->format('n')];
            foreach ($accountIds as $accountId) {
                $months[$key][$accountId] = 0;
            }
        }

        foreach ($query->all() as $row) {
            $key = sprintf('%04d-%02d', $row->year, $row->month);
            if (isset($months[$key])) {
                $months[$key][$row->account_id] = $row->balance_value;
            }
        }

        $summary = [
            'Accounts' => $accounts,
            'ClosingBalances' => array_values($months),
        ];
        $this->set('summary', $summary);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['summary', 'success']);
    }
}
