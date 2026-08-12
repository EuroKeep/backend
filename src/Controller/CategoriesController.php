<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Response;
use eurokeep\Model\Table\CategoriesTable;

/**
 * Categories Controller
 *
 * @property CategoriesTable $Categories
 */
class CategoriesController extends AuthenticatedController
{
    public array $paginate = [
        'limit' => 1000,
        'maxLimit' => 1000,
        'order' => [
            'Category.name' => 'asc',
        ],
    ];

    /**
     * Index method
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $this->Categories->recover();
        $categoriesQuery = $this
            ->Categories
            ->find()
            ->orderBy(['Categories.name' => 'ASC']);

        $categories = $this->paginate($categoriesQuery);

        $this->set('items', $categories);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    /**
     * View method
     *
     * @param int $entityId Identifier of the Category.
     * @throws \eurokeep\Controller\RecordNotFoundException When record not found.
     */
    public function view(int $entityId): void
    {
        $this->request->allowMethod(['get']);
        $this->Categories->recover();
        $category = $this->Categories->get($entityId, contain: ['Budget', 'Transactions', 'Subscription']);
        $this->set(compact('category'));
    }

    /**
     * Add method
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $category = $this->Categories->newEmptyEntity();
        $category = $this->Categories->patchEntity($category, $this->request->getData());
        if ($this->Categories->save($category)) {
            $this->Flash->success(__('The category has been saved.'));

            return $this->redirect(['action' => 'index']);
        }
        $this->Flash->error(__('The category could not be saved. Please, try again.'));
        $this->set(compact('category'));
    }

    /**
     * Edit method
     *
     * @param mixed $entityId Identifier of the Category. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function edit($entityId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->Categories->recover();
        $this->editEntity($this->Categories, $entityId, 'Category', ['contain' => []]);
    }

    /**
     * Delete method
     *
     * @param int $entityId Identifier of the Category.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete(int $entityId): void
    {
        $this->request->allowMethod(['delete']);

        try {
            $entity = $this->Categories->get($entityId);
        } catch (RecordNotFoundException) {
            $this->set('item');
            $this->set('success', false);
            $this->set('errors', ['Category not found']);

            $this->viewBuilder()->setOption('serialize', ['item', 'success', 'errors']);

            return;
        }

        if (!$this->Categories->delete($entity)) {
            $this->addError('Error', 'Category not deleted.');
        }
    }

    /**
     * Tree function
     */
    public function tree(): void
    {
        $categories = $this->Categories
            ->find('threaded')
            ->orderBy(['lft' => 'ASC'])
            ->toArray();

        $this->set('categoryTree', $categories);
        $this->viewBuilder()->setOption('serialize', ['categoryTree', 'success']);
    }
}
