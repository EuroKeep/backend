<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Authentication\Controller\Component\AuthenticationComponent;
use JetBrains\PhpStorm\NoReturn;
use Override;

/**
 * Auth Controller.
 *
 * I handle logging in and out.
 *
 * @property AuthenticationComponent $Authentication
 *
 */
class AuthController extends AppController
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function initialize(): void
    {
        parent::initialize();

        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * I handle the login.
     */
    public function login(): void
    {
        $this->request->allowMethod(['post']);
        $result = $this->Authentication->getResult();

        $errors = [];
        if ($result && $result->isValid()) {
            $foundUser = $result->getData();
            $this->set('success', true);
            $this->set('user', $foundUser);
        } else {
            $this->set('success', false);
            $errors[] = 'User authentication failed';
        }

        $this->set('errors', $errors);
        $this->viewBuilder()->setOption('serialize', ['user', 'success', 'errors']);
    }

    /**
     * I handle the logout.
     */
    #[NoReturn]
    public function logout(): void
    {
        $this->Authentication->logout();
        header('Location: /a/login');
        exit(302);
    }
}
