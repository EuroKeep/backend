<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Subscriptions extends AbstractMigration
{


    /**
     * Whether the tables created in this migration
     * should auto-create an `id` field or not
     *
     * This option is global for all tables created in the migration file.
     * If you set it to false, you have to manually add the primary keys for your
     * tables using the Migrations\Table::addPrimaryKey() method
     *
     * @var bool
     */
    public bool $autoId = false;


    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        // Subscriptions
        if (!$this->hasTable('subscription')) {
            $this->table('subscription')
                ->addColumn('id', 'integer', [
                    'autoIncrement' => true,
                    'default' => null,
                    'limit' => 10,
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
                    'default' => 0,
                    'limit' => 6,
                    'null' => false,
                ])
                ->addColumn('name', 'string', [
                    'default' => null,
                    'limit' => 128,
                    'null' => false,
                ])
                ->addColumn('account_id', 'integer', [
                    'default' => 1,
                    'limit' => 10,
                    'null' => false,
                ])
                ->addColumn('balance_value', 'decimal', [
                    'default' => null,
                    'precision' => 15,
                    'scale' => 2,
                    'null'    => false,
                ])
                ->addColumn('interval', 'string', [
                    'default' => null,
                    'limit' => 128,
                    'null' => true,
                ])
                ->addColumn('start', 'date', [
                    'limit' => null,
                    'null' => true,
                ])
                ->addColumn('end', 'date', [
                    'limit' => null,
                    'null' => true,
                ])
                ->addColumn('user_id', 'integer', [
                    'default' => 1,
                    'limit' => 10,
                    'null' => false,
                ])
                ->addColumn('category_id', 'integer', [
                    'default' => 1,
                    'limit' => 10,
                    'null' => false,
                ])
                ->create();
        }

    }
}
