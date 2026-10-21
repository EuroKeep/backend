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

namespace eurokeep\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\AccountTable;
use eurokeep\Model\Table\TransactionsTable;
use Override;

/**
 * AccountsCleanup command.
 */
class AccountsCleanupCommand extends Command
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function execute(Arguments $args, ConsoleIo $io)
    {
        parent::execute($args, $io);
        /** @var AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        $accounts = $accountTable->find()->all();

        /** @var TransactionsTable $transactionTable */
        $transactionTable = TableRegistry::getTableLocator()->get('Transactions');
        foreach ($accounts as $account) {
            $value = $transactionTable
                ->find()
                ->where([
                    'account_id' => $account->get('id')
                ])
                ->all()
                ->sumOf('balance_value');

            $account->balance_value = $value;
            $accountTable->save($account);
        }
    }
}
