<?php

namespace market\dataProvider;

/**
 * Class Fabric
 *
 * @package market\dataProvider
 */
class Fabric implements IDataProvider
{
    /**
     * @var IDataProvider
     */
    protected $dp;

    /**
     * Class for get instances
     *
     * @var IDataProvider
     */
    protected $className;

    /**
     * Fabric constructor.
     *
     * @param IDataProvider $dp
     * @param $className
     */
    public function __construct(IDataProvider $dp, $className)
    {
        $this->dp = $dp;
        $this->className = $className;
    }

    /**
     * Returns instances
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getData()
    {
        foreach ($this->dp->getData() as $data) {
            yield new $this->className($data);
        }
    }

}