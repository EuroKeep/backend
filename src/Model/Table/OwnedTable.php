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

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\Table;
use Closure;
use eurokeep\Model\Behavior\UserOwnedBehavior;
use Override;
use Psr\SimpleCache\CacheInterface;

/**
 * Account Model
 *
 * @mixin UserOwnedBehavior
 */
abstract class OwnedTable extends Table
{
    public static ?int $currentUserId = -1;

    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     */
    #[Override]
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('UserOwned', [
            'user_id' => self::$currentUserId,
        ]);
    }

    /**
     * I will set the UserId of the owner of the Entities that inherit from me.
     * @param int|null $userId I am the id of the User that must own searched entities.
     */
    public static function setCurrentUserId(?int $userId): void
    {
        self::$currentUserId = $userId;
    }

    /**
     * This does the same as the cake ORM->get()
     *
     * But it also checks the ownership. Also, it disables the access to the user_id field.
     *
     * @throws RecordNotFoundException When the entity does not exist (or is filtered out by UserOwnedBehavior).
     * @throws UnauthorizedException When the entity belongs to another user.
     */
    #[Override]
    public function get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, string|Closure|null $cacheKey = null, ...$args): EntityInterface
    {
        $element = parent::get($primaryKey, $finder, $cache, $cacheKey, $args);

        if ($element->has('user_id') && $element->get('user_id') !== self::$currentUserId) {
            throw new UnauthorizedException('This is not yours.');
        }

        $element->setAccess('user_id', false);

        return $element;
    }
}
