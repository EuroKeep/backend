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

namespace eurokeep\Model\Enum;

/**
 * Node types of the Sankey diagram, see AnalyzerController::sankey(). For the virtual nodes
 * (Total, Surplus, Deficit) the node id equals the value; category nodes use `<value>-<categoryId>`.
 */
enum SankeyNodeType: string
{
    case Income = 'income';
    case Expense = 'expense';
    case Total = 'total';
    case Surplus = 'surplus';
    case Deficit = 'deficit';
}
