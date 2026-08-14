<?php
declare(strict_types=1);

namespace eurokeep\Controller;

use Cake\ORM\TableRegistry;
use DateTime;
use InvalidArgumentException;

/**
 * Transactions Controller
 *
 * @property \eurokeep\Model\Table\TransactionsTable $Transactions
 */
class TransactionsController extends AuthenticatedController
{
    public array $paginate = [
        'limit' => 100,
        'maxLimit' => 100,
        'order' => [
            'Transactions.created' => 'desc',
        ],
    ];

    /**
     * Add method
     *
     * Not built on the shared addEntity() helper: a transfer additionally creates a second,
     * mirrored transaction on the target account, which is single-entity logic doesn't cover.
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->Transactions, [
            'flags' => 1,
        ]);
    }

    /**
     * Edit method
     *
     * @param mixed $id Identifier of the transaction. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function edit($id = null): void
    {
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->Transactions, $id, 'Transaction');
    }

    /**
     * Index method
     *
     * @return void Renders view
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $qry = $this->Transactions->find();

        $transactions = $this->paginate($qry);

        $this->set(compact('transactions'));

        $this->set('transactions', $transactions);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['transactions', 'success']);
    }

    /**
     * @param int $accountId
     * @return void
     * @throws \DateMalformedStringException
     */
    public function stream(int $accountId): void
    {
        $this->request->allowMethod(['get']);
        /** @var \eurokeep\Controller\AccountTable $accountTable */
        $accountTable = TableRegistry::getTableLocator()->get('Account');
        $account = $accountTable->get($accountId);

        $start = new DateTime($this->request->getQueryParams()['start'] ?? date('Y-m-d H:i:s'));
        $end = new DateTime($this->request->getQueryParams()['end'] ?? date('Y-m-d H:i:s'));

        $transactions = $account->streamTransactions($start, $end);
        $settlement = $account->settlement($transactions);

        $this->set('transactions', $transactions);
        $this->set('settlement', $settlement);
        $this->set('account', $account);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['settlement', 'transactions', 'account', 'success']);
        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * Try find older comments with data from post
     *
     * @return void
     */
    public function mapping(): void
    {
        $this->request->allowMethod(['post']);
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['success']);

        $comment = $this->request->getData('comment', '');
        $comment = addcslashes($comment, '%_');
        if (empty($comment)) {
            throw new InvalidArgumentException('Comment cannot be empty');
        }
        $transaction = $this
            ->Transactions
            ->find()
            ->where([
                'comment LIKE' => "%$comment%",
            ])
            ->orderBy([
                'Transactions.created' => 'DESC',
            ])
            ->contain(['Categories'])
            ->firstOrFail();
        $this->set('transaction', $transaction);

        $this->viewBuilder()->setOption('serialize', ['success', 'transaction']);
    }

    /**
     * View method
     *
     * @param mixed $id Identifier of the transaction. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($id = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->Transactions, $id, 'Transaction');
    }

    /**
     * Delete method
     *
     * @param mixed $id Identifier of the transaction. Not type-hinted so that a missing or
     *   non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function delete($id = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->Transactions, $id, 'Transaction');
    }
}
