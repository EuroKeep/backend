<?php
declare(strict_types=1);

namespace eurokeep\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\AccountTable;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property int $flags
 * @property string|null $balance_value
 * @property string $balance_currency
 * @property string $comment
 * @property int|null $category_id
 * @property int $account_id
 * @property int|null $transfer_id
 * @property int|null $piggybank_id
 *
 * @property Category $category
 * @property Subscription $subscription
 * @property Account $account
 */
class Transaction extends Entity
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
        'balance_value' => true,
        'balance_currency' => true,
        'comment' => true,
        'category_id' => true,
        'account_id' => true,
        'transfer_id' => true,
        'piggybank_id' => true,
        'subscription_id' => true,
        'category' => true,
        'account' => true,
        'subscription' => true
    ];

    /**
     * I will return the Account that is used on this Transaction.
     */
    final public function getAccount(): Account
    {
        /** @var AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        return $accountTable->get($this->get('account_id'));
    }
}
