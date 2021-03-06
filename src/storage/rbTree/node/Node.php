<?php

namespace market\storage\rbTree\node;

use market\storage\Fabric;

class Node implements ICanGoLeft, ICompare, IContain {

    const RED = "R";
    const BLACK = "B";

    private $left;
    private $right;
    private $color;
    private $value;
    private $parent;

    private $extantion;

    /**
     * Node constructor.
     *
     * @param null $value
     * @param null $color
     * @param Fabric $extantionFabric
     */
    public function __construct(
        $value = null,
        $color = null,
        Fabric $extantionFabric
    ) {
        $this->value = $value;
        $this->color = $color;
        $this->extantion = $extantionFabric->getInstance();
        $this->extantion->setNode($this);
    }

    /**
     * @return mixed
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param mixed $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return mixed
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param mixed $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param null $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    public function isFree() {
        return $this->value == null;
    }

    public function isLeftFree() {
        return $this->left == null;
    }

    public function isRightFree() {
        return $this->right == null;
    }

    public function isParentFree() {
        return $this->parent == null;
    }

    public function isBlack() {
        return $this->color == self::BLACK;
    }

    public function makeBlack() {
        $this->color = self::BLACK;
    }

    public function isRed() {
        return $this->color == self::RED;
    }

    public function makeRed() {
        $this->color = self::RED;
    }

    /**
     * Возвращет "дедушку" узла дерева.
     * @return Node
     */
    public function getGrandfather() {
        if($this->parent != null) {
            return $this->parent->parent;
        }
        return null;
    }

    /**
     * Возвращает "дядю" узла дерева.
     * @return Node
     */
    public function getUncle() {
        $grand = $this->getGrandfather();
        if($grand != null) {
            if($grand->left == $this->parent) {
                return $grand->right;
            } elseif ($grand->right == $this->parent)
                return $grand->left;
        }
        return null;
    }

    /**
     * Возвращает следующий по значению узел дерева.
     */
    public function getSuccessor()
    {
        $temp = null;
        $node = $this;
        if(!$node->isRightFree()) {
            $temp = $node->getRight();
            while(!$temp->isLeftFree()) {
                $temp = $temp->getLeft();
            }
            return $temp;
        }
        $temp = $node->getParent();
        while($temp != null && $node == $temp->getRight()) {
            $node = $temp;
            $temp = $temp->getParent();
        }
        return $temp;
    }

    public function getColorName() {
        return (($this->isBlack()) ? "B" : "R");
    }

    /**
     * @param $recieved
     * @return mixed
     */
    public function compare($recieved)
    {
        return $this->extantion->compare($recieved);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function contain($value) {
        return $this->extantion->contain($value);
    }

    /**
     * @param $value
     * @return bool
     */
    public function canGoLeft($value)
    {
        return $this->extantion->canGoLeft($value);
    }
}