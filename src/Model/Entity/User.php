<?php
declare(strict_types=1);

namespace eurokeep\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\AccountTable;

/**
 * User Entity
 *
 * @property int $id
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property int $flags
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property array $settings
 *
 * @property Account[] $account
 * @property Transaction[] $transaction
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'created' => true,
        'modified' => true,
        'flags' => true,
        'name' => true,
        'email' => true,
        'password' => true,
        'api_key' => true,
        'account' => true,
        'transaction' => true,
        'role_id' => true,
        'settings' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array<string>
     */
    protected array $_hidden = [
        'password',
        'api_key' => true,
    ];

    /**
     * @param string $password
     */
    protected function _setPassword(string $password): string
    {
        $hasher = new DefaultPasswordHasher();

        return $hasher->hash($password);
    }

    /**
     * I will return the total balance of all Accounts.
     */
    final public function getTotalBalances(): array
    {
        /** @var AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');

        // Full Balance
        $qry = $accountTable->find();
        $qry->where(['user_id' => $this->id])
            ->select([
                'balance_currency',
                'total_balance' => $qry->func()->sum('balance_value')
            ])
            ->group(['balance_currency']);

        $sum = $qry->toArray();


        $totalBalances = [];
        foreach ($sum as $row) {
            $totalBalances[] = [
                'currency' => $row['balance_currency'],
                'value' => $row['total_balance'],
            ];
        }

        return $totalBalances;
    }
}
