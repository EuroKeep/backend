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
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Closure;
use eurokeep\Model\Entity\Budget;
use Override;
use Psr\SimpleCache\CacheInterface;

/**
 * Budget Model
 *
 * @method Budget newEmptyEntity()
 * @method Budget newEntity(array $data, array $options = [])
 * @method array<Budget> newEntities(array $data, array $options = [])
 * @method Budget get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, Closure|string|null $cacheKey = null, mixed ...$args)
 * @method Budget findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method Budget patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method array<Budget> patchEntities(iterable $entities, array $data, array $options = [])
 * @method Budget|false save(EntityInterface $entity, array $options = [])
 * @method Budget saveOrFail(EntityInterface $entity, array $options = [])
 * @method iterable<Budget>|ResultSetInterface<Budget>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<Budget>|ResultSetInterface<Budget> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<Budget>|ResultSetInterface<Budget>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<Budget>|ResultSetInterface<Budget> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin TimestampBehavior
 */
class BudgetTable extends OwnedTable
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     */
    #[Override]
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('budget');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
        ]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('flags')
            ->notEmptyString('flags');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->numeric('limit')
            ->notEmptyString('limit');

        $validator
            ->integer('category_id')
            ->requirePresence('category_id', 'create')
            ->notEmptyString('category_id');

        $validator
            ->nonNegativeInteger('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('currency')
            ->maxLength('currency', 3)
            ->notEmptyString('currency');

        return parent::validationDefault($validator);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return parent::buildRules($rules);
    }
}
