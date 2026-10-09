<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Authentication\Controller\Component\AuthenticationComponent;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\AccountTable;

/**
 * Auth Controller.
 *
 * I handle logging in and out.
 *
 * @property AuthenticationComponent $Authentication
 *
 */
class DashboardController extends AuthenticatedController
{
    public function networth()
    {
        $this->request->allowMethod(['get']);
        /** @var AccountTable $AccountsTable */
        $AccountsTable = TableRegistry::getTableLocator()->get('Account');

        $total = 0;
        $accounts = $AccountsTable->find()->all();

        foreach ($accounts as $account) {
            // TODO: CALCULATE TO MAIN CURRENCY
            $total += $account->balance_value;
        }

        $networth = [
            'balance_value' => $total,
            'balance_currency' => 'eur',
        ];

        $this->set('item', $networth);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['item', 'success']);
    }
}
