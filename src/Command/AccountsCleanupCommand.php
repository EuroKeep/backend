<?php
declare(strict_types=1);

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
