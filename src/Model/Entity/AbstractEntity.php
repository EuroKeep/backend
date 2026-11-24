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

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionClassConstant;

/**
 * Abstract Entity
 *
 * Provides bitwise access to the `flags` column. The available flags are read from
 * the `FLAG_*` constants of the entity's table class (e.g. `UsersTable::FLAG_ACTIVE`).
 *
 * @property int $flags
 * @property EntityFlags $flag_states Boolean view on `flags`, e.g. `$user->flag_states->isActive`
 */
abstract class AbstractEntity extends Entity
{
    public const string FLAG_STATES_FIELD = 'flag_states';

    /**
     * Cache of flag maps per table class.
     *
     * @var array<class-string, array<string, int>>
     */
    private static array $flagMapCache = [];

    /**
     * @param array<string, mixed> $properties
     * @param array<string, mixed> $options
     */
    public function __construct(array $properties = [], array $options = [])
    {
        if (!in_array(self::FLAG_STATES_FIELD, $this->_virtual, true)) {
            $this->_virtual[] = self::FLAG_STATES_FIELD;
        }
        // flag_states may only be mass-assigned if flags itself may be.
        $this->_accessible[self::FLAG_STATES_FIELD] = $this->_accessible['flags'] ?? $this->_accessible['*'] ?? false;

        parent::__construct($properties, $options);
    }

    /**
     * Sets or clears a single flag bit (e.g. `UsersTable::FLAG_ACTIVE`).
     */
    final public function setFlag(int $flagBit, bool $value): void
    {
        self::assertSingleBit($flagBit);

        $flags = (int)($this->get('flags') ?? 0);
        $flags = $value ? ($flags | $flagBit) : ($flags & ~$flagBit);

        $this->set('flags', $flags);
    }

    /**
     * Returns whether a single flag bit (e.g. `UsersTable::FLAG_ACTIVE`) is set.
     */
    final public function getFlag(int $flagBit): bool
    {
        self::assertSingleBit($flagBit);

        return ((int)($this->get('flags') ?? 0) & $flagBit) !== 0;
    }

    /**
     * Property name => flag bit for this entity, derived from the table's `FLAG_*` constants.
     * `FLAG_SKIP_ANALYSIS = 2` becomes `['isSkipAnalysis' => 2]`.
     *
     * @return array<string, int>
     */
    final public function getFlagMap(): array
    {
        $source = $this->getSource();
        if ($source === '') {
            return [];
        }

        $tableClass = TableRegistry::getTableLocator()->get($source)::class;
        if (isset(self::$flagMapCache[$tableClass])) {
            return self::$flagMapCache[$tableClass];
        }

        $map = [];
        $constants = (new ReflectionClass($tableClass))->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC);
        foreach ($constants as $constant) {
            $value = $constant->getValue();
            if (!str_starts_with($constant->getName(), 'FLAG_') || !is_int($value) || !self::isSingleBit($value)) {
                continue;
            }
            $words = explode('_', strtolower(substr($constant->getName(), 5)));
            $map['is' . implode('', array_map(ucfirst(...), $words))] = $value;
        }

        return self::$flagMapCache[$tableClass] = $map;
    }

    /**
     * Getter for the virtual `flag_states` field.
     */
    protected function _getFlagStates(): EntityFlags
    {
        return new EntityFlags($this, $this->getFlagMap());
    }

    /**
     * Setter for the virtual `flag_states` field, e.g. from API input:
     * `{"flag_states": {"isActive": false}}`. Only the given flags are changed.
     * Unknown flags or non-boolean values become validation errors (see EntityFlags::__set()).
     *
     * @param iterable<string, mixed>|EntityFlags $states
     */
    protected function _setFlagStates(iterable|EntityFlags $states): null
    {
        $flags = $this->_getFlagStates();
        $states = $states instanceof EntityFlags ? $states->toArray() : $states;
        foreach ($states as $name => $value) {
            $flags->{$name} = $value;
        }

        // Nothing is stored for the virtual field itself, everything lives in `flags`.
        return null;
    }

    private static function isSingleBit(int $value): bool
    {
        return $value > 0 && ($value & ($value - 1)) === 0;
    }

    private static function assertSingleBit(int $flagBit): void
    {
        if (!self::isSingleBit($flagBit)) {
            throw new InvalidArgumentException(sprintf('Flag bit must be a single power of two, %d given.', $flagBit));
        }
    }
}
