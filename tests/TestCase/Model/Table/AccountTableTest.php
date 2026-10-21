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
use eurokeep\Model\Table\AccountTable;

/**
 * eurokeep\Model\Table\AccountTable Test Case
 */
class AccountTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \eurokeep\Model\Table\AccountTable
     */
    protected $Account;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Account',
        'app.Accounttype',
        'app.Transaction',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Account') ? [] : ['className' => AccountTable::class];
        $this->Account = $this->getTableLocator()->get('Account', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Account);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \eurokeep\Model\Table\AccountTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \eurokeep\Model\Table\AccountTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
