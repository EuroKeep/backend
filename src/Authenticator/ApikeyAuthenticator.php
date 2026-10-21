<?php declare(strict_types=1);

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

namespace eurokeep\Authenticator;

use Authentication\Authenticator\AbstractAuthenticator;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Cake\ORM\TableRegistry;
use Psr\Http\Message\ServerRequestInterface;

class ApikeyAuthenticator extends AbstractAuthenticator
{

    /**
     * @param ServerRequestInterface $request
     * @return ResultInterface
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        $authorizationHeaderLine = $request->getHeaderLine('Authorization');
        if (empty($authorizationHeaderLine)) {
            return new Result(null, 'NO_AUTHORIZATION');
        }

        $User = $this->_identifier->identify($request->getHeaders());

        if (empty($User)) {
            return new Result(null, 'NOT_FOUND');
        }

        $UsersTable = TableRegistry::getTableLocator()->get('Users');
        $User = $UsersTable->get($User['id']);

        return new Result($User, 'SUCCESS');
    }
}
