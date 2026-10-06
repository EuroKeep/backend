<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Database\Expression\QueryExpression;

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
        $this->addEntity($this->Account, [
            'user_id' => $this->user->id,
            'balance_value' => 0,
            'flags' => 1,
        ]);
    }

    /**
     * View method
     *
     * @param mixed $entityId Identifier of the account. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($entityId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Account, $entityId, 'Account');
    }

    /**
     * Edit method
     *
     * @param mixed $entityId Identifier of the account. Not type-hinted so that a missing
     *   or non-numeric route segment can be reported as a normal 400 response instead of
     *   an uncaught ArgumentCountError/TypeError.
     */
    public function edit($entityId = null): void
    {
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Account, $entityId, 'Account', [], ['balance_value']);
    }

    /**
     * Delete method
     *
     * @param mixed $entityId Identifier of the account. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function delete($entityId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Account, $entityId, 'Account');
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
        $this->set('item', $entity);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['item', 'success']);
    }
}
