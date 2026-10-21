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

namespace eurokeep\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface {

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // Calling $handler->handle() delegates control to the *next* middleware
        // In your application's queue.
        $response = $handler->handle($request);

        if ($response instanceof Response) {
            if ($request instanceof ServerRequest) {
                // @todo fix localhost
                $response = $response
                    ->cors($request)
                    ->allowOrigin(['http://localhost:4200'])
                    ->allowMethods(['*'])
                    ->allowHeaders(['X-CSRF-Token'])
                    ->allowCredentials()
                    ->build()
                    ->withStatus(200, __('You shall pass!!'));
            }
        }

        return $response;
    }
}

