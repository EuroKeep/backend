<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
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
            ->contain(['Categories', 'PaymentMethod'])
            ->orderByAsc('Subscription.name');

        $filters = $this->getRequest()->getQueryParams()['filter'] ?? [];
        foreach ($filters as $fieldname => $value) {
            $qry->where(["$fieldname LIKE" => "$value%",]);
        }

        $subscriptions = $this
            ->paginate($qry);

        $this->set(compact('subscriptions'));
        $this->set('items', $subscriptions);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    /**
     * View method
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function view($subscriptionId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Subscription, $subscriptionId, 'Subscription', ['contain' => []]);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->Subscription, ['user_id' => $this->user->id]);
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
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Subscription, $subscriptionId, 'Subscription');
    }

    /**
     * Delete method
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function delete($subscriptionId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Subscription, $subscriptionId, 'Subscription');
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
