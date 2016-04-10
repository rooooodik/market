<?php

namespace market\model;

/**
 * Class Model
 *
 * @package market\model
 */
abstract class Model {

    /**
     * Attribute list for instance
     *
     * @var array
     */
    protected static $attributes = [];

    /**
     * Model constructor.
     *
     * @param $fields
     */
    public function __construct(\Iterator $fields)
    {
        foreach ($fields as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Returns attributes
     *
     * @return array
     */
    public static function getAttributes()
    {
        return static::$attributes;
    }
}