<?php

namespace market\storage\nestedSet;

/**
 * Interface IFinder
 *
 * @package market\storage\nestedSet
 */
interface IFinder
{
    /**
     * Returns data by id
     *
     * @param $id
     * @return null
     */
    public function find($id);

    /**
     * Returns children by parent id
     *
     * @param $parentId
     * @return mixed
     */
    public function findChildren($parentId);

}