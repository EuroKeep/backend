<?php
declare(strict_types=1);

namespace eurokeep\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use eurokeep\Model\Table\OwnedTable;

/**
 * UserOwned behavior
 */
class UserOwnedBehavior extends Behavior
{
    private function allowAccess(EntityInterface $entity): bool
    {
        $userId = $this->getConfig('user_id');
        if ($userId === null) {
            return true;
        }

        $entityUserId = $entity->get('user_id');
        if (empty($entityUserId)) {
            return true;
        }

        if ($entityUserId !== $userId) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function beforeFind($event, Query $query, $options, $primary): void
    {
        $userId = $this->getConfig('user_id');
        if ($userId === null) {
            return;
        }

        $query->where([
            $this->_table->getAlias() . '.user_id' => $userId,
        ]);
    }

    public function beforeDelete(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$this->allowAccess($entity)) {
            throw new UnauthorizedException('You are not allowed to delete this item');
        }
    }

    /**
     * I force every Entity of the owning Table to belong to the current user, on both create and
     * update. This runs after patchEntity()/newEntity(), so it always wins over whatever `user_id`
     * a client may have tried to smuggle into the request body - closing that mass-assignment gap
     * regardless of what the Entity's `_accessible` map allows.
     *
     * @param EventInterface $event The beforeSave event.
     * @param EntityInterface $entity The Entity about to be saved.
     * @param ArrayObject $options Options passed to Table::save().
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options): void
    {
        if (!$this->allowAccess($entity)) {
            throw new UnauthorizedException('You are not allowed to delete this item');
        }

        // Bypass `_accessible`/guarding on purpose: this is the one place that is allowed to set
        // user_id, everyone else (including the client) is not.
        $entity->set('user_id', OwnedTable::$currentUserId, ['guard' => false]);
    }
}
