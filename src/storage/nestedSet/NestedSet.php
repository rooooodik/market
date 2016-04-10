<?php

namespace market\storage\nestedSet;

/**
 * Class NestedSet
 *
 * @package market\storage\nestedSet
 */
class NestedSet implements INestedSet {

    /**
     * Indexed data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Array child by parent
     *
     * @var array
     */
    protected $indexByParent = [];

    /**
     * @param $data
     * @param $index
     * @param $parentIndex
     */
    public function add($data, $index, $parentIndex) {
        $this->data[$index] = $data;
        $this->indexByParent[$parentIndex][]
            = &$this->data[$index];
    }

    /**
     * @param $id
     * @return null
     */
    public function find($id) {
        if (!empty($this->data[$id])) {
            return $this->data[$id];
        } else {
            return null;
        }
    }

    /**
     * @param $parentId
     * @return null
     */
    public function findChildren($parentId) {
        if (!empty($this->indexByParent[$parentId])) {
            return $this->indexByParent[$parentId];
        } else {
            return null;
        }
    }

}