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
use Cake\ORM\Association\HasMany;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Closure;
use eurokeep\Model\Entity\Accounttype;
use Override;
use Psr\SimpleCache\CacheInterface;

/**
 * Accounttype Model
 *
 * @property AccountTable&HasMany $Account
 *
 * @method Accounttype newEmptyEntity()
 * @method Accounttype newEntity(array $data, array $options = [])
 * @method array<Accounttype> newEntities(array $data, array $options = [])
 * @method Accounttype get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, Closure|string|null $cacheKey = null, mixed ...$args)
 * @method Accounttype findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method Accounttype patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method array<Accounttype> patchEntities(iterable $entities, array $data, array $options = [])
 * @method Accounttype|false save(EntityInterface $entity, array $options = [])
 * @method Accounttype saveOrFail(EntityInterface $entity, array $options = [])
 * @method iterable<Accounttype>|ResultSetInterface<Accounttype>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<Accounttype>|ResultSetInterface<Accounttype> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<Accounttype>|ResultSetInterface<Accounttype>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<Accounttype>|ResultSetInterface<Accounttype> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin TimestampBehavior
 */
class AccounttypeTable extends Table
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

        $this->setTable('accounttype');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Account', [
            'foreignKey' => 'accounttype_id',
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
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        return parent::validationDefault($validator);
    }
}
