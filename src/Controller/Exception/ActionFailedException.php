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

namespace eurokeep\Controller\Exception;

use Exception;

/**
 * I abort a controller action. AppController::invokeAction() turns me into the regular JSON error response.
 *
 * I deliberately extend Exception directly (not RuntimeException etc.), so that the specific
 * catch blocks inside the actions do not swallow me.
 */
final class ActionFailedException extends Exception
{
    /**
     * @param int $statusCode I am the HTTP StatusCode for the error response.
     * @param array<array-key, mixed> $messages I am the list of error messages sent in the response.
     */
    public function __construct(public readonly int $statusCode, public readonly array $messages)
    {
        parent::__construct((string)json_encode($messages), $statusCode);
    }
}
