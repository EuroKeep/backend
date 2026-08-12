<?php
declare(strict_types=1);

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\Validation\Validator;
use eurokeep\Model\Entity\Subscription;
use Override;

/**
 * Subscription Model
 *
 * @method Subscription newEmptyEntity()
 * @method Subscription newEntity(array $data, array $options = [])
 * @method Subscription[] newEntities(array $data, array $options = [])
 * @method Subscription get($primaryKey, $options = [])
 * @method Subscription findOrCreate($search, ?callable $callback = null, $options = [])
 * @method Subscription patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method Subscription[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method Subscription|false save(EntityInterface $entity, $options = [])
 * @method Subscription saveOrFail(EntityInterface $entity, $options = [])
 * @method Subscription[]|ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method Subscription[]|ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin TimestampBehavior
 */
class SubscriptionTable extends OwnedTable
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

        $this->setTable('subscription');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Categories', [
            'foreignKey' => 'category_id',
        ]);

        $this->belongsTo('Account', [
            'foreignKey' => 'account_id',
        ]);

        $this->addBehavior('Timestamp');
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
            ->integer('account_id')
            ->requirePresence('account_id', 'create')
            ->notEmptyString('account_id');

        $validator
            ->numeric('balance_value')
            ->requirePresence('balance_value', 'create')
            ->notEmptyString('balance_value');

        $validator
            ->integer('category_id')
            ->allowEmptyString('category_id');

        $validator
            ->scalar('interval')
            ->maxLength('interval', 128)
            ->allowEmptyString('interval');

        $validator
            ->date('start')
            ->allowEmptyDate('start');

        $validator
            ->date('end')
            ->allowEmptyDate('end');

        $validator
            ->scalar('term')
            ->maxLength('term', 128)
            ->allowEmptyString('term');

        $validator
            ->scalar('noticeperiod')
            ->maxLength('noticeperiod', 128)
            ->allowEmptyString('noticeperiod');

        $validator
            ->scalar('comments')
            ->maxLength('comments', 512)
            ->allowEmptyString('comments');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        return parent::validationDefault($validator);
    }
}
