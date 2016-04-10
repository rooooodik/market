<?php

namespace market\dataProvider;

/**
 * Interface DataProvider
 *
 * @package market\dataProvider
 */
interface IDataProvider
{
    /**
     * Returns data from resource
     *
     * @return \Generator
     */
    public function getData();

}