<?php

namespace market\model;

/**
 * Class Cost
 *
 * @package market\model
 */
class Cost extends Model
{
    protected $sFrom;
    protected $sTo;
    protected $costOfOneMRur;
    protected $regionId;

    protected static $attributes = [
        'sFrom',
        'sTo',
        'costOfOneMRur',
        'regionId',
    ];

    /**
     * Cost constructor.
     *
     * @param \Iterator $fields
     * @throws \Exception
     */
    public function __construct(\Iterator $fields)
    {
        parent::__construct($fields);
        if ($this->sFrom >= $this->sTo) {
            throw new \Exception(
                "sFrom must be >= than sTo"
            );
        }
    }

    /**
     * @return mixed
     */
    public function getSFrom()
    {
        return $this->sFrom;
    }

    /**
     * @return mixed
     */
    public function getSTo()
    {
        return $this->sTo;
    }

    /**
     * @return mixed
     */
    public function getCostOfOneMRur()
    {
        return $this->costOfOneMRur;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

}