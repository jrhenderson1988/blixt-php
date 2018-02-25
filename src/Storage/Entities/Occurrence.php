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
     * Mappings of the methods to sets of keys. That method will be used to set a property identified by one of the keys
     * when using the make method to create an instance of the entity.
     *
     * @return array
     */
    public static function getAttributeMappings()
    {
        return array_merge(parent::getAttributeMappings(), [
            'setFieldId' => ['field_id', 'fieldId', 'setFieldId'],
            'setTermId' => ['term_id', 'termId', 'setTermId'],
            'setFrequency' => ['frequency', 'setFrequency']
        ]);
    }
}