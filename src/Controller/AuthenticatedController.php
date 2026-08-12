<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace eurokeep\Controller;

use Authentication\AuthenticationService;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Entity\User;
use eurokeep\Model\Table\OwnedTable;
use eurokeep\Model\Table\UsersTable;
use JetBrains\PhpStorm\NoReturn;
use Override;
use Throwable;

/**
 * I am an arbitrary Controller that makes sure a logged +in user is found.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 * @property AuthenticationService $Authentication
 */
class AuthenticatedController extends AppController
{
    /**
     * @var User I am the authenticated User
     */
    protected User $user;

    /**
     * @inheritDoc
     */
    #[Override]
    public function initialize(): void
    {
        parent::initialize();

        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            // Force-Load the user from the Database to verify that the entry is still there.
            try {
                /** @var UsersTable $usersTable */
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $this->user = $usersTable->get($result->getData()->id);

                OwnedTable::setCurrentUserId($this->user->id);
            } catch (Throwable) {
                $this->http401();
            }
        } else {
            $this->http401();
        }
    }

    /**
     * I am the overall method to return HTTP403
     */
    #[NoReturn]
    final protected function http403(string $message = ''): void
    {
        header('HTTP/1.1 403 Forbidden');
        if ($message !== '') {
            $this->addError('HTTP', $message);
        }
        exit(403);
    }

    /**
     * I am the overall method to return HTTP401
     */
    #[NoReturn]
    final protected function http401(string $message = ''): void
    {
        header('HTTP/1.1 401 Unauthorized');
        if ($message !== '') {
            $this->addError('HTTP', $message);
        }
        exit(401);
    }

    /**
     * I am the generic "edit" JSON API action shared by all CRUD controllers.
     *
     * I validate the identifier and the request body, load the entity, patch and validate it,
     * then save it. Missing parameters, missing entities and invalid JSON input are reported
     * as normal JSON responses (400/404) instead of uncaught exceptions.
     *
     * @param \Cake\ORM\Table $table The Table to load/patch/save the entity through.
     * @param mixed $id The (route) identifier of the entity. Deliberately not type-hinted at
     *   the call site (the action's own parameter) so that a missing or non-numeric value can
     *   be reported as a 400 instead of an ArgumentCountError/TypeError.
     * @param string $resourceName Human-readable name used in the "not found"/"missing id"
     *   error messages, e.g. 'Account'.
     * @param array $getOptions Options passed through to Table::get(), e.g. ['contain' => []].
     */
    final protected function editEntity(Table $table, mixed $id, string $resourceName, array $getOptions = []): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$id)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('error', ["Missing or invalid $resourceName id"]);

            return;
        }

        // Malformed JSON / non-object body (BodyParserMiddleware returns null on invalid JSON)
        $data = $this->request->getData();
        if (!is_array($data)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('error', ['Invalid JSON body']);

            return;
        }

        try {
            $item = $table->get((int)$id, $getOptions);
        } catch (RecordNotFoundException) {
            $this->response = $this->response->withStatus(404);
            $this->set('item');
            $this->set('success', false);
            $this->set('error', ["$resourceName not found"]);

            return;
        }

        // Apply the new data
        $item = $table->patchEntity($item, $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('error', $item->getErrors());

            return;
        }

        // Cannot save?
        if (!$table->save($item)) {
            $this->response = $this->response->withStatus(500);
            $this->set('item');
            $this->set('success', false);
            $this->set('error', ['Internal error']);

            return;
        }

        $this->set('item', $item);
        $this->set('success', true);
    }
}
