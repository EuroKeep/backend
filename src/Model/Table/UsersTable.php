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
use eurokeep\Model\Entity\User;
use Override;

/**
 * User Model
 *
 * @property AccountTable&HasMany $Account
 * @property TransactionsTable&HasMany $Transaction
 *
 * @method User newEmptyEntity()
 * @method User newEntity(array $data, array $options = [])
 * @method User[] newEntities(array $data, array $options = [])
 * @method User get($primaryKey, $options = [])
 * @method User findOrCreate($search, ?callable $callback = null, $options = [])
 * @method User patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method User[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method User|false save(EntityInterface $entity, $options = [])
 * @method User saveOrFail(EntityInterface $entity, $options = [])
 * @method User[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method User[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Role', [
            'foreignKey' => 'role_id',
        ]);
        $this->hasMany('Account', [
            'foreignKey' => 'user_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);

        $this->getSchema()->setColumnType('settings', 'json');
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

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('password')
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        $validator
            ->scalar('api_key')
            ->allowEmptyString('api_key');

        $validator
            ->numeric('role_id')
            ->requirePresence('role_id', 'role_id')
            ->notEmptyString('role_id');

        return parent::validationDefault($validator);
    }

    /**
     * I will simply create a new user.
     * @param string $email The Email to use.
     * @param string $name The name of the user.
     * @param string $password The password to set.
     * @return User Instance of the newly created User entity.
     */
    public function create(string $email, string $name, string $password): User
    {
        $user = $this->newEmptyEntity();

        $user->email = $email;
        $user->name = $name;
        $user->password = $password;

        $this->save($user);

        return $user;
    }

    /**
     * I will set the password of the given $User.
     * @param User $user Which user?
     * @param string $password Which password?
     */
    public function setPassword(User $user, string $password): void
    {
        // VERIFY COMPLEXITY
        $user->password = $password;
        $user->flags = 3; //Make this the binary operation!
        $this->save($user);
    }

    /**
     * I will set the apikey of the given $User.
     * @param User $user Which user?
     * @param string $apiKey ApiKey of the user.
     */
    public function setApiKey(User $user, string $apiKey): void
    {
        // VERIFY COMPLEXITY
        $user->api_key = $apiKey;
        $user->flags = 3; //Make this the binary operation!
        $this->save($user);
    }
}
