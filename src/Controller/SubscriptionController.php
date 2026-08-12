<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
use Cake\Datasource\Exception\RecordNotFoundException;
use eurokeep\Model\Entity\Subscription;
use eurokeep\Model\Table\SubscriptionTable;
use JetBrains\PhpStorm\ObjectShape;

/**
 * Subscription Controller
 *
 * @property SubscriptionTable $Subscription
 * @method Subscription[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class SubscriptionController extends AuthenticatedController
{
    public array $paginate = [
        'limit' => 100,
        'maxLimit' => 100,
        'order' => [
            'Transactions.created' => 'desc',
        ],
    ];

    /**
     * Index method
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $qry = $this->Subscription->find()
            ->contain(['Categories'])->orderByAsc('Subscription.name');

        $filters = $this->getRequest()->getQueryParams()['filter'] ?? [];
        foreach ($filters as $fieldname => $value) {
            $qry->where(["$fieldname LIKE" => "$value%",]);
        }

        $subscriptions = $this
            ->paginate($qry);

        $this->set(compact('subscriptions'));
        $this->set('subscriptions', $subscriptions);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['subscriptions', 'success']);
    }

    /**
     * View method
     *
     * @param int|null $subscriptionId Subscription id.
     * @throws RecordNotFoundException When record not found.
     */
    public function view(?int $subscriptionId = null): void
    {
        $this->request->allowMethod(['get']);
        $subscription = $this->Subscription->get($subscriptionId, [
            'contain' => [],
        ]);

        $this->set('subscription', $subscription);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['subscription', 'success']);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success']);

        $data = $this->request->getData();
        $data['user_id'] = $this->user->id;
        $subscriptionTable = $this->Subscription;
        $entity = $subscriptionTable->newEmptyEntity();
        $entity = $subscriptionTable->patchEntity($entity, $data);
        $subscriptionTable->save($entity);

        if ($entity->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('error', $entity->getErrors());
            $this->viewBuilder()->setOption('serialize', ['error']);

            return;
        }
        $this->set('subscription', $entity);
        $this->viewBuilder()->setOption('serialize', ['success', 'account']);
    }

    /**
     * Edit method
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function edit($subscriptionId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->editEntity($this->Subscription, $subscriptionId, 'Subscription');
    }

    /**
     * Delete method
     *
     * @param int|null $subscriptionId Subscription id.
     */
    public function delete(?int $subscriptionId = null): void
    {
        $this->request->allowMethod(['delete']);
        $subscription = $this->Subscription->get($subscriptionId);
        if (!$this->Subscription->delete($subscription)) {
            $this->addError('Subscription not deleted');
        }
    }

    /**
     * I will forecast the transactions of the given $subscriptionId for the next twelve months ahead.
     *
     * @param int|null $subscriptionId I am the ID of the subscription you want to forecast.
     */
    public function forecast(?int $subscriptionId = null): void
    {
        $this->request->allowMethod(['get']);
        // Find subscription(s)
        if ($subscriptionId) {
            $subscriptions =
                $this
                    ->Subscription
                    ->find()
                    ->contain(['Categories', 'Account'])
                    ->where(['Subscription.id' => $subscriptionId])
                    ->all();
        } else {
            $subscriptions = $this
                ->Subscription
                ->find()
                ->contain(['Categories', 'Account'])
                ->orderByAsc('Categories.id')
                ->orderByAsc('Subscription.name')
                ->all();
        }

        // Create forecast(s)
        $end = new Chronos('Today next year');
        $forecast = [];
        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $forecast = array_merge(
                $forecast,
                $subscription->forecast($end),
            );
        }

        // Sort by execution date
        usort($forecast, fn(
            #[ObjectShape(['execution' => 'string'])]
            $a,
            #[ObjectShape(['execution' => 'string'])]
            $b,
        ) => strtotime($a->execution) <=> strtotime($b->execution));

        $this->set('forecast', $forecast);
        $this->viewBuilder()->setOption('serialize', ['success', 'forecast']);
    }
}
