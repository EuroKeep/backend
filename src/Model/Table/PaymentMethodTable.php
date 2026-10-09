<?php
declare(strict_types=1);

namespace eurokeep\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PaymentMethod Model
 *
 * @method \eurokeep\Model\Entity\PaymentMethod newEmptyEntity()
 * @method \eurokeep\Model\Entity\PaymentMethod newEntity(array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\PaymentMethod> newEntities(array $data, array $options = [])
 * @method \eurokeep\Model\Entity\PaymentMethod get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \eurokeep\Model\Entity\PaymentMethod findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \eurokeep\Model\Entity\PaymentMethod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\eurokeep\Model\Entity\PaymentMethod> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \eurokeep\Model\Entity\PaymentMethod|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \eurokeep\Model\Entity\PaymentMethod saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\PaymentMethod>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\PaymentMethod>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\PaymentMethod>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\PaymentMethod> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\PaymentMethod>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\PaymentMethod>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\eurokeep\Model\Entity\PaymentMethod>|\Cake\Datasource\ResultSetInterface<\eurokeep\Model\Entity\PaymentMethod> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \eurokeep\Model\Behavior\LogoBehavior
 */
class PaymentMethodTable extends Table
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

        $this->setTable('payment_method');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Logo', [
            'field' => 'image',
            'directory' => ROOT . DS . 'uploads' . DS . 'payment-method-logos',
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
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('image')
            ->maxLength('image', 128)
            ->allowEmptyString('image');

        return $validator;
    }
}
