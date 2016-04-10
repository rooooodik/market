<?php

namespace market\storage\rbTree\node\extantion;

use market\storage\rbTree\node\ICanGoLeft;
use market\storage\rbTree\node\ICompare;
use market\storage\rbTree\node\IContain;
use market\storage\rbTree\node\Node;

class Range implements ICanGoLeft, ICompare, IContain {

    protected $node;

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @param $recieved
     * @return int
     */
    public function compare($recieved)
    {
        if(
            $this->node->getValue()->getFrom()
            < $recieved->getFrom()
        ) {
            return -1;
        } elseif (
            $this->node->getValue()->getFrom()
            > $recieved->getFrom()
        ) {
            return 1;
        }
        return 0;
    }

    /**
     * Создержит ли данная нода значение
     * @param $value
     * @return bool
     */
    public function contain ($value) {
        if ($value <= $this->node->getValue()->getTo()) {
            return true;
        } else {
            return false;
        }
    }

    public function canGoLeft($value)
    {
        return $this->node->getValue()->getFrom() > $value;
    }
}