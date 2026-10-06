<?php declare(strict_types=1);

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
