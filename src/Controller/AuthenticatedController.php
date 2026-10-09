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
use Cake\Database\Exception\QueryException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Behavior\LogoBehavior;
use eurokeep\Model\Entity\User;
use eurokeep\Model\Table\OwnedTable;
use eurokeep\Model\Table\UsersTable;
use InvalidArgumentException;
use JetBrains\PhpStorm\NoReturn;
use Override;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
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
     * @param array $readonlyFields Simple array of fields that will not be updated.
     */
    final protected function editEntity(Table $table, mixed $id, string $resourceName, array $getOptions = [], array $readonlyFields = []): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$id)) {
            $this->fail(400, "Missing or invalid $resourceName id");
        }

        // Malformed JSON / non-object body (BodyParserMiddleware returns null on invalid JSON)
        $data = $this->request->getData();
        if (!is_array($data)) {
            $this->fail(400, 'Invalid JSON body');
        }

        try {
            $item = $table->get((int)$id, $getOptions);
        } catch (RecordNotFoundException) {
            $this->fail(404, "$resourceName not found");
        }

        // Forbid write-access to distinct fields of the entity.
        foreach ($readonlyFields as $field) {
            $item->setAccess($field, false);
        }

        // Apply the new data
        $item = $table->patchEntity($item, $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->failMany(400, $item->getErrors());
        }

        // Cannot save?
        if (!$table->save($item)) {
            $this->fail(500, 'Internal error');
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * I am the generic "view" JSON API action shared by all CRUD controllers.
     *
     * I validate the identifier and load the entity. Missing parameters, missing entities and
     * authorization failures are reported as normal JSON responses (400/401/403/404) instead
     * of uncaught exceptions.
     *
     * @param Table $table The Table to load the entity through.
     * @param mixed $id The (route) identifier of the entity. Deliberately not type-hinted at
     *   the call site (the action's own parameter) so that a missing or non-numeric value can
     *   be reported as a 400 instead of an ArgumentCountError/TypeError.
     * @param string $resourceName Human-readable name used in the "not found"/"missing id"
     *   error messages, e.g. 'Account'.
     * @param array $getOptions Options passed through to Table::get(), e.g. ['contain' => []].
     */
    final protected function viewEntity(Table $table, mixed $id, string $resourceName, array $getOptions = []): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$id)) {
            $this->fail(400, "Missing or invalid $resourceName id");
        }

        try {
            $item = $table->get((int)$id, $getOptions);
        } catch (RecordNotFoundException) {
            $this->fail(400, "$resourceName not found");
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * I am the generic "add" JSON API action shared by all CRUD controllers.
     *
     * I validate the request body, create and validate a new entity, then save it. Invalid
     * JSON input is reported as a normal JSON response (400) instead of an uncaught exception.
     *
     * @param Table $table The Table to create/save the entity through.
     * @param array $extraData Field values forced onto the request body before patching,
     *   taking precedence over anything the client sent, e.g. ['user_id' => $this->user->id]
     *   for server-controlled fields that must not be trusted from client input.
     */
    final protected function addEntity(Table $table, array $extraData = []): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        // Malformed JSON / non-object body (BodyParserMiddleware returns null on invalid JSON)
        $data = $this->request->getData();
        if (!is_array($data)) {
            $this->fail(400, 'Invalid JSON body');
        }

        $item = $table->newEmptyEntity();
        $item = $table->patchEntity($item, $extraData + $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->failMany(400, $item->getErrors());
        }

        // Cannot save?
        if (!$table->save($item)) {
            $this->fail(500, 'Internal error');
        }

        $this->response = $this->response->withStatus(201);
        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * I am the generic "delete" JSON API action shared by all CRUD controllers.
     *
     * I validate the identifier, load the entity and delete it. Missing parameters, missing
     * entities and authorization failures are reported as normal JSON responses
     * (400/401/403/404) instead of uncaught exceptions.
     *
     * @param Table $table The Table to load/delete the entity through.
     * @param mixed $id The (route) identifier of the entity. Deliberately not type-hinted at
     *   the call site (the action's own parameter) so that a missing or non-numeric value can
     *   be reported as a 400 instead of an ArgumentCountError/TypeError.
     * @param string $resourceName Human-readable name used in the "not found"/"missing id"/
     *   "not deleted" error messages, e.g. 'Account'.
     * @param array $getOptions Options passed through to Table::get(), e.g. ['contain' => []].
     */
    final protected function deleteEntity(Table $table, mixed $id, string $resourceName, array $getOptions = []): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$id)) {
            $this->fail(400, "Missing or invalid $resourceName id");
        }

        try {
            $item = $table->get((int)$id, $getOptions);
        } catch (RecordNotFoundException) {
            $this->fail(400, "$resourceName not found");
        }

        // Cannot delete?
        try {
            $deleted = $table->delete($item);
        } catch (QueryException) {
            $this->fail(409, "$resourceName cannot be deleted: still referenced by other records");
        }

        if (!$deleted) {
            $this->fail(500, "$resourceName not deleted");
        }

        $this->set('item');
        $this->set('success', true);
    }

    /**
     * I am the generic "upload-logo" JSON API action shared by all controllers whose Table uses the
     * LogoBehavior. I store the uploaded image (multipart field `logo`) as the one and only logo of the
     * entity, a previously uploaded logo is replaced. Only images are accepted.
     *
     * @param Table $table The Table (with LogoBehavior) to load the entity through.
     * @param mixed $id The (route) identifier of the entity, see editEntity().
     * @param string $resourceName Human-readable name used in error messages, e.g. 'Subscription'.
     *
     * @throws
     */
    final protected function uploadLogoEntity(Table $table, mixed $id, string $resourceName): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        if (!ctype_digit((string)$id)) {
            $this->fail(400, "Missing or invalid $resourceName id");
        }

        try {
            $item = $table->get((int)$id);
        } catch (RecordNotFoundException|UnauthorizedException) {
            $this->fail(404, "$resourceName not found");
        }

        $file = $this->request->getUploadedFile('logo');
        if (!$file instanceof UploadedFileInterface) {
            $this->fail(400, 'No logo uploaded');
        }

        try {
            /** @var LogoBehavior $logo */
            $logo = $table->getBehavior('Logo');
            $item = $logo->storeLogo($item, $file);
        } catch (InvalidArgumentException $exception) {
            $this->fail(400, $exception->getMessage());
        } catch (RuntimeException $exception) {
            $this->fail(500, $exception->getMessage());
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * I am the generic "delete-logo" JSON API action: I remove the logo of the entity (and delete its
     * file, if it was uploaded).
     *
     * @param Table $table The Table (with LogoBehavior) to load the entity through.
     * @param mixed $id The (route) identifier of the entity, see editEntity().
     * @param string $resourceName Human-readable name used in error messages, e.g. 'Subscription'.
     */
    final protected function deleteLogoEntity(Table $table, mixed $id, string $resourceName): void
    {
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        if (!ctype_digit((string)$id)) {
            $this->fail(400, "Missing or invalid $resourceName id");
        }

        try {
            $item = $table->get((int)$id);
            /** @var LogoBehavior $logo */
            $logo = $table->getBehavior('Logo');
            $item = $logo->removeLogo($item);
        } catch (RecordNotFoundException|UnauthorizedException) {
            $this->fail(404, "$resourceName not found");
        } catch (RuntimeException $exception) {
            $this->fail(500, $exception->getMessage());
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * I am the generic "logo" action: I deliver the uploaded logo file of the entity.
     *
     * @param Table $table The Table (with LogoBehavior) to load the entity through.
     * @param mixed $id The (route) identifier of the entity.
     * @throws NotFoundException If the entity or its uploaded logo does not exist.
     */
    final protected function logoEntityResponse(Table $table, mixed $id): Response
    {
        if (!ctype_digit((string)$id)) {
            throw new NotFoundException();
        }

        try {
            $item = $table->get((int)$id);
        } catch (RecordNotFoundException|UnauthorizedException) { // @phpstan-ignore catch.neverThrown (UnauthorizedException is thrown by OwnedTable::get())
            throw new NotFoundException();
        }

        /** @var LogoBehavior $logo */
        $logo = $table->getBehavior('Logo');
        $path = $logo->getLogoPath($item);
        if ($path === null) {
            throw new NotFoundException();
        }

        $mimeType = array_search(pathinfo($path, PATHINFO_EXTENSION), LogoBehavior::MIME_TYPES, true);

        return $this->response
            ->withFile($path)
            ->withType((string)$mimeType);
    }
}
