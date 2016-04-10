<?php

namespace market\model;

use market\storage\nestedSet\IIndexed;

/**
 * Class Region
 *
 * @package market\model
 */
class Region extends Model implements IIndexed
{
    protected $name;
    protected $type;
    protected $id;
    protected $parentId;

    protected static $attributes = [
        'name',
        'type',
        'id',
        'parentId',
    ];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

}