<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
use Cake\ORM\TableRegistry;
use DateTime;
use eurokeep\Model\Table\CategoriesTable;
use eurokeep\Model\Table\TransactionsTable;

/**
 * Analyzer Controller
 */
class AnalyzerController extends AuthenticatedController
{
    /**
     * @return void
     * @throws \DateMalformedStringException
     */
    public function category(): void
    {
        $this->request->allowMethod(['get']);
        $start = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['start']))->startOfMonth();
        $end = Chronos::createFromInterface(new DateTime($this->request->getQueryParams()['end']))->startOfMonth();
        $categoryId = $this->request->getQueryParams()['categoryId'] ?? 64;

        /** @var CategoriesTable $categoriesTable */
        $categoriesTable = TableRegistry::getTableLocator()->get('Categories');
        $categories = $categoriesTable
            ->find()
            ->where(['parent_id' => (int)$categoryId])
            ->all();
        $category = $categoriesTable->get($categoryId);

        $items = [];
        $total = 0;
        $transactions = [];

        $categoryTransactions = $category->getTransactions($start, $end, 'eur', false, true);
        foreach ($categoryTransactions as $transaction) {
            $transactions[] = $transaction;
        }
        $value = -TransactionsTable::summarize($categoryTransactions);

        $total += $value;
        $items[] = [
            'name' => "$category->emoji",
            'value' => $value,
            'id' => $category->id,
        ];

        // Now children
        foreach ($categories as $category) {
            $categoryTransactions = $category->getTransactions($start, $end, 'eur', true, true);
            $value = -TransactionsTable::summarize($categoryTransactions);
            foreach ($categoryTransactions as $transaction) {
                $total += $transaction->balance_value;
                $transactions[] = $transaction;
            }
            if ($value == 0) {
                continue;
            }
            $items[] = [
                'name' => "$category->emoji",
                'value' => $value,
                'id' => $category->id,
            ];
        }

        $this->set('transactions', $transactions);
        $this->set('items', $items);
        $this->set('total', $total);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'transactions', 'total', 'success']);
    }
}
