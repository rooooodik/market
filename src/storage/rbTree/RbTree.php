<?php

namespace market\storage\rbTree;

use market\storage\Fabric;
use market\storage\rbTree\node\Node;

class RbTree {

    /**
     * Корень дерева.
     */
    private $root;
    /**
     * Ограничитель, который обозначает нулевую ссылку.
     */
    private $nil;

    /**
     * Ссылка на элемент на который указывает итератор.
     */
    private $current;

    private $isRemoved;

    private $nodeFabric;

    function __construct(Fabric $nodeFabric)
    {
        $this->nodeFabric = $nodeFabric;
        $this->node = $this->nodeFabric->getInstance();
        $this->root = $this->nodeFabric->getInstance();
        $this->nil = $this->nodeFabric->getInstance();
        $this->nil->setColor(Node::BLACK);
        $this->root->setParent($this->nil);
        $this->root->setRight($this->nil);
        $this->root->setLeft($this->nil);
        $this->isRemoved = false;
    }

    /**
     * Левый поворот дерева tree относительно узла node.
     * @param RbTree $tree
     * @param Node $node
     */
    private static function leftRotate(RbTree $tree, Node $node) {
        $nodeParent = $node->getParent();
        $nodeRight = $node->getRight();
        if($nodeParent != $tree->nil) {
            if($nodeParent->getLeft() == $node) {
                $nodeParent->setLeft($nodeRight);
            } else {
                $nodeParent->setRight($nodeRight);
            }
        } else {
            $tree->root = $nodeRight;
            $tree->root->setParent($tree->nil);
        }
        $node->setRight($nodeRight->getLeft());
        $nodeRight->setLeft($node);
    }

    /**
     * Правый поворот дерева tree относительно узла node.
     * @param RbTree $tree
     * @param Node $node
     */
    private static function rightRotate(RbTree $tree, Node $node) {
        $nodeParent = $node->getParent();
        $nodeLeft = $node->getLeft();
        if($nodeParent != $tree->nil) {
            if($nodeParent->getLeft() == $node) {
                $nodeParent->setLeft($nodeLeft);
            } else {
                $nodeParent->setRight($nodeLeft);
            }
        } else {
            $tree->root = $nodeLeft;
            $tree->root->setParent($tree->nil);
        }
        $node->setLeft($nodeLeft->getRight());
        $nodeLeft->setRight($node);
    }

    /**
     * Добавление значения
     * @param $value
     */
    public function add($value) {
        $node = $this->root;
        $temp = $this->nil;
        $newNode = $this->nodeFabric->getInstance();
        $newNode->setValue($value);
        $newNode->setColor(Node::RED);
        while($node != null && $node != $this->nil && !$node->isFree()) {
            $temp = $node;
            if ( $newNode->compare($node->getValue()) < 0) {
                $node = $node->getLeft();
            } else {
                $node = $node->getRight();
            }
        }
        $newNode->setParent($temp);
        if($temp == $this->nil) {
            $this->root->setValue($newNode->getValue());
        } else {
            if( $newNode->compare($temp->getValue()) < 0)
                $temp->setLeft($newNode);
            else
                $temp->setRight($newNode);
        }
        $newNode->setLeft($this->nil);
        $newNode->setRight($this->nil);
        $this->fixInsert($newNode);
    }

    /**
     * Исправление для сохранения свойств
     * @param Node $node
     */
    private function fixInsert(Node $node) {
        while( !$node->isParentFree() && $node->getParent()->isRed() ) {
            if($node->getParent() == $node->getGrandfather()->getLeft()) {
                $temp = $node->getGrandfather()->getRight();
                if($temp->isRed()) {
                    $temp->makeBlack();
                    $node->getParent()->makeBlack();
                    $node->getGrandfather()->makeRed();
                    $node = $node->getGrandfather();
                } else {
                    if($node == $node->getParent()->getRight()) {
                        $node = $node->getParent();
                        $this->leftRotate($this, $node);
                    }
                    $node->getParent()->makeBlack();
                    $node->getGrandfather()->makeRed();
                    $this->rightRotate($this, $node->getGrandfather());
                }
            } else {
                $temp = $node->getGrandfather()->getLeft();
                if ($temp->isRed()) {
                    $temp->makeBlack();
                    $node->getParent()->makeBlack();
                    $node->getGrandfather()->makeRed();
                    $node = $node->getGrandfather();
                } else {
                    if($node == $node->getParent()->getLeft()) {
                        $node = $node->getParent();
                        $this->rightRotate($this, $node);
                    }
                    $node->getParent()->makeBlack();
                    $node->getGrandfather()->makeRed();
                    $this->leftRotate($this, $node->getGrandfather());
                }
            }
        }
        $this->root->makeBlack();
    }

    /**
     * Поиск в дереве
     * @param $value
     * @return null|Node
     */
    public function findValue($value) {
        $node = $this->root;
        while($node != null && $node != $this->nil && !$node->contain($value)) {
            if( $node->canGoLeft($value) ) {
                $node = $node->getLeft();
            } else {
                $node = $node->getRight();
            }
        }
        return $node;
    }

}