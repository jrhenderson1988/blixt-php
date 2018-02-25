<?php

namespace Blixt\Storage\Entities;

use Blixt\Storage\Entities\Concerns\BelongsToField;
use Blixt\Storage\Entities\Concerns\BelongsToTerm;

class Occurrence extends Entity
{
    use BelongsToField, BelongsToTerm;

    /**
     * @var int|null
     */
    protected $frequency;

    /**
     * Occurrence constructor.
     *
     * @param int|null|mixed $id
     * @param int|null|mixed $fieldId
     * @param int|null|mixed $termId
     * @param int|null|mixed $frequency
     */
    public function __construct($id = null, $fieldId = null, $termId = null, $frequency = null)
    {
        parent::__construct($id);

        $this->setFieldId($fieldId);
        $this->setTermId($termId);
        $this->setFrequency($frequency);
    }

    /**
     * @return int|null
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param int|null|mixed $frequency
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency !== null ? intval($frequency) : null;
    }

    /**
     * Fluent getter/setter for frequency.
     *
     * @param int|null|mixed $frequency
     *
     * @return $this|int|null
     */
    public function frequency($frequency = null)
    {
        if (func_num_args() === 0) {
            return $this->getFrequency();
        }

        $this->setFrequency($frequency);

        return $this;
    }

    /**
     * Create a new occurrence from the set of attributes given.
     *
     * @param array|object $attributes
     *
     * @return \Blixt\Storage\Entities\Occurrence
     */
    public static function make($attributes)
    {
        $occurrence = new static();

        foreach ((array) $attributes as $key => $value) {
            if (in_array($key, ['id', 'setId'])) {
                $occurrence->setId($value);
            } elseif (in_array($key, ['field_id', 'fieldId', 'setFieldId'])) {
                $occurrence->setFieldId($value);
            } elseif (in_array($key, ['term_id', 'termId', 'setTermId'])) {
                $occurrence->setTermId($value);
            } elseif (in_array($key, ['frequency' ,'setFrequency'])) {
                $occurrence->setFrequency($value);
            }
        }

        return $occurrence;
    }
}