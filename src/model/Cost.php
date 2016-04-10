<?php

namespace market\model;

use market\storage\mergeManager\INestingIndex;
use market\storage\mergeManager\IRange;

/**
 * Class Cost
 *
 * @package market\model
 */
class Cost extends Model implements IRange, INestingIndex
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
     * @param array $fields
     * @param bool|true $validate
     * @throws \Exception
     */
    public function __construct(array $fields = [], $validate = true)
    {
        parent::__construct($fields);
        if ($validate && $this->sFrom >= $this->sTo) {
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
     * @param mixed $sFrom
     */
    public function setSFrom($sFrom)
    {
        $this->sFrom = $sFrom;
    }

    /**
     * @param mixed $sTo
     */
    public function setSTo($sTo)
    {
        $this->sTo = $sTo;
    }

    /**
     * @return mixed
     */
    public function getCostOfOneMRur()
    {
        return $this->costOfOneMRur;
    }

    /**
     * @param mixed $costOfOneMRur
     */
    public function setCostOfOneMRur($costOfOneMRur)
    {
        $this->costOfOneMRur = $costOfOneMRur;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    public function setTo($sTo)
    {
        return $this->setSTo($sTo);
    }

    public function getTo()
    {
        return $this->getSTo();
    }

    public function setFrom($sFrom)
    {
        return $this->setSFrom($sFrom);
    }

    public function getFrom()
    {
        return $this->getSFrom();
    }

    public function setValue($value)
    {
        $this->setCostOfOneMRur($value);
    }

    public function getValue()
    {
        $this->getCostOfOneMRur();
    }

    public function getNestingIndex()
    {
        return $this->getRegionId();
    }

    public function setNestingIndex($index)
    {
        $this->setRegionId($index);
    }


}