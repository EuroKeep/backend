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

namespace eurokeep\Model\Entity;

use Cake\Chronos\Chronos;
use Cake\Datasource\ResultSetInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\CategoriesTable;

/**
 * Budget Entity
 *
 * @property int $id
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property int $flags
 * @property string $name
 * @property float $limit
 * @property int $category_id
 * @property int $user_id
 * @property string $currency
 *
 * @property Category $category
 */
class Budget extends Entity
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
        'limit' => true,
        'category_id' => true,
        'user_id' => true,
        'currency' => true,
        'category' => true,
    ];

    /**
     * I will find those transactions of the current Budget's Category (and their Children) that took place between $start and $end
     * @param Chronos $start I am the start of the time range to watch for transactions that match the Budget's criteria.
     * @param Chronos $end I am the end of the time range to watch for transactions that match the Budget's criteria.
     */
    public function getTransactions(Chronos $start, Chronos $end): ResultSetInterface
    {
        $transactionTable = TableRegistry::getTableLocator()->get('Transactions');

        /** @var CategoriesTable $categoriesTable */
        $categoriesTable = $transactionTable->Categories;
        $category = $categoriesTable->get($this->category_id);

        return $category->getTransactions($start, $end, $this->currency);
    }
}
