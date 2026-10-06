<?php declare(strict_types=1);

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

        return $UsersTable->find()->where([
            'api_key' => $apikey
        ])->disableHydration()->firstOrFail();
    }
}
