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
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Closure;
use eurokeep\Model\Behavior\UserOwnedBehavior;
use eurokeep\Model\Entity\Account;
use Override;
use Psr\SimpleCache\CacheInterface;

/**
 * Account Model
 *
 * @property UsersTable&BelongsTo $Users
 * @property TransactionsTable&HasMany $Transaction
 *
 * @method Account newEmptyEntity()
 * @method Account newEntity(array $data, array $options = [])
 * @method array<Account> newEntities(array $data, array $options = [])
 * @method Account get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, Closure|string|null $cacheKey = null, mixed ...$args)
 * @method Account findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method Account patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method array<Account> patchEntities(iterable $entities, array $data, array $options = [])
 * @method Account|false save(EntityInterface $entity, array $options = [])
 * @method Account saveOrFail(EntityInterface $entity, array $options = [])
 * @method iterable<Account>|ResultSetInterface<Account>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<Account>|ResultSetInterface<Account> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<Account>|ResultSetInterface<Account>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<Account>|ResultSetInterface<Account> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin TimestampBehavior
 */
class AccountTable extends OwnedTable
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

        $this->setTable('account');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        /** @see UserOwnedBehavior */
        $this->addBehavior('Timestamp');

        $this->belongsTo('Accounttype', [
            'foreignKey' => 'accounttype_id',
        ]);

        $this->hasMany('Transactions', [
            'foreignKey' => 'account_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
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
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('emoji')
            ->maxLength('emoji', 8)
            ->requirePresence('emoji', 'create')
            ->notEmptyString('emoji');

        $validator
            ->scalar('iban')
            ->maxLength('iban', 50)
            ->allowEmptyString('iban');

        $validator
            ->decimal('balance_value')
            ->requirePresence('balance_value', 'create')
            ->notEmptyString('balance_value');

        $validator
            ->scalar('balance_currency')
            ->maxLength('balance_currency', 100)
            ->requirePresence('balance_currency', 'create')
            ->notEmptyString('balance_currency');

        $validator
            ->integer('accounttype_id')
            ->requirePresence('accounttype_id', 'create')
            ->notEmptyString('accounttype_id');

        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        return parent::validationDefault($validator);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     */
    #[Override]
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'), ['errorField' => 'user_id']);

        return parent::buildRules($rules);
    }
}
