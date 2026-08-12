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
     * @param mixed $accountTypeId Identifier of the accounttype. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function edit($accountTypeId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);

        // Missing or non-numeric identifier
        if (!ctype_digit((string)$accountTypeId)) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Missing or invalid accounttype id']);

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
            $item = $this->Accounttype->get((int)$accountTypeId);
        } catch (RecordNotFoundException) {
            $this->response = $this->response->withStatus(404);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Accounttype not found']);

            return;
        }

        // Apply the new data
        $item = $this->Accounttype->patchEntity($item, $data);

        // Haz errors?
        if ($item->hasErrors()) {
            $this->response = $this->response->withStatus(400);
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', $item->getErrors());

            return;
        }

        // Cannot save?
        if (!$this->Accounttype->save($item)) {
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
