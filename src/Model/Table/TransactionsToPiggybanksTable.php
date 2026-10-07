<?php
declare(strict_types=1);

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use eurokeep\Model\Entity\Transaction;
use eurokeep\Model\Entity\TransactionsToPiggybank;

/**
 * TransactionsToPiggybanks Model
 *
 * @property \eurokeep\Model\Table\PiggybankTable&\Cake\ORM\Association\BelongsTo $Piggybanks
 *
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank newEmptyEntity()
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank newEntity(array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\TransactionsToPiggybank> newEntities(array $data, array $options = [])
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\TransactionsToPiggybank> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \eurokeep\Model\Entity\TransactionsToPiggybank saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\TransactionsToPiggybank>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\TransactionsToPiggybank>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\TransactionsToPiggybank>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\TransactionsToPiggybank> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\TransactionsToPiggybank>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\TransactionsToPiggybank>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\TransactionsToPiggybank>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\TransactionsToPiggybank> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TransactionsToPiggybanksTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('transactions_to_piggybanks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Transactions', [
            'foreignKey' => 'transaction_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Piggybanks', [
            'foreignKey' => 'piggybank_id',
            'className' => 'Piggybank',
            'joinType' => 'INNER',
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
            ->decimal('balance_value')
            ->requirePresence('balance_value', 'create')
            ->notEmptyString('balance_value');

        $validator
            ->integer('transaction_id')
            ->notEmptyString('transaction_id');

        $validator
            ->integer('piggybank_id')
            ->notEmptyString('piggybank_id');

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
        $rules->add($rules->existsIn(['transaction_id'], 'Transactions'), ['errorField' => 'transaction_id']);
        $rules->add($rules->existsIn(['piggybank_id'], 'Piggybanks'), ['errorField' => 'piggybank_id']);

        return $rules;
    }

    /**
     * Overridden to automatically refresh the balance of the affected Accounts when a Transaction is created or modified.
     *
     * @param TransactionsToPiggybank $entity
     */
    #[Override]
    protected function _onSaveSuccess(EntityInterface $entity, \ArrayObject $options): bool
    {
        if (!parent::_onSaveSuccess($entity, $options)) {
            return false;
        }

        $entity->getPiggybank()->summarize();

        return true;
    }

    /**
     * Overridden to automatically refresh the balance of the affected Account when a Transaction is deleted.
     *
     * @param Transaction $entity
     */
    #[Override]
    public function delete(EntityInterface $entity, $options = []): bool
    {
        if (!parent::delete($entity, $options)) {
            return false;
        }

        $entity->getPiggybank()->summarize();

        return true;
    }
}
