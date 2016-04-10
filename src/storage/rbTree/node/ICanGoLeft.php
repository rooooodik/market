<?php

namespace market\storage\rbTree\node;

interface ICanGoLeft {
    /**
     * @param $value
     * @return mixed
     */
    public function canGoLeft($value);
}