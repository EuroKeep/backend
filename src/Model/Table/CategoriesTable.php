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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use eurokeep\Model\Entity\Category;
use InvalidArgumentException;

/**
 * Categories Model
 *
 * @property \eurokeep\Model\Table\BudgetTable&\Cake\ORM\Association\HasMany $Budget
 * @property \eurokeep\Model\Table\TransactionsTable&\Cake\ORM\Association\HasMany $Transaction
 * @property \eurokeep\Model\Table\SubscriptionTable&\Cake\ORM\Association\HasMany $Subscription
 *
 * @method \eurokeep\Model\Entity\Category newEmptyEntity()
 * @method \eurokeep\Model\Entity\Category newEntity(array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\Category> newEntities(array $data, array $options = [])
 * @method \eurokeep\Model\Entity\Category get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \eurokeep\Model\Entity\Category findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \eurokeep\Model\Entity\Category patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\Category> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \eurokeep\Model\Entity\Category|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \eurokeep\Model\Entity\Category saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\Category>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\Category>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\Category>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\Category> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\Category>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\Category>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\Category>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\Category> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \Cake\ORM\Behavior\TreeBehavior
 */
class CategoriesTable extends Table
{
    public const int FLAG_ACTIVE = 1;
    public const int FLAG_INCOME_ROOT = 4;
    public const int FLAG_EXPENSE_ROOT = 8;
    public const int FLAG_TRANSFER_ROOT = 16;
    public const int FLAGS_ROOT_TYPE = self::FLAG_INCOME_ROOT | self::FLAG_EXPENSE_ROOT | self::FLAG_TRANSFER_ROOT;

    public const int ID_EXPENSE_ROOT = 64;
    public const int ID_INCOME_ROOT = 65;
    public const int ID_TRANSFER_ROOT = 76;

    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree', [
            'recoverOrder' => ['lft' => 'ASC'],
        ]);

        $this->belongsTo('ParentCategories', [
            'className' => 'Categories',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('Budget', [
            'foreignKey' => 'category_id',
        ]);
        $this->hasMany('ChildCategories', [
            'className' => 'Categories',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('Transactions', [
            'foreignKey' => 'category_id',
        ]);
        $this->hasMany('Subscription', [
            'foreignKey' => 'category_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('flags')
            ->notEmptyString('flags');

        $validator
            ->scalar('emoji')
            ->maxLength('emoji', 16)
            ->requirePresence('emoji', 'create')
            ->notEmptyString('emoji');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('parent_id')
            ->allowEmptyString('parent_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['parent_id'], 'ParentCategories'), ['errorField' => 'parent_id']);

        return $rules;
    }

    public function getRootCategories(): array
    {
        return $this->find()->where([
            'parent_id IS NULL',
        ])->toArray();
    }

    /**
     * I will return THE root Category carrying the given root-type bit.
     * @param int $flag One of FLAG_INCOME_ROOT, FLAG_EXPENSE_ROOT, FLAG_TRANSFER_ROOT.
     * @return Category The root Category.
     * @throws RecordNotFoundException When no Category carries the bit (broken master data).
     */
    public function getRootByFlag(int $flag): Category
    {
        if (!in_array($flag, [self::FLAG_INCOME_ROOT, self::FLAG_EXPENSE_ROOT, self::FLAG_TRANSFER_ROOT], true)) {
            throw new InvalidArgumentException("$flag is no root-type flag");
        }

        /** @var Category */
        return $this->find()
            ->where(['parent_id IS' => null])
            ->where(["(flags & $flag) <> 0"])
            ->firstOrFail();
    }
}
