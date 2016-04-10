<?php

namespace market\storage\validator;

/**
 * Class ObjectType
 *
 * @package market\storage\validator
 */
interface IValidator {

    /**
     * @param $value
     * @return mixed
     */
    public function validate($value);

    /**
     * @return string
     */
    public function getError();

}