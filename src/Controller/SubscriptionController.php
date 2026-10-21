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

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
use Cake\Http\Response;
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
     * I will store the uploaded image (multipart field `logo`) as the logo of the given subscription.
     * A previously uploaded logo is replaced. Only images are accepted.
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted, see view().
     */
    public function uploadLogo($subscriptionId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->uploadLogoEntity($this->Subscription, $subscriptionId, 'Subscription');
    }

    /**
     * I will remove the logo of the given subscription (and delete its file, if it was uploaded).
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted, see view().
     */
    public function deleteLogo($subscriptionId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteLogoEntity($this->Subscription, $subscriptionId, 'Subscription');
    }

    /**
     * I will deliver the uploaded logo of the given subscription.
     *
     * @param mixed $subscriptionId Identifier of the subscription. Not type-hinted, see view().
     */
    public function logo($subscriptionId = null): Response
    {
        $this->request->allowMethod(['get']);

        return $this->logoEntityResponse($this->Subscription, $subscriptionId);
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
        $end = new Chronos('last day of next month')->endOfDay();
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

    /**
     * I will summarize the costs of all active subscriptions (no end date or end date not yet passed).
     * Subscriptions whose interval cannot be parsed are counted, but excluded from all cost figures.
     */
    public function statistics(): void
    {
        $this->request->allowMethod(['get']);

        $subscriptions = $this->Subscription->find()
            ->contain(['Categories'])
            ->where(['OR' => [
                'Subscription.end IS' => null,
                'Subscription.end >=' => Chronos::today()->toDateString(),
            ]])
            ->all();

        $annualTotal = 0.0;
        $priced = 0;
        $mostExpensive = null;
        $cheapest = null;
        $mostExpensiveCost = null;
        $cheapestCost = null;

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $annualCost = $subscription->getAnnualCost();
            if ($annualCost === null) {
                continue;
            }

            $annualTotal += $annualCost;
            $priced++;

            // Compare by absolute amount, so it does not matter whether costs are stored as negative values.
            if ($mostExpensiveCost === null || abs($annualCost) > abs($mostExpensiveCost)) {
                $mostExpensive = $subscription;
                $mostExpensiveCost = $annualCost;
            }
            if ($cheapestCost === null || abs($annualCost) < abs($cheapestCost)) {
                $cheapest = $subscription;
                $cheapestCost = $annualCost;
            }
        }

        $monthlyTotal = $annualTotal / 12;

        $statistics = [
            'count' => $subscriptions->count(),
            'count_without_interval' => $subscriptions->count() - $priced,
            'monthly_total' => round($monthlyTotal, 2),
            'annual_total' => round($annualTotal, 2),
            'daily_total' => round($annualTotal / 365, 2),
            'monthly_average' => $priced > 0 ? round($monthlyTotal / $priced, 2) : 0.0,
            'most_expensive' => $mostExpensive === null ? null : [
                'subscription' => $mostExpensive,
                'monthly_cost' => round($mostExpensiveCost / 12, 2),
            ],
            'cheapest' => $cheapest === null ? null : [
                'subscription' => $cheapest,
                'monthly_cost' => round($cheapestCost / 12, 2),
            ],
        ];

        $this->set('statistics', $statistics);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['statistics', 'success']);
    }
}
