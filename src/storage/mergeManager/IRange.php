<?php

namespace market\storage\mergeManager;

/**
 * Interface IRange
 *
 * @package market\storage\nestedSet
 */
interface IRange
{
    /**
     * @return mixed
     */
    public function getFrom();

    /**
     * @return mixed
     */
    public function getTo();

    /**
     * @param $from
     * @return mixed
     */
    public function setFrom($from);

    /**
     * @param $to
     * @return mixed
     */
    public function setTo($to);


    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return mixed
     */
    public function setValue($value);

}