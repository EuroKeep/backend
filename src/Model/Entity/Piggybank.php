<?php
declare(strict_types=1);

namespace eurokeep\Model\Entity;

use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\PiggybankTable;
use eurokeep\Model\Table\TransactionsTable;
use eurokeep\Model\Table\TransactionsToPiggybanksTable;

/**
 * Piggybank Entity
 *
 * @property int $id
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property int $flags
 * @property int $account_id
 * @property string $name
 * @property float $balance_value
 * @property string $balance_currency
 * @property FrozenDate|null $start
 * @property FrozenDate|null $end
 * @property float $target
 * @property string|null $description
 * @property int $user_id
 */
class Piggybank extends Entity
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
        'account_id' => true,
        'name' => true,
        'balance_value' => true,
        'balance_currency' => true,
        'start' => true,
        'end' => true,
        'target' => true,
        'description' => true,
        'user_id' => true
    ];

    /**
     * I will transaction the given $value from the given $account to the Piggybank.
     * @param Account $account I am the Account the money comes from.
     * @param float $value I am the balance that is added to the Piggybank.
     * @param string $comment I am the comment the user added.
     * @param Category $Category I am the Category that is used.
     */
    public function save(Account $account, float $value, string $comment, Category $Category): void
    {
        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');

        // Transaction to remove money from the account
        $PassiveTransaction = $transactionsTable->newEmptyEntity();
        $PassiveTransaction = $transactionsTable->patchEntity($PassiveTransaction, [
            'balance_value' => -$value,
            'balance_currency' => 'EUR',
            'comment' => $comment,
            'category_id' => $Category->get('id'),
            'account_id' => $account->get('id'),
            'user_id' => 1,
        ]);
        $transactionsTable->save($PassiveTransaction);


        // Transaction to add money to piggybank
        $ActiveTransaction = $transactionsTable->newEmptyEntity();
        $ActiveTransaction = $transactionsTable->patchEntity($ActiveTransaction, [
            'balance_value' => $value,
            'balance_currency' => 'EUR',
            'comment' => $comment,
            'category_id' => $Category->get('id'),
            'account_id' => 20,
            'piggybank_id' => $this->get('id'),
            'user_id' => 1,
        ]);
        $transactionsTable->save($ActiveTransaction);

        // Summarize the Piggybank again
        $this->summarize();
    }

    /**
     * I will find all transactions connected to the given $Piggybank and summarize them.
     * The sum will be stored at the Piggyback.
     */
    public function summarize(): void
    {
        /** @var TransactionsToPiggybanksTable $transactionsToPiggybanksTable */
        $transactionsToPiggybanksTable = TableRegistry::getTableLocator()->get('TransactionsToPiggybanks');
        $value = $transactionsToPiggybanksTable
            ->find()
            ->where([
                'piggybank_id' => $this->get('id')
            ])
            ->all()
            ->sumOf('balance_value');

        $this->set('balance_value', $value);

        /** @var PiggybankTable $PiggybankTable */
        $PiggybankTable = TableRegistry::getTableLocator()->get('Piggybank');
        $PiggybankTable->save($this);
    }
}
