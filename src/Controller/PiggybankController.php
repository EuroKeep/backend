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

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use eurokeep\Model\Table\TransactionsTable;
use eurokeep\Model\Table\TransactionsToPiggybanksTable;
use JetBrains\PhpStorm\NoReturn;

/**
 * Piggybank Controller
 *
 * @property \eurokeep\Model\Table\PiggybankTable $Piggybank
 * @method \eurokeep\Controller\Piggybank[]|\eurokeep\Controller\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PiggybankController extends AuthenticatedController
{
    /**
     * Index method
     *
     * @return void Renders view
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $qry = $this->Piggybank->find();

        $filters = $this->getRequest()->getQueryParams()['filter'] ?? [];
        foreach ($filters as $fieldname => $value) {
            $qry->where(["$fieldname LIKE" => "$value%",]);
        }

        $this->set('items', $this->paginate($qry));
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items']);
    }

    public function fetch($transactionId = null): void
    {
        $this->request->allowMethod(['get']);

        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');

        try {
            $transactionsTable->get($transactionId);
        } catch (RecordNotFoundException) { // @phpstan-ignore catch.neverThrown (thrown by OwnedTable::get(), hidden by the baked @method get())
            $this->fail(404, 'Transaction not found');
        } catch (UnauthorizedException) { // @phpstan-ignore catch.neverThrown (thrown by OwnedTable::get(), hidden by the baked @method get())
            $this->fail(401, 'Not authorized to view this Transaction');
        }


        /** @var TransactionsToPiggybanksTable $transactionsToPiggybanksTable */
        $transactionsToPiggybanksTable = TableRegistry::getTableLocator()->get('TransactionsToPiggybanks');
        $items = $transactionsToPiggybanksTable->find()->where(['transaction_id' => $transactionId])->all();

        $arr = [
            'transaction_id' => $transactionId,
            'parts' => []
        ];

        foreach ($items as $item) {
            $arr['parts'][$item->get('piggybank_id')] = $item->get('balance_value');
        }

        $this->set('item', $arr);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['item']);
    }

    /**
     * Save a Piggybank
     */
    #[NoReturn]
    public function save($transactionId = null): void
    {
        $this->request->allowMethod(['post']);

        /** @var TransactionsTable $transactionsTable */
        $transactionsTable = TableRegistry::getTableLocator()->get('Transactions');

        try {
            $transactionsTable->get($transactionId);
        } catch (RecordNotFoundException) { // @phpstan-ignore catch.neverThrown (thrown by OwnedTable::get(), hidden by the baked @method get())
            $this->fail(404, 'Transaction not found');
        } catch (UnauthorizedException) { // @phpstan-ignore catch.neverThrown (thrown by OwnedTable::get(), hidden by the baked @method get())
            $this->fail(401, 'Not authorized to view this Transaction');
        }

        /** @var TransactionsToPiggybanksTable $transactionsToPiggybanksTable */
        $transactionsToPiggybanksTable = TableRegistry::getTableLocator()->get('TransactionsToPiggybanks');


        $data = $this->request->getData();

        foreach ($data['parts'] as $piggybankId => $balance) {
            // Only for the existence/ownership check, get() throws for unknown or foreign Piggybanks.
            $this->Piggybank->get($piggybankId);
            $transactionsToPiggybanksEntry = $transactionsToPiggybanksTable->find()->where(['piggybank_id' => $piggybankId, 'transaction_id' => $transactionId])->first();

            if (!$transactionsToPiggybanksEntry) {
                $transactionsToPiggybanksEntry = $transactionsToPiggybanksTable->newEmptyEntity();
            }

            $transactionsToPiggybanksEntry->transaction_id = $transactionId;
            $transactionsToPiggybanksEntry->balance_value = $balance;
            $transactionsToPiggybanksEntry->piggybank_id = $piggybankId;
            $transactionsToPiggybanksTable->save($transactionsToPiggybanksEntry);
        }
    }

    /**
     * View method
     *
     * @param mixed $id Identifier of the piggybank. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($id = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Piggybank, $id, 'Piggybank', ['contain' => []]);
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->Piggybank, ['user_id' => $this->user->id]);
    }

    /**
     * Edit method
     *
     * @param mixed $id Identifier of the piggybank. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function edit($id = null): void
    {
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Piggybank, $id, 'Piggybank', ['contain' => []]);
    }

    /**
     * Delete method
     *
     * @param mixed $id Identifier of the piggybank. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function delete($id = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Piggybank, $id, 'Piggybank');
    }
}
