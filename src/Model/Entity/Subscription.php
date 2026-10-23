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

namespace eurokeep\Model\Entity;

use Cake\Chronos\Chronos;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use DateInterval;


/**
 * Subscription Entity
 *
 * @property int $id
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property int $flags
 * @property string $name
 * @property string|null $logo
 * @property int $account_id
 * @property float $balance_value
 * @property string|null $subscription_group_id
 * @property string|null $interval
 * @property FrozenDate|null $start
 * @property FrozenDate|null $end
 * @property string|null $term
 * @property string|null $noticeperiod
 * @property string|null $comments
 * @property int $user_id
 * @property string $execution
 */
class Subscription extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'created' => true,
        'modified' => true,
        'flags' => true,
        'name' => true,
        'account_id' => true,
        'balance_value' => true,
        'category_id' => true,
        'paymentmethod_id' => true,
        'interval' => true,
        'start' => true,
        'end' => true,
        'term' => true,
        'noticeperiod' => true,
        'description' => true,
        'comments' => true,
        'user_id' => true,
    ];

    /**
     * I will parse the given $interval from the database
     * @return DateInterval|null I am the DateInterval Object that represents the actual Interval. If not possible, I am null.
     */
    public function getInterval(): DateInterval|null
    {
        if (!preg_match('/^(\d+)([MYD])$/', $this->interval, $matches)) {
            return null;
        }

        $value = (int)$matches[1];
        $unit = $matches[2];
        return match ($unit) {
            'M' => new DateInterval("P{$value}M"),
            'Y' => new DateInterval("P{$value}Y"),
            'D' => new DateInterval("P{$value}D"),
        };
    }

    /**
     * I will extrapolate the amount per execution to a full year.
     * Uses the same factors as the frontend (D = 365, W = 52, M = 12, Y = 1 per year).
     * @return float|null I am the annual cost, or null if the interval cannot be parsed.
     */
    public function getAnnualCost(): ?float
    {
        if (!preg_match('/^(\d+)([DWMY])$/', (string)$this->interval, $matches) || (int)$matches[1] === 0) {
            return null;
        }

        $perYear = ['D' => 365, 'W' => 52, 'M' => 12, 'Y' => 1][$matches[2]] / (int)$matches[1];

        return (float)$this->balance_value * $perYear;
    }

    /**
     * I will validate whether the Subscription can be used for forecasting transactions.
     */
    public function isEligibleForForecast(): bool
    {
        if (empty($this->interval) || empty($this->start)) {
            return false;
        }

        return true;
    }

    /**
     * I will look into the time range between $current and $end and determine, which occasions shall run the Subscription.
     * @param Chronos $end I am the end of the time range.
     */
    public function forecast(Chronos $end): array
    {
        // Sorry, your subscription cannot be forecasted.
        if (!$this->isEligibleForForecast()) {
            return [];
        }

        // If the subscription ends before $end, use that instead to not show obsolete transactions.
        if ($this->end && $this->end->getTimestamp() < $end->getTimestamp()) {
            $end = new Chronos($this->end->getTimestamp());
        }

        // Begin the interval calculation on the start of the Subsciption.
        $current = new Chronos($this->start->getTimestamp());

        $interval = $this->getInterval();

        $now = new Chronos('today')->startOfDay();
        $results = [];
        while ($current < $end) {
            // Only show future transactions.
            if ($current >= $now) {
                $myInterval = clone $this;
                $myInterval->execution = $current->format('Y-m-' . $this->start->format('d'));
                $results[] = $myInterval;
            }
            $current = $current->add($interval);
        }

        return $results;
    }
}
