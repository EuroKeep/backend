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

namespace eurokeep\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use eurokeep\Model\Table\MovementTable;

/**
 * eurokeep\Model\Table\MovementTable Test Case
 */
class MovementTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \eurokeep\Model\Table\MovementTable
     */
    protected $Movement;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Transaction',
        'app.Category',
        'app.Account',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Transactions') ? [] : ['className' => MovementTable::class];
        $this->Movement = $this->getTableLocator()->get('Transactions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Movement);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \eurokeep\Model\Table\MovementTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
