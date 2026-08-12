<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Account Controller
 *
 * @property \eurokeep\Model\Table\AccountTable $Account
 * @method \eurokeep\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccountController extends AuthenticatedController
{
    public array $paginate = [
        'limit' => 100,
        'maxLimit' => 100,
    ];

    /**
     * Index method
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $query = $this
            ->Account
            ->find()
            ->where(fn(QueryExpression $exp) => $exp->add('(flags & 1) <> 0'))
            ->orderBy(fn(QueryExpression $exp, $q) => [
                '(flags & 2)' => 'DESC',
                'Account.name' => 'ASC',
            ]);

        $this->set('items', $this->paginate($query));
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success']);

        // Fetch data
        $data = $this->request->getData();
        $data['user_id'] = $this->user->id;
        $data['balance_value'] = 0;
        $data['flags'] = 1;

        // Add the Account
        $entity = $this->Account->newEmptyEntity();
        $entity = $this->Account->patchEntity($entity, $data);

        // Validate
        if ($entity->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('error', $entity->getErrors());
            $this->viewBuilder()->setOption('serialize', ['success', 'error']);

            return;
        }
        // Save
        if (!$this->Account->save($entity)) {
            $this->response = $this->response->withStatus(500);
            $this->set('success', false);
            $this->set('error', 'Saving failed');
            $this->viewBuilder()->setOption('serialize', ['success', 'error']);

            return;
        }

        $this->response = $this->response->withStatus(201);
        $this->set('item', $entity);
        $this->viewBuilder()->setOption('serialize', ['success', 'item']);
    }

    /**
     * View method
     *
     * @param int $entityId Identifier of the account.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(int $entityId): void
    {
        $this->request->allowMethod(['get']);

        try {
            $this->set('item', $this->Account->get($entityId));
            $this->set('success', true);
        } catch (RecordNotFoundException) {
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Account not found']);
        }

        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);
    }

    /**
     * Edit method
     *
     * @param mixed $entityId Identifier of the account.
     */
    public function edit($entityId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$entityId)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Missing or invalid account id']);

            return;
        }

        // Malformed JSON / non-object body (BodyParserMiddleware returns null on invalid JSON)
        $data = $this->request->getData();
        if (!is_array($data)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Invalid JSON body']);

            return;
        }

        try {
            $item = $this->Account->get((int)$entityId);
        } catch (RecordNotFoundException) {
            $this->response = $this->response->withStatus(404);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Account not found']);

            return;
        }

        // Apply the new data
        $item = $this->Account->patchEntity($item, $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', $item->getErrors());

            return;
        }

        // Cannot save?
        if (!$this->Account->save($item)) {
            $this->response = $this->response->withStatus(500);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Internal error']);

            return;
        }

        $this->set('item', $item);
        $this->set('success', true);
    }

    /**
     * Delete method
     *
     * @param int $entityId Identifier of the account.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(int $entityId): void
    {
        $this->request->allowMethod(['delete']);

        try {
            $entity = $this->Account->get($entityId);
        } catch (RecordNotFoundException) {
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Account not found']);

            $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);

            return;
        }

        if (!$this->Account->delete($entity)) {
            $this->addError('Error', 'Account not deleted.');
        }
    }

    /**
     * toggleFavourite method
     *
     * @param int $entityId Identifier of the account.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggleFavourite(int $entityId): void
    {
        $this->request->allowMethod(['post']);
        // Fetch & Toggle
        $entity = $this->Account->get($entityId);
        $entity->flags ^= 2;
        $this->Account->save($entity);

        // Response
        $this->set('account', $entity);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['account', 'success']);
    }
}
