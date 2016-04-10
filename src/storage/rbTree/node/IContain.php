<?php

namespace market\storage\rbTree\node;

interface IContain {
    /**
     * @param $value
     * @return bool
     */
    public function contain ($value);
}