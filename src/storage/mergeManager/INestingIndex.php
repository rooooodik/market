<?php

namespace market\storage\mergeManager;

/**
 * Interface INestingIndex
 *
 * @package market\storage\mergeManager
 */
interface INestingIndex
{
    /**
     * @return mixed
     */
    public function getNestingIndex();

    /**
     * @return mixed
     */
    public function setNestingIndex($index);

}