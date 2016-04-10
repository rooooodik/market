<?php

namespace market\storage;

/**
 * Class Fabric
 *
 * @package market\storage
 */
class Fabric {

    protected $reflectionClass;
    protected $params = [];

    /**
     * Fabric constructor.
     */
    public function __construct($className, $params = [])
    {
        $this->reflectionClass = new \ReflectionClass($className);
        $this->params = $params;
    }

    public function getInstance()
    {
        return $this->reflectionClass->newInstanceArgs($this->params);
    }

}