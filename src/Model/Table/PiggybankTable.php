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

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Validation\Validator;
use eurokeep\Model\Entity\Piggybank;
use Override;

/**
 * Piggybank Model
 *
 * @method Piggybank newEmptyEntity()
 * @method Piggybank newEntity(array $data, array $options = [])
 * @method Piggybank[] newEntities(array $data, array $options = [])
 * @method Piggybank get($primaryKey, $options = [])
 * @method Piggybank findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Piggybank patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Piggybank[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Piggybank|false save(EntityInterface $entity, $options = [])
 * @method Piggybank saveOrFail(EntityInterface $entity, $options = [])
 * @method Piggybank[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Piggybank[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Piggybank[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Piggybank[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class PiggybankTable extends OwnedTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     */
    #[Override]
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('piggybank');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('TransactionsToPiggybanks', [
            'foreignKey' => 'piggybank_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     */
    #[Override]
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('flags')
            ->notEmptyString('flags');

        $validator
            ->integer('account_id')
            ->requirePresence('account_id', 'create')
            ->notEmptyString('account_id');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->decimal('balance_value')
            ->requirePresence('balance_value', 'create')
            ->notEmptyString('balance_value');

        $validator
            ->scalar('balance_currency')
            ->maxLength('balance_currency', 7)
            ->requirePresence('balance_currency', 'create')
            ->notEmptyString('balance_currency');

        $validator
            ->date('start')
            ->allowEmptyDate('start');

        $validator
            ->date('end')
            ->allowEmptyDate('end');

        $validator
            ->numeric('target')
            ->requirePresence('target', 'create')
            ->notEmptyString('target');

        $validator
            ->scalar('description')
            ->maxLength('description', 512)
            ->allowEmptyString('description');
        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        return parent::validationDefault($validator);
    }
}
