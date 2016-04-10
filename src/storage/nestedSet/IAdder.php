<?php

namespace market\storage\nestedSet;

/**
 * Interface IAdder
 *
 * @package market\storage\nestedSet
 */
interface IAdder
{
    /**
     * Add element to storage
     *
     * @param $data
     * @param $index
     * @param $parentIndex
     */
    public function add($data, $index, $parentIndex);

}