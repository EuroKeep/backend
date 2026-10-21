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
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\TransactionsTable;
use JetBrains\PhpStorm\NoReturn;

/**
 * Account Controller
 *
 * @property \eurokeep\Model\Table\AccountTable $Account
 * @method \eurokeep\Controller\Account[]|\eurokeep\Controller\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StatsController extends AuthenticatedController
{
    /**
     * Index method
     *
     * @return void Renders view
     */
    #[NoReturn]
    public function index(): void
    {
        header("Content-Type: text/plain");
        $this->request->allowMethod(['get']);
        $this->accounts();
        $this->piggybanks();
        $this->budgets();
        exit(200);
    }

    /**
     * Output accounts data
     *
     * @return void
     */
    private function accounts(): void
    {
        /** @var \eurokeep\Model\Table\AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');

        $accounts = $accountTable->find('all');
        $totals = [];
        foreach ($accounts as $account) {
            $totals[$account->balance_currency] ??= 0;
            $totals[$account->balance_currency] += $account->balance_value;
            echo chr(10) . <<<TXT
# HELP eurokeep_account_{$account->id}_balance_unit_$account->balance_currency current balance of account "$account->name"
# TYPE eurokeep_account_{$account->id}_balance_unit_$account->balance_currency gauge
eurokeep_account_{$account->id}_balance_unit_$account->balance_currency {$account->balance_value}

TXT;
        }

        foreach ($totals as $currency => $total) {
            echo chr(10) . <<<TXT
# HELP eurokeep_total_balance_unit_$currency total balance of assets held in currency $currency
# TYPE eurokeep_total_balance_unit_$currency gauge
eurokeep_total_balance_unit_$currency {$total}

TXT;
        }
    }

    /**
     * Output Budgets data
     *
     * @return void
     */
    private function budgets(): void
    {
        $start ??= Chronos::now()->startOfMonth();
        $end ??= Chronos::now()->endOfMonth();
        $now = Chronos::now();
        /** @var \eurokeep\Model\Table\BudgetTable $BudgetTable */
        $BudgetTable = TableRegistry::getTableLocator()->get('Budget');
        $Budgets = $BudgetTable->find('all');

        /** @var \eurokeep\Model\Entity\Budget $budget */
        foreach ($Budgets as $budget) {
            $transactions = $budget->getTransactions($start, $end);
            $spentValue = TransactionsTable::summarize($transactions);

            if ($budget->limit <= 0) {
                // Cannot
                continue;
            }
            $spentPercentage = -$spentValue / $budget->limit * 100;

            $freeValue = $budget->limit + $spentValue;
            $freePercent = $freeValue / $budget->limit * 100;

            $daysLeft = $now->diffInDays($end);
            $budgetPerDay = $freeValue / max(1, $daysLeft);
            echo chr(10) . <<<TXT
# HELP eurokeep_budget_{$budget->id}_limit_value Budget {$budget->name} (#{$budget->id}): Limit
# TYPE eurokeep_budget_{$budget->id}_limit_value gauge
eurokeep_budget_{$budget->id}_limit_value {$budget->limit}
# HELP eurokeep_budget_{$budget->id}_spent_value Budget {$budget->name} (#{$budget->id}): Spent Value
# TYPE eurokeep_budget_{$budget->id}_spent_value gauge
eurokeep_budget_{$budget->id}_spent_value {$spentValue}
# HELP eurokeep_budget_{$budget->id}_spent_percentage Budget {$budget->name} (#{$budget->id}): Spent Percentage
# TYPE eurokeep_budget_{$budget->id}_spent_percentage gauge
eurokeep_budget_{$budget->id}_spent_percentage {$spentPercentage}
# HELP eurokeep_budget_{$budget->id}_free_value Budget {$budget->name} (#{$budget->id}): Free Value
# TYPE eurokeep_budget_{$budget->id}_free_value gauge
eurokeep_budget_{$budget->id}_free_value {$freeValue}
# HELP eurokeep_budget_{$budget->id}_free_percentage Budget {$budget->name} (#{$budget->id}): Free Percentage
# TYPE eurokeep_budget_{$budget->id}_free_percentage gauge
eurokeep_budget_{$budget->id}_free_percentage {$freePercent}
# HELP eurokeep_budget_{$budget->id}_daily Budget {$budget->name} (#{$budget->id}): Budget per Day until end
# TYPE eurokeep_budget_{$budget->id}_daily gauge
eurokeep_budget_{$budget->id}_daily {$budgetPerDay}

TXT;
        }
    }

    /**
     * Output Piggybank Data
     *
     * @return void
     */
    private function piggybanks(): void
    {
        /** @var \eurokeep\Model\Table\PiggybankTable $PIggybankTable */
        $PiggybankTable = TableRegistry::getTableLocator()->get('Piggybank');

        $Piggybanks = $PiggybankTable->find('all');

        /** @var \eurokeep\Model\Entity\Piggybank $piggyBank */
        foreach ($Piggybanks as $piggyBank) {
            $percent = $piggyBank->balance_value / $piggyBank->target * 100;
            echo chr(10) . <<<TXT
# HELP eurokeep_piggybank_{$piggyBank->id}_target_percentage percentage of the piggybank {$piggyBank->id} ('{$piggyBank->name}')
# TYPE eurokeep_piggybank_{$piggyBank->id}_target_percentage gauge
eurokeep_piggybank_{$piggyBank->id}_target_percentage {$percent}
# HELP eurokeep_piggybank_{$piggyBank->id}_target_value target value of the piggybank {$piggyBank->id} ('{$piggyBank->name}')
# TYPE eurokeep_piggybank_{$piggyBank->id}_target_value gauge
eurokeep_piggybank_{$piggyBank->id}_target_value {$piggyBank->target}
# HELP eurokeep_piggybank_{$piggyBank->id}_target currently saved value of the piggybank {$piggyBank->id} ('{$piggyBank->name}')
# TYPE eurokeep_piggybank_{$piggyBank->id}_target gauge
eurokeep_piggybank_{$piggyBank->id}_target {$piggyBank->target}

TXT;
        }
    }
}
