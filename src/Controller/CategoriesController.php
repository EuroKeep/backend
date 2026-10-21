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

use Cake\Database\Exception\DatabaseException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
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

    /**
     * Move method
     *
     * I move a Category to a new parent (`parent_id`, `null` = root) and to the given zero-based
     * `position` among its new siblings. Moving a Category into itself or one of its own
     * descendants is rejected with a 400.
     *
     * @param mixed $entityId Identifier of the Category. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function move($entityId = null): void
    {
        $this->request->allowMethod(['patch', 'put']);
        $this->viewBuilder()->setOption('serialize', ['item', 'success', 'error']);

        if (!ctype_digit((string)$entityId)) {
            $this->fail(400, 'Missing or invalid Category id');
        }

        $parentId = $this->request->getData('parent_id');
        $parentId = $parentId === null || $parentId === '' ? null : (int)$parentId;
        $position = max(0, (int)$this->request->getData('position', 0));

        try {
            $item = $this->Categories->get((int)$entityId);
        } catch (RecordNotFoundException) {
            $this->fail(404, 'Category not found');
        }

        $connection = $this->Categories->getConnection();
        try {
            $connection->transactional(function () use ($item, $parentId, $position): void {
                // 1. Re-parent: the TreeBehavior appends the node as the last child of its new parent.
                if ($item->parent_id !== $parentId) {
                    $item->parent_id = $parentId;
                    $this->Categories->saveOrFail($item);
                }

                // 2. Re-order among the (new) siblings.
                $siblings = $this->Categories
                    ->find()
                    ->where($parentId === null ? ['parent_id IS' => null] : ['parent_id' => $parentId])
                    ->orderBy(['lft' => 'ASC'])
                    ->all()
                    ->extract('id')
                    ->toList();
                $current = array_search($item->id, $siblings, true);
                $target = min($position, count($siblings) - 1);

                if ($current > $target) {
                    $this->Categories->moveUp($item, $current - $target);
                } elseif ($current < $target) {
                    $this->Categories->moveDown($item, $target - $current);
                }
            });
        } catch (DatabaseException $exception) {
            // e.g. moving a Category into itself or one of its descendants
            $this->fail(400, $exception->getMessage());
        } catch (PersistenceFailedException) {
            $this->fail(400, 'Parent category not found');
        }

        $this->set('item', $this->Categories->get($item->id));
        $this->set('success', true);
    }
}
