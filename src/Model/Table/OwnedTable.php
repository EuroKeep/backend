<?php
declare(strict_types=1);

namespace eurokeep\Model\Table;

use Cake\Datasource\EntityInterface;
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
     * But it also checks the ownership.
     */
    #[Override]
    public function get(mixed $primaryKey, array|string $finder = 'all', CacheInterface|string|null $cache = null, string|Closure|null $cacheKey = null, ...$args): EntityInterface
    {
        $element = parent::get($primaryKey, $finder, $cache, $cacheKey, $args);

        if ($element->has('user_id') && $element->get('user_id') !== self::$currentUserId) {
            throw new UnauthorizedException('This is not yours.');
        }

        return $element;
    }
}
