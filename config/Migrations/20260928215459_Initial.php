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

use Migrations\BaseMigration;

/**
 * Initial database schema (snapshot of the production database, 2026-09-28).
 *
 * Replaces the former migrations Users, Accounting, Budget, Subscriptions,
 * RemoveSubscriptionOwnerDefaults, PiggybankConnections and PaymentMethods.
 */
class Initial extends BaseMigration
{
    public bool $autoId = false;

    /**
     * @inheritDoc
     */
    public function up(): void
    {

        $this->table('payment_method')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('image', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->create();

        $this->table('role')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->create();

        $this->table('accounttype')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->create();

        $this->table('users')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('api_key', 'string', [
                'default' => null,
                'limit' => 256,
                'null' => true,
            ])
            ->addColumn('role_id', 'integer', [
                'default' => '1',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('settings', 'text', [
                'collation' => 'utf8mb4_bin',
                'default' => '{}',
                'limit' => 2147483647,
                'null' => false,
            ])
            ->addIndex(
                $this->index('email')
                    ->setName('uq_users_email')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('role_id')
                    ->setName('idx_users_role_id')
            )
            ->addForeignKey(
                $this->foreignKey('role_id')
                    ->setReferencedTable('role')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_users_role_id')
            )
            ->create();

        $this->table('account')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('emoji', 'string', [
                'default' => null,
                'limit' => 8,
                'null' => false,
            ])
            ->addColumn('iban', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('balance_value', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('balance_currency', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('accounttype_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('accounttype_id')
                    ->setName('idx_account_accounttype_id')
            )
            ->addIndex(
                $this->index('user_id')
                    ->setName('idx_account_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_account_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('accounttype_id')
                    ->setReferencedTable('accounttype')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_account_accounttype_id')
            )
            ->create();

        $this->table('categories')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('emoji', 'string', [
                'default' => null,
                'limit' => 16,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('parent_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('lft', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('rght', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index('parent_id')
                    ->setName('idx_categories_parent_id')
            )
            ->addForeignKey(
                $this->foreignKey('parent_id')
                    ->setReferencedTable('categories')
                    ->setReferencedColumns('id')
                    ->setDelete('SET_NULL')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_categories_parent_id')
            )
            ->create();

        $this->table('budget')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('limit', 'decimal', [
                'default' => '0.00000000',
                'null' => false,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('category_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => '1',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('currency', 'string', [
                'default' => 'eur',
                'limit' => 3,
                'null' => false,
            ])
            ->addIndex(
                $this->index('category_id')
                    ->setName('idx_budget_category_id')
            )
            ->addIndex(
                $this->index('user_id')
                    ->setName('idx_budget_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_budget_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('category_id')
                    ->setReferencedTable('categories')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_budget_category_id')
            )
            ->create();

        $this->table('piggybank')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('account_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('balance_value', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('balance_currency', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('start', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('end', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('target', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 512,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => '1',
                'limit' => null,
                'null' => false,
                'signed' => false,
            ])
            ->create();

        $this->table('subscription')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => false,
            ])
            ->addColumn('account_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('balance_value', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('interval', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('start', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('end', 'date', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('term', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('noticeperiod', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('comments', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('category_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('paymentmethod_id', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('logo', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index('account_id')
                    ->setName('idx_subscription_account_id')
            )
            ->addIndex(
                $this->index('user_id')
                    ->setName('idx_subscription_user_id')
            )
            ->addIndex(
                $this->index('category_id')
                    ->setName('idx_subscription_category_id')
            )
            ->addIndex(
                $this->index('paymentmethod_id')
                    ->setName('paymentmethod_id')
            )
            ->addForeignKey(
                $this->foreignKey('category_id')
                    ->setReferencedTable('categories')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_subscription_category_id')
            )
            ->addForeignKey(
                $this->foreignKey('account_id')
                    ->setReferencedTable('account')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_subscription_account_id')
            )
            ->addForeignKey(
                $this->foreignKey('user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_subscription_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('paymentmethod_id')
                    ->setReferencedTable('payment_method')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_subscription_paymentmethod_id')
            )
            ->create();

        $this->table('transactions')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('balance_value', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 20,
                'scale' => 8,
            ])
            ->addColumn('balance_currency', 'string', [
                'default' => 'EUR',
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('comment', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => false,
            ])
            ->addColumn('category_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('account_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('transfer_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('piggybank_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('subscription_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                $this->index('created')
                    ->setName('idx_transactions_created')
            )
            ->addIndex(
                $this->index('category_id')
                    ->setName('idx_transactions_category_id')
            )
            ->addIndex(
                $this->index('transfer_id')
                    ->setName('idx_transactions_transfer_id')
            )
            ->addIndex(
                $this->index('account_id')
                    ->setName('idx_transactions_account_id')
            )
            ->addIndex(
                $this->index('subscription_id')
                    ->setName('idx_transactions_subscription_id')
            )
            ->addIndex(
                $this->index('user_id')
                    ->setName('user_id')
            )
            ->addForeignKey(
                $this->foreignKey('category_id')
                    ->setReferencedTable('categories')
                    ->setReferencedColumns('id')
                    ->setDelete('SET_NULL')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_category_id')
            )
            ->addForeignKey(
                $this->foreignKey('account_id')
                    ->setReferencedTable('account')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_account_id')
            )
            ->addForeignKey(
                $this->foreignKey('user_id')
                    ->setReferencedTable('users')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_user_id')
            )
            ->addForeignKey(
                $this->foreignKey('transfer_id')
                    ->setReferencedTable('transactions')
                    ->setReferencedColumns('id')
                    ->setDelete('SET_NULL')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_transfer_id')
            )
            ->addForeignKey(
                $this->foreignKey('subscription_id')
                    ->setReferencedTable('subscription')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_subscription_id')
            )
            ->create();

        $this->table('transactions_to_piggybanks')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('created', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('flags', 'integer', [
                'default' => '0',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('balance_value', 'decimal', [
                'default' => null,
                'null' => false,
                'precision' => 15,
                'scale' => 2,
            ])
            ->addColumn('transaction_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('piggybank_id', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                $this->index([
                        'piggybank_id',
                        'transaction_id',
                    ])
                    ->setName('piggybank_id')
                    ->setType('unique')
            )
            ->addIndex(
                $this->index('piggybank_id')
                    ->setName('idx_transactions_piggybank_id')
            )
            ->addIndex(
                $this->index('transaction_id')
                    ->setName('idx_transactions_transaction_id')
            )
            ->addForeignKey(
                $this->foreignKey('transaction_id')
                    ->setReferencedTable('transactions')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_transaction_id')
            )
            ->addForeignKey(
                $this->foreignKey('piggybank_id')
                    ->setReferencedTable('piggybank')
                    ->setReferencedColumns('id')
                    ->setDelete('RESTRICT')
                    ->setUpdate('NO_ACTION')
                    ->setName('fk_transactions_piggybank_id')
            )
            ->create();

    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        $this->table('account')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'accounttype_id'
            )->save();

        $this->table('budget')
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'category_id'
            )->save();

        $this->table('categories')
            ->dropForeignKey(
                'parent_id'
            )->save();

        $this->table('subscription')
            ->dropForeignKey(
                'category_id'
            )
            ->dropForeignKey(
                'account_id'
            )
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'paymentmethod_id'
            )->save();

        $this->table('transactions')
            ->dropForeignKey(
                'category_id'
            )
            ->dropForeignKey(
                'account_id'
            )
            ->dropForeignKey(
                'user_id'
            )
            ->dropForeignKey(
                'transfer_id'
            )
            ->dropForeignKey(
                'subscription_id'
            )->save();

        $this->table('transactions_to_piggybanks')
            ->dropForeignKey(
                'transaction_id'
            )
            ->dropForeignKey(
                'piggybank_id'
            )->save();

        $this->table('users')
            ->dropForeignKey(
                'role_id'
            )->save();

        $this->table('account')->drop()->save();
        $this->table('accounttype')->drop()->save();
        $this->table('budget')->drop()->save();
        $this->table('categories')->drop()->save();
        $this->table('payment_method')->drop()->save();
        $this->table('piggybank')->drop()->save();
        $this->table('role')->drop()->save();
        $this->table('subscription')->drop()->save();
        $this->table('transactions')->drop()->save();
        $this->table('transactions_to_piggybanks')->drop()->save();
        $this->table('users')->drop()->save();
    }
}
