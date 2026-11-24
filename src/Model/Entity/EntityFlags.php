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

namespace eurokeep\Model\Entity;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Boolean view on the `flags` bitmask of an entity.
 *
 * Every `FLAG_*` constant of the entity's table becomes a boolean property,
 * e.g. `UsersTable::FLAG_ACTIVE` => `$user->flag_states->isActive`.
 *
 * Reading and writing goes straight through AbstractEntity::getFlag()/setFlag(),
 * so the object never holds state of its own and is always in sync with `flags`.
 *
 * Writing an unknown flag or a non-boolean value does not throw, it adds a validation
 * error to the entity under `flag_states.<name>` (so save() fails and the API returns 400).
 * Reading an unknown flag is a programming error and still throws.
 */
final class EntityFlags implements JsonSerializable
{
    private readonly AbstractEntity $entity;
    private readonly array $map;

    /**
     * @param AbstractEntity $entity
     * @param array<string, int> $map Property name => flag bit
     */
    public function __construct(AbstractEntity $entity, array $map) {
        $this->entity = $entity;
        $this->map = $map;
    }

    /**
     * I will erturn whether the given $flagName is enabled or disabled.
     * @param string $flagName
     * @return bool
     */
    public function __get(string $flagName): bool
    {
        return $this->entity->getFlag($this->bit($flagName));
    }

    /**
     * I will set the given $flagName to the given $value.
     * @param string $flagName
     * @param mixed $value
     * @return void
     */
    public function __set(string $flagName, mixed $value): void
    {
        if (!isset($this->map[$flagName])) {
            $this->addError($flagName, 'unknownFlag', sprintf(
                'Unknown flag "%s". Known flags: %s',
                $flagName,
                implode(', ', array_keys($this->map)) ?: '-',
            ));

            return;
        }

        $bool = is_bool($value) ? $value : filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($bool === null) {
            $this->addError($flagName, 'boolean', sprintf('Flag "%s" must be a boolean value.', $flagName));

            return;
        }

        $this->entity->setFlag($this->map[$flagName], $bool);
    }

    /**
     * I will return whether the given $flagName has a value in the map.
     * @param string $flagName
     * @return bool
     */
    public function __isset(string $flagName): bool
    {
        return isset($this->map[$flagName]);
    }

    /**
     * @return array<string, bool>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->map as $flagName => $bit) {
            $result[$flagName] = $this->entity->getFlag($bit);
        }

        return $result;
    }

    /**
     * @return array<string, bool>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $flagName
     * @param string $rule
     * @param string $message
     * @return void
     */
    private function addError(string $flagName, string $rule, string $message): void
    {
        $this->entity->setError(AbstractEntity::FLAG_STATES_FIELD, [$flagName => [$rule => $message]]);
    }

    /**
     * I will return the binary position of the given $flagName.
     * @param string $flagName
     * @return int
     */
    private function bit(string $flagName): int
    {
        if (!isset($this->map[$flagName])) {
            throw new InvalidArgumentException(sprintf(
                'Unknown flag "%s" for %s. Known flags: %s',
                $flagName,
                $this->entity::class,
                implode(', ', array_keys($this->map)) ?: '-',
            ));
        }

        return $this->map[$flagName];
    }
}
