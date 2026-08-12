<?php
declare(strict_types=1);

namespace eurokeep\Model\Entity;

use Cake\Chronos\Chronos;
use Cake\Datasource\ResultSetInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Entity;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

/**
 * Category Entity
 *
 * @property int $id
 * @property DateTime|null $created
 * @property DateTime|null $modified
 * @property int $flags
 * @property string $emoji
 * @property string $name
 * @property int|null $parent_id
 * @property int $lft
 * @property int $rght
 *
 * @property Budget[] $budget
 * @property Transaction[] $transaction
 * @property Subscription[] $subscription
 */
class Category extends Entity
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
        'emoji' => true,
        'name' => true,
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'budget' => true,
        'transactions' => true,
        'subscription' => true,
    ];

    /**
     * I will return all Categories that are the direct children of this Category.
     * @return ResultSetInterface The list of Categories that are children of this Category.
     */
    public function getChildren(bool $direct = true): ResultSetInterface
    {
        return TableRegistry::getTableLocator()
            ->get('Categories')
            ->find('children', [
                'for' => $this->id,
                'direct' => $direct
            ])
            ->all();
    }

    /**
     * I will find all Transactions for this category or for the nested child Categories.
     * @param Chronos $start I am the start of the time range where the Transactions will be searched.
     * @param Chronos $end I am the end of the time range where the Transactions will be searched.
     * @param string $currency I am the Currency this Budget is valid for.
     * @return ResultSetInterface I am the list of Transactions found in the range.
     */
    public function getTransactions(Chronos $start, Chronos $end, string $currency): ResultSetInterface
    {
        $accountIds = TableRegistry::getTableLocator()
            ->get('Account')
            ->find()
            ->where([
                'balance_currency' => $currency
            ])
            ->all()
            ->extract('id')
            ->toArray();
        if(empty($accountIds)) {
            return new ResultSet([]);
        }

        $descendants = $this
            ->getChildren(false)
            ->extract('id')
            ->toList();
        $descendants[] = $this->id;

        return TableRegistry::getTableLocator()
            ->get('Transactions')
            ->find()
            ->where([
                'Account.id IN' => $accountIds,
                'Transactions.category_id IN' => $descendants,
                'Transactions.created >=' => $start,
                'Transactions.created <' => $end,
#                'Transactions.balance_value <' => 0
            ])
            ->contain(['Account', 'Categories'])
            ->orderBy(['Transactions.created' => 'DESC'])
            ->all();
    }
}
