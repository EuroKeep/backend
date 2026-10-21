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

use Authentication\Controller\Component\AuthenticationComponent;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\UsersTable;
use JetBrains\PhpStorm\NoReturn;
use Throwable;
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

        $this->Authentication->allowUnauthenticated(['login', 'init', 'register', 'logout']);
    }

    public function init()
    {
        $user = null;
        $isLoggedIn = false;

        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            try {
                /** @var UsersTable $usersTable */
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $user = $usersTable->get($result->getData()->id);
                $isLoggedIn = true;
            } catch (Throwable) {
                // Ignored here: If user is not found or inactive, this is only the init request for the login.
            }
        }

        $this->set('user', $user);
        $this->set('is_logged_in', $isLoggedIn);
        $this->set('enable_registration', filter_var(env('ENABLE_REGISTRATION', false), FILTER_VALIDATE_BOOLEAN));
        $this->viewBuilder()->setOption('serialize', ['user', 'success', 'errors', 'enable_registration', 'is_logged_in']);
    }

    public function register()
    {
        if (!filter_var(env('ENABLE_REGISTRATION', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->fail(400, 'Registration disabled. Check your .env file.');
        }
        $this->request->allowMethod(['put']);

        $username = trim((string)$this->request->getData()['username']);
        $password = $this->request->getData()['password'];

        /** @var UsersTable $usersTable */
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->create($username, $username, $password);

        $this->set('user', $user);
        $this->set('success', true);
        $this->set('errors', []);
        $this->viewBuilder()->setOption('serialize', ['user', 'success', 'errors']);
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
