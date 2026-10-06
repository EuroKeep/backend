<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */

namespace eurokeep\Controller;

use Cake\Chronos\Chronos;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\View\JsonView;
use Override;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * @inheritDoc
     * @throws
     */
    #[Override]
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Authentication.Authentication');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function viewClasses(): array
    {
        $this->viewClasses = [JsonView::class];
        return parent::viewClasses();
    }

    /**
     * @inheritDoc
     *
     * We always render JSON. There's no front-end here.
     */
    #[Override]
    public function beforeRender(EventInterface $event): void
    {
        // Make sure that the success key is true if not defined.
        if (!$this->has('success')) {
            $this->set('success', true);
        }

        // Make sure the errors array exists, even if empty.
        if (!$this->has('errors')) {
            $this->set('errors', []);
        }

        // Add CSRF Token
        $this->set('csrfToken', $this->request->getAttribute('csrfToken'));

        $serialize = $this->viewBuilder()->getOption('serialize');
        $serialize[] = 'success';
        $serialize[] = 'errors';
        $serialize[] = 'csrfToken';
        $serialize = array_unique($serialize);

        $this->viewBuilder()->setOption('serialize', $serialize);
        $this->viewBuilder()->setClassName('Json');

        parent::beforeRender($event);
    }

    /**
     * I will verify if the viewBuilder has the given $key.
     * @param string $key The key you want to check for.
     * @return bool  Whether the $key is part of the viewBuilder.
     */
    final protected function has(string $key): bool
    {
        return $this->viewBuilder()->hasVar($key);
    }

    /**
     *  I will add a new error with the given $key and $message to the ViewBuilder.
     * @param string $key The key for the error message. May be a field name, etc.
     * @param string $message The actual error message that.
     */
    final protected function addError(string $key, string $message): void
    {
        $errors = $this->viewBuilder()->getVar($key);
        $errors[$key] = $message;
        $this->set('errors', $errors);
    }

    /**
     * @return Chronos[]                   I return the real start and end normalized to server time.
     */
    protected function getRange(): array
    {
        $startString = $this->request->getQueryParams()['start'] ?? 'first day of this month';
        $endString = $this->request->getQueryParams()['end'] ?? 'first day of next month';
        $startObject = (new DateTime())->modify($startString);
        $endObject = (new DateTime())->modify($endString);

        if ($startString && $endString) {
            $startObject = new DateTime($startString);
            $endObject = new DateTime($endString);
        }

        $startChronos = Chronos::createFromInterface($startObject)->startOfMonth();
        $endChronos = Chronos::createFromInterface($endObject)->startOfMonth();


        return [
            $startChronos,
            $endChronos,
        ];
    }

    /**
     * I will make it easier to reply with error codes.
     *
     * @param int $statusCode I am the HTTP StatusCode for the error response.
     * @param string $message I am the message text sent in the response.
     * @return void
     */
    protected function fail(int $statusCode, string $message): void
    {
        $this->response = $this->response->withStatus($statusCode);
        $this->set('item');
        $this->set('success', false);
        $this->set('error', [$message]);
    }

    /**
     * For the given $startString and $endString, I will return the first Day and last Day of the year.
     * @param string|null $startString I am the first date of said year.
     * @param string|null $endString I am the last day of said year.
     * @return Chronos[]
     */
    protected function getAnnualRange(string|null $startString = null, string|null $endString = null): array
    {
        [$start, $end] = $this->getRange();

        $start->modify('first day of this year');
        $end->modify('last day of this year');

        return [
            $start,
            $end,
        ];
    }

}