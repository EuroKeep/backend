<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use eurokeep\Model\Entity\Accounttype;
use eurokeep\Model\Table\AccounttypeTable;

/**
 * Accounttype Controller
 *
 * @property AccounttypeTable $Accounttype
 * @method Accounttype[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccounttypeController extends AuthenticatedController
{
    /**
     * Index method
     *
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $accounttypes = $this->paginate($this->Accounttype);

        $this->set(compact('accounttypes'));
        $this->set('items', $accounttypes);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success', 'accounttypes', 'items']);
    }

    /**
     * View method
     *
     * @param int|null $accountTypeId Accounttype id.
     * @throws RecordNotFoundException When record not found.
     */
    public function view(int|null $accountTypeId = null): void
    {
        $this->request->allowMethod(['get']);
        $accounttype = $this->Accounttype->get($accountTypeId, [
            'contain' => [],
        ]);

        $this->set('accounttype', $accounttype);
        $this->viewBuilder()->setOption('serialize', ['account', 'success']);
    }

    /**
     * Add method
     *
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success']);

        // Fetch data
        $account = $this->request->getData();

        // Add the Account
        $entity = $this->Accounttype->newEmptyEntity();
        $entity = $this->Accounttype->patchEntity($entity, $account);

        if ($entity->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('error', $entity->getErrors());
            $this->viewBuilder()->setOption('serialize', ['error']);
            return;
        }
        $this->Accounttype->save($entity);

        $this->set('account', $entity);
        $this->viewBuilder()->setOption('serialize', ['success', 'account']);
    }

    /**
     * Edit method
     *
     * @param int|null $accountTypeId Accounttype id.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit(int|null $accountTypeId = null): void
    {
        $this->request->allowMethod(['post']);

        $accounttype = $this->Accounttype->get($accountTypeId);

        // Apply the new data
        $accounttype = $this
            ->Accounttype
            ->patchEntity($accounttype, $this->request->getData());

        // Haz errors?
        if ($accounttype->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('error', $accounttype->getErrors());
            $this->viewBuilder()->setOption('serialize', ['error']);
            return;
        }

        // Cannot save?
        if (!$this->Accounttype->save($accounttype)) {
            $this->set('success', false);
        }
        $this->set(compact('accounttype'));
    }

    /**
     * Delete method
     *
     * @param int|null $accountTypeId Accounttype id.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete(int|null $accountTypeId = null): void
    {
        $this->request->allowMethod(['delete']);
        $accountType = $this->Accounttype->get($accountTypeId);

        if (!$this->Accounttype->delete($accountType)) {
            $this->addError('Error', 'Account type not deleted.');
        }
    }
}
