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
        $this->Authentication->allowUnauthenticated(['csrf']);
    }

    public function csrf()
    {
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

            // Put default settings to the user
            if (empty($foundUser->settings['SUMMARY_ACCOUNTS'])) {
                $foundUser->settings['SUMMARY_ACCOUNTS'] = [];
            }

            // Save that help is displayed if not disabled by the user.
            if (empty($foundUser->settings['helpDisplay'])) {
                $foundUser->settings['helpDisplay'] = true;
            }
            $this->set('success', true);
            $this->set('user', $foundUser);
        } else {
            $this->fail(400, 'User authentication failed');
            return;
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
        $this->request->allowMethod(['post']);
        $this->Authentication->logout();
    }
}
