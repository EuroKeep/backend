<?php
declare(strict_types=1);

namespace eurokeep\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use DateTime;
use eurokeep\Model\Table\AccountTable;
use eurokeep\Model\Table\TransactionsTable;

/**
 * Account Entity
 *
 * @property int $id
 * @property DateTime|null $created
 * @property DateTime|null $modified
 * @property int $flags
 * @property string $name
 * @property string $emoji
 * @property string $iban
 * @property string $balance_value
 * @property string $balance_currency
 * @property int $accounttype_id
 * @property int $user_id
 *
 * @property Accounttype $accounttype
 * @property Transaction[] $transactions
 * @property User $user
 */
class Account extends Entity
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
        'emoji' => true,
        'iban' => true,
        'balance_value' => true,
        'balance_currency' => true,
        'accounttype_id' => true,
        'user_id' => true,
        'accounttype' => true,
        'transactions' => true,
        'user' => true,
    ];

    /**
     * I will summarize the Transactions on the Account and store the current balance to it.
     */
    public function summarize(): void
    {
        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
        $value = $transactionsTable
            ->find()
            ->where([
                'account_id' => $this->get('id')
            ])
            ->all()
            ->sumOf('balance_value');

        $this->set('balance_value', $value);

        /** @var AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        $accountTable->save($this);
    }

    /**
     * Streamtransactions
     */
    public function streamTransactions(DateTime $start, DateTime $end): array
    {
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');
        return $transactionsTable
            ->find()
            ->contain(['Categories'])
            ->where([
                'Transactions.created >=' => $start,
                'Transactions.created <' => $end,
                'account_id' => $this->id
            ])
            ->order(['Transactions.created' => 'DESC'])
            ->limit(250)
            ->toArray();

    }

    /**
     * Settlement
     */
    public function settlement(array $transactions): array
    {
        $settlement = [
            'expenses' => 0,
            'revenues' => 0,
            'settlement' => 0
        ];
        foreach ($transactions as $transaction) {
            if ($transaction->balance_value < 0) {
                $settlement['expenses'] += $transaction->balance_value;
            } else {
                $settlement['revenues'] += $transaction->balance_value;
            }
            $settlement['settlement'] += $transaction->balance_value;
        }

        return $settlement;
    }
}
