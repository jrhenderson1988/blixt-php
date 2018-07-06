<?php

namespace Blixt\Persistence;

use InvalidArgumentException;

/**
 * Not currently used, though it will be in the future.
 */
class Condition
{
    const EQUALS = 1;
    const NOT_EQUALS = 2;
    const GREATER_THAN = 3;
    const GREATER_THAN_OR_EQUAL_TO = 4;
    const LESS_THAN = 5;
    const LESS_THAN_OR_EQUAL_TO = 6;
    const IN = 7;
    const NOT_IN = 8;
    const STARTS_WITH = 9;
    const ENDS_WITH = 10;
    const CONTAINS = 11;
    const NULL = 12;
    const NOT_NULL = 13;
    const EQ = 1;
    const IS = 1;
    const NEQ = 2;
    const GT = 3;
    const GTE = 4;
    const LT = 5;
    const LTE = 6;
    const NIN = 8;
    const SW = 9;
    const EW = 10;
    const CT = 11;
    const N = 12;
    const NN = 13;

    /**
     * A set of mappings from string values to integer representations of operators.
     *
     * @var array
     */
    protected static $operators = [
        '=' => self::EQUALS,
        '!=' => self::NOT_EQUALS,
        '<>' => self::NOT_EQUALS,
        '>' => self::GREATER_THAN,
        '>=' => self::GREATER_THAN_OR_EQUAL_TO,
        '<' => self::LESS_THAN,
        '<=' => self::LESS_THAN_OR_EQUAL_TO,
        'in' => self::IN,
        'not in' => self::NOT_IN,
        'starts with' => self::STARTS_WITH,
        'ends with' => self::ENDS_WITH,
        'contains' => self::CONTAINS,
        'null' => self::NULL,
        'is null' => self::NULL,
        'not null' => self::NOT_NULL,
        'is not null' => self::NOT_NULL,
    ];

    /**
     * @var string
     */
    protected $field;

    /**
     * @var int
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * Condition constructor.
     *
     * @param string     $field
     * @param int        $operator
     * @param mixed|null $value
     */
    public function __construct(string $field, int $operator, $value = null)
    {
        $this->setField($field);
        $this->setOperator($operator);
        $this->setValue($value);
    }

    /**
     * Get the field in the condition.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Set the condition field.
     *
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * Get the condition operator.
     *
     * @return int
     */
    public function getOperator(): int
    {
        return $this->operator;
    }

    /**
     * Set the condition operator.
     *
     * @param int $operator
     */
    public function setOperator(int $operator): void
    {
        if (! in_array($operator, static::$operators)) {
            throw new InvalidArgumentException('Invalid operator provided.');
        }

        $this->operator = $operator;
    }

    /**
     * Get the condition value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the condition value. If the value provided is not an array, and the operator is either IN or NOT IN, the
     * given value is converted into an array. Similarly, if the operator is either NULL or NOT NULL, the value is set
     * to null, as it is not needed. In all other cases, the value is simply set.
     *
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $operator = $this->getOperator();

        if (in_array($operator, [static::IN, static::NOT_IN])) {
            $this->value = is_array($value) ? $value : [];
        } elseif (in_array($operator, [static::NULL, static::NOT_NULL])) {
            $this->value = null;
        } else {
            $this->value = $value;
        }
    }

    /**
     * Factory method to make a condition. The developer may provide only two parameters, the field and the value and
     * the operator will be assumed to be equals. Additionally, if providing an operator, the developer may provide a
     * valid string representation of an operator and it will be converted to the correct integer representation.
     *
     * @param string     $field
     * @param string|int $operator
     * @param mixed|null $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function make(string $field, $operator, $value = null): Condition
    {
        if ($value === null) {
            $value = $operator;
            $operator = static::EQUALS;
        }

        return new static(
            $field,
            is_string($operator) ? static::determineOperator($operator) : $operator,
            $value
        );
    }

    /**
     * Determine the correct integer operator with the given string value.
     *
     * @param string $operator
     *
     * @return int
     */
    public static function determineOperator(string $operator): int
    {
        if (isset(static::$operators[$operator = mb_strtolower(trim($operator))])) {
            return static::$operators[$operator];
        }

        throw new InvalidArgumentException('Invalid operator provided.');
    }

    /**
     * Create an equals condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function equals(string $field, $value): Condition
    {
        return self::make($field, static::EQUALS, $value);
    }

    /**
     * Create a not equals condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function notEquals(string $field, $value): Condition
    {
        return self::make($field, static::NOT_EQUALS, $value);
    }

    /**
     * Create a greater than condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function greaterThan(string $field, $value): Condition
    {
        return self::make($field, static::GREATER_THAN, $value);
    }

    /**
     * Create a greater than or equal to condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function greaterThanOrEqualTo(string $field, $value): Condition
    {
        return self::make($field, static::GREATER_THAN_OR_EQUAL_TO, $value);
    }

    /**
     * Create a less than condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function lessThan(string $field, $value): Condition
    {
        return self::make($field, static::LESS_THAN, $value);
    }

    /**
     * Create a less than or equal to condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function lessThanOrEqualTo(string $field, $value): Condition
    {
        return self::make($field, static::LESS_THAN_OR_EQUAL_TO, $value);
    }

    /**
     * Create an in condition.
     *
     * @param string $field
     * @param array  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function in(string $field, array $value): Condition
    {
        return self::make($field, static::IN, $value);
    }

    /**
     * Create a not in condition.
     *
     * @param string $field
     * @param array  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function notIn(string $field, array $value): Condition
    {
        return self::make($field, static::NOT_IN, $value);
    }

    /**
     * Create a starts with condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function startsWith(string $field, $value): Condition
    {
        return self::make($field, static::STARTS_WITH, $value);
    }

    /**
     * Create an ends with condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function endsWith(string $field, $value): Condition
    {
        return self::make($field, static::ENDS_WITH, $value);
    }

    /**
     * Create a contains condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function contains(string $field, $value): Condition
    {
        return self::make($field, static::CONTAINS, $value);
    }

    /**
     * Create a null condition.
     *
     * @param string $field
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function null(string $field): Condition
    {
        return self::make($field, static::NULL);
    }

    /**
     * Create a not null condition.
     *
     * @param string $field
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function notNull(string $field): Condition
    {
        return self::make($field, static::NOT_NULL);
    }

    /**
     * Create an equals condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function eq(string $field, $value): Condition
    {
        return self::equals($field, $value);
    }

    /**
     * Create an equals condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function is(string $field, $value): Condition
    {
        return self::equals($field, $value);
    }

    /**
     * Create a not equals condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function ne(string $field, $value): Condition
    {
        return self::notEquals($field, $value);
    }

    /**
     * Create a greater than condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function gt(string $field, $value): Condition
    {
        return self::greaterThan($field, $value);
    }

    /**
     * Create a greater than or equal to condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function gte(string $field, $value): Condition
    {
        return self::greaterThanOrEqualTo($field, $value);
    }

    /**
     * Create a less than condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function lt(string $field, $value): Condition
    {
        return self::lessThan($field, $value);
    }

    /**
     * Create a less than or equal to condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function lte(string $field, $value): Condition
    {
        return self::lessThanOrEqualTo($field, $value);
    }

    /**
     * Create a not in condition.
     *
     * @param string $field
     * @param array  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function nin(string $field, array $value): Condition
    {
        return self::notIn($field, $value);
    }

    /**
     * Create a starts with condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function sw(string $field, $value): Condition
    {
        return self::startsWith($field, $value);
    }

    /**
     * Create an ends with condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function ew(string $field, $value): Condition
    {
        return self::endsWith($field, $value);
    }

    /**
     * Create a contains condition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function ct(string $field, $value): Condition
    {
        return self::contains($field, $value);
    }

    /**
     * Create a null condition.
     *
     * @param string $field
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function n(string $field): Condition
    {
        return self::null($field);
    }

    /**
     * Create a not null condition.
     *
     * @param string $field
     *
     * @return \Blixt\Persistence\Condition
     */
    public static function nn(string $field): Condition
    {
        return self::notNull($field);
    }
}
