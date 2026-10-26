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

        $query = $AccountsTable
            ->unhydratedFind();
        $query
            ->select([
                'balance_currency',
                'balance_value' => $query->func()->sum('balance_value'),
            ])
            ->where(['Account.user_id' => $this->user->id])
            ->having(['balance_value != ' => 0])
            ->groupBy(['balance_currency'])
            ->orderByAsc('balance_currency');

        $networth = [];
        foreach ($query->all() as $row) {
            $networth[] = [
                'balance_value' => $row['balance_value'],
                'balance_currency' => $row['balance_currency'],
            ];
        }

        $this->set('items', $networth);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }
}
