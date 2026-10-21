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
use eurokeep\Model\Entity\Role;
use Override;
use Psr\SimpleCache\CacheInterface;

/**
 * Role Model
 *
 * @property UsersTable&HasMany $Users
 *
 * @method Role newEmptyEntity()
 * @method Role newEntity(array $data, array $options = [])
 * @method array<Role> newEntities(array $data, array $options = [])
 * @method Role get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, Closure|string|null $cacheKey = null, mixed ...$args)
 * @method Role findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method Role patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method array<Role> patchEntities(iterable $entities, array $data, array $options = [])
 * @method Role|false save(EntityInterface $entity, array $options = [])
 * @method Role saveOrFail(EntityInterface $entity, array $options = [])
 * @method iterable<Role>|ResultSetInterface<Role>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<Role>|ResultSetInterface<Role> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<Role>|ResultSetInterface<Role>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<Role>|ResultSetInterface<Role> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin TimestampBehavior
 */
class RoleTable extends Table
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

        $this->setTable('role');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Users', [
            'foreignKey' => 'role_id',
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
