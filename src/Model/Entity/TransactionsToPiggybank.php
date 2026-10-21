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

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\PiggybankTable;

/**
 * TransactionsToPiggybank Entity
 *
 * @property int $id
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property int $flags
 * @property string $balance_value
 * @property int $transaction_id
 * @property int $piggybank_id
 *
 * @property \eurokeep\Model\Entity\Account $account
 * @property \eurokeep\Model\Entity\Piggybank $piggybank
 */
class TransactionsToPiggybank extends Entity
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
        'transaction_id' => true,
        'piggybank_id' => true,
        'account' => true,
        'piggybank' => true,
    ];

    /**
     * I will return the Piggybank that is used on this Transaction.
     */
    final public function getPiggybank(): Piggybank
    {
        /** @var PiggybankTable $piggybankTable */
        $piggybankTable = TableRegistry::getTableLocator()->get('Piggybank');
        return $piggybankTable->get($this->get('piggybank_id'));
    }
}
