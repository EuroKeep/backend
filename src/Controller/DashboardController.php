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
