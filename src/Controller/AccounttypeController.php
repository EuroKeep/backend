<?php
declare(strict_types=1);

namespace eurokeep\Controller;

/**
 * Accounttype Controller
 *
 * @property \eurokeep\Model\Table\AccounttypeTable $Accounttype
 * @method \eurokeep\Model\Entity\Accounttype[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
     * @param mixed $accountTypeId Identifier of the accounttype. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function view($accountTypeId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Accounttype, $accountTypeId, 'Accounttype', ['contain' => []]);
    }

    /**
     * Add method
     *
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->Accounttype);
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
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Accounttype, $accountTypeId, 'Accounttype');
    }

    /**
     * Delete method
     *
     * @param mixed $accountTypeId Identifier of the accounttype. Not type-hinted so that a
     *   missing or non-numeric route segment can be reported as a normal 400 response instead
     *   of an uncaught ArgumentCountError/TypeError.
     */
    public function delete($accountTypeId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Accounttype, $accountTypeId, 'Accounttype');
    }
}
