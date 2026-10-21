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

use Cake\Http\Response;

/**
 * PaymentMethod Controller
 *
 * @property \eurokeep\Model\Table\PaymentMethodTable $PaymentMethod
 */
class PaymentMethodController extends AuthenticatedController
{
    public array $paginate = [
        'limit' => 1000,
        'maxLimit' => 1000,
    ];

    /**
     * Index method
     *
     * Only active payment methods (flags & 1) are listed, unless the query param `all=1` is given
     * (used by the settings area to manage inactive payment methods as well).
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);
        $query = $this->PaymentMethod
            ->find()
            ->orderBy(['PaymentMethod.name' => 'ASC']);

        if ($this->request->getQuery('all') !== '1') {
            $query->where(['PaymentMethod.flags' => 1]);
        }

        $this->set('items', $this->paginate($query));
        $this->set('success', true);
        $this->viewBuilder()->setOption('serialize', ['items', 'success']);
    }

    /**
     * View method
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted so that a missing
     *   or non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function view($entityId = null): void
    {
        $this->request->allowMethod(['get']);
        $this->viewEntity($this->PaymentMethod, $entityId, 'PaymentMethod');
    }

    /**
     * Add method
     */
    public function add(): void
    {
        $this->request->allowMethod(['post']);
        $this->addEntity($this->PaymentMethod, [
            'flags' => (int)($this->request->getData('flags') ?? 1),
        ]);
    }

    /**
     * Edit method
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted so that a missing
     *   or non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function edit($entityId = null): void
    {
        $this->request->allowMethod(['patch', 'put']);
        $this->editEntity($this->PaymentMethod, $entityId, 'PaymentMethod');
    }

    /**
     * Delete method
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted so that a missing
     *   or non-numeric route segment can be reported as a normal 400 response instead of an
     *   uncaught ArgumentCountError/TypeError.
     */
    public function delete($entityId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteEntity($this->PaymentMethod, $entityId, 'PaymentMethod');
    }

    /**
     * I will store the uploaded image (multipart field `logo`) as the image of the given payment method.
     * A previously uploaded image is replaced. Only images are accepted.
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted, see view().
     */
    public function uploadLogo($entityId = null): void
    {
        $this->request->allowMethod(['post']);
        $this->uploadLogoEntity($this->PaymentMethod, $entityId, 'PaymentMethod');
    }

    /**
     * I will remove the image of the given payment method (and delete its file, if it was uploaded).
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted, see view().
     */
    public function deleteLogo($entityId = null): void
    {
        $this->request->allowMethod(['delete']);
        $this->deleteLogoEntity($this->PaymentMethod, $entityId, 'PaymentMethod');
    }

    /**
     * I will deliver the uploaded image of the given payment method.
     *
     * @param mixed $entityId Identifier of the payment method. Not type-hinted, see view().
     */
    public function logo($entityId = null): Response
    {
        $this->request->allowMethod(['get']);

        return $this->logoEntityResponse($this->PaymentMethod, $entityId);
    }
}
