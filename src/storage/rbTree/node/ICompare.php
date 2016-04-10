<?php

namespace market\storage\rbTree\node;

interface ICompare {
    /**
     * @param $recieved
     * @return mixed
     */
    public function compare($recieved);
}