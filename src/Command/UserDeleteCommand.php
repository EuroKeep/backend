<?php
declare(strict_types=1);

namespace eurokeep\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use eurokeep\Model\Table\UsersTable;
use Override;

/**
 * UserDelete command.
 */
class UserDeleteCommand extends Command
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
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->find()->toArray();
        $io->out('Select the user you want to remove from your EuroKeep instance');

        $mapping = [];
        foreach ($users as $user) {
            $io->out(sprintf(
                    '%s: %s (%s)',
                    $user['id'],
                    $user['name'],
                    $user['email']
                )
            );
            $mapping[$user['id']] = $user;
        }

        $userIds = Hash::extract($users, '{n}.id');

        $selection = $io->askChoice('Please select a user ID', $userIds);


        $email = $io->ask('Please enter the user\' email address for sake of security.');
        $email = trim($email);

        $user = $mapping[$selection];

        if ($email !== $user['email']) {
            $io->out('Seems you were wrong with the email address.');
            $io->out("That's all, folks.");
            exit(1);
        }
        $usersTable->delete($user);

        $io->out("The user #{$user['id']}, {$user['email']} has been deleted, including all accounts, transactions, etc.");
        $io->out("That's all, folks.");
    }
}
