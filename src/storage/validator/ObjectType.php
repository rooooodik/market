<?php

namespace market\storage\validator;

/**
 * Class ObjectType
 *
 * @package market\storage\validator
 */
class ObjectType implements IValidator {

    protected $type;
    protected $error;

    /**
     * ObjectType constructor.
     *
     * @param $type
     */
    public function __construct($type) {
        $this->type = $type;
    }

    public function validate($object)
    {
        if ($object instanceof $this->type) {
            return true;
        } else {
            $this->error = 'Elements must be instance of ' . $this->type;
        }
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

}