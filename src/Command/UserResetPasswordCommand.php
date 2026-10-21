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
use Cake\Utility\Hash;
use eurokeep\Model\Table\UsersTable;
use Override;

/**
 * UserResetPassword command.
 */
class UserResetPasswordCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser The built parser.
     */
    #[Override]
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param Arguments $args The command arguments.
     * @param ConsoleIo $io The console io
     * @return null|void|int The exit code or null for success
     */
    #[Override]
    public function execute(Arguments $args, ConsoleIo $io)
    {
        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->find()->toArray();
        $io->out('Select the user of your EuroKeep instance, whose password will be reset.');

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

        $password = $io->ask('Please enter the new password.');
        $password = trim($password);

        $user = $mapping[$selection];
        $usersTable->setPassword($user, $password);

        $io->out("The password of user #{$user['id']}, {$user['email']} has been changed.");
        $io->out("That's all, folks.");
    }
}
