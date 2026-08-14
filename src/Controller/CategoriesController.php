<?php
declare(strict_types=1);

namespace eurokeep\Controller;

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
     * @param mixed $entityId Identifier of the Category. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($entityId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->Categories->recover();
        $this->viewEntity($this->Categories, $entityId, 'Category', ['contain' => ['Budget', 'Transactions', 'Subscription']]);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->Categories->recover();
        $this->addEntity($this->Categories);
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
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Categories, $entityId, 'Category', ['contain' => []]);
        $this->Categories->recover();
    }

    /**
     * Delete method
     *
     * @param mixed $entityId Identifier of the Category. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function delete($entityId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->Categories->recover();
        $this->deleteEntity($this->Categories, $entityId, 'Category');
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
