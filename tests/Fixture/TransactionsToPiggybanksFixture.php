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

namespace eurokeep\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransactionsToPiggybanksFixture
 */
class TransactionsToPiggybanksFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'created' => '2026-08-24 20:39:36',
                'modified' => '2026-08-24 20:39:36',
                'flags' => 1,
                'balance_value' => 1.5,
                'transaction_id' => 1,
                'piggybank_id' => 1,
            ],
        ];
        parent::init();
    }
}
