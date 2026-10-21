<?php declare(strict_types=1);

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

namespace eurokeep\Identifier;


use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\IdentifierInterface;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\UsersTable;

class ApikeyIdentifier extends AbstractIdentifier implements IdentifierInterface
{

    public function identify(array $credentials): array|null
    {
        if (empty($credentials['Authorization'])) {
            return null;
        }

        try {
            return $this->_findIdentity($credentials['Authorization'][0]);

        } catch (\Exception $e) {
            return null;
        }
    }

    protected function _findIdentity(string $apikey)
    {
        //
        $apikey = str_replace('Bearer ', '', $apikey);
        /** @var UsersTable $UserTable */
        $UsersTable = TableRegistry::getTableLocator()->get('Users');

        return $UsersTable->unhydratedFind()->where([
            'api_key' => $apikey
        ])->firstOrFail();
    }
}
