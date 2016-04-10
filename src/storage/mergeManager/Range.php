<?php

namespace market\storage\mergeManager;

use market\storage\nestedSet\IFinder;
use market\storage\nestedSet\INestedSet;

/**
 * Class Range
 *
 * @package market\storage\mergeManager
 */
class Range {

    /**
     * @var array
     */
    protected $data;

    /**
     * @var INestedSet
     */
    protected $nestedSet;

    /**
     * @var mixed
     */
    protected $objectClass;

    public function getData()
    {
        foreach ($this->data as $index => $dataInIndex) {
            yield $index => $this->getDataByIndex($index);
        }
    }

    /**
     * @param $index
     * @return \Generator
     */
    public function getDataByIndex($index)
    {
        foreach($this->data[$index] as $node) {
            yield $node['object'];
        }
    }

    /**
     * @param IFinder $nestedSet
     */
    public function setNestedSet(IFinder $nestedSet)
    {
        $this->nestedSet = $nestedSet;
    }

    /**
     * Adding without intersection
     *
     * @param IRange $object
     * @param IIndexed $nestedSetNode
     */
    public function add(IRange $object, IIndexed $nestedSetNode)
    {
        $this->objectClass = get_class($object);
        $this->push(clone $object, $nestedSetNode);
    }

    /**
     * @param IRange $object
     * @param IIndexed $nestedSetNode
     * @param int $priority
     */
    protected function push(
        IRange $object,
        IIndexed $nestedSetNode,
        $priority = 0
    ) {
        $this->projectionOnChild(clone $object, $nestedSetNode, $priority);
        if (!empty($this->data[$nestedSetNode->getId()])) {
            $this->merge(clone $object, $nestedSetNode, $priority);
        } else {
            $this->normalAdding(clone $object, $nestedSetNode, $priority);
        }
    }

    /**
     * @param IRange $object
     * @param IIndexed $nestedSetNode
     * @param int $priority
     */
    protected function projectionOnChild(
        IRange $object,
        IIndexed $nestedSetNode,
        $priority = 0
    ) {
        $priority++;
        $children = $this->nestedSet->findChildren($nestedSetNode->getId());
        if (!empty($children)) {
            foreach ($children as $child) {
                $this->push(clone $object, $child, $priority);
            }
        }
    }

    /**
     * @param IRange $object
     * @param IIndexed $nestedSetNode
     * @param $priority
     */
    protected function merge(IRange $object, IIndexed $nestedSetNode, $priority)
    {
        $commit = 1;
        foreach ($this->data[$nestedSetNode->getId()] as $dataNode) {
            if ($commit == 0) {
                $commit = 1;
            }
            $value = $this->getPriorityValue($object, $dataNode, $priority);

            if ($object->getTo() < $dataNode['object']->getFrom()) {
                $this->normalAdding($object, $nestedSetNode, $priority);
                break;
            } elseif ($object->getFrom() > $dataNode['object']->getTo()) {
                $commit = 0;
                continue;
            } else {
                if ($object->getFrom() < $dataNode['object']->getFrom()) {
                    $sub = 0;
                    if ($priority > $dataNode['priority']) {
                        $sub = -1;
                    }
                    $this->data[$nestedSetNode->getId()][$object->getFrom()]
                        ['object'] = clone $object;
                    $this->data[$nestedSetNode->getId()][$object->getFrom()]
                        ['object']->setTo(
                            $dataNode['object']->getFrom() - $sub
                        );
                    $this->data[$nestedSetNode->getId()][$object->getFrom()]
                        ['priority'] = $priority;

                    $object->setFrom($dataNode['object']->getFrom());
                }
                if ($object->getFrom() >= $dataNode['object']->getFrom()) {
                    if ($object->getFrom() > $dataNode['object']->getFrom()) {
                        $this->data[$nestedSetNode->getId()][$dataNode['object']
                            ->getFrom()] = $this->newNode(
                            $dataNode['object']->getFrom(),
                            $object->getFrom(),
                            $dataNode['object']->getValue(),
                            $nestedSetNode->getId(),
                            $priority
                        );
                    }
                    if ($object->getFrom() < $dataNode['object']->getTo()) {
                        if ($object->getTo() <= $dataNode['object']->getTo()) {
                            $this->data[$nestedSetNode->getId()]
                            [$object->getFrom()] = $this->newNode(
                                $object->getFrom(),
                                $object->getTo(),
                                $value,
                                $nestedSetNode->getId(),
                                $priority
                            );
                            if (
                                $object->getTo() < $dataNode['object']->getTo()
                            ) {
                                $this->data[$nestedSetNode->getId()]
                                [$object->getTo()] = $this->newNode(
                                    $object->getTo(),
                                    $dataNode['object']->getTo(),
                                    $dataNode['object']->getValue(),
                                    $nestedSetNode->getId(),
                                    $priority
                                );
                            }
                            break;
                        } else {
                            $this->data[$nestedSetNode->getId()]
                                [$object->getFrom()] = $this->newNode(
                                $object->getFrom(),
                                $dataNode['object']->getTo(),
                                $value,
                                $nestedSetNode->getId(),
                                $priority
                            );
                            $object->setFrom($dataNode['object']->getTo());
                            $commit = 0;
                            continue;
                        }
                    }
                }
            }
        }
        if ($commit == 0) {
            $this->normalAdding($object, $nestedSetNode, $priority);
        }
        asort($this->data[$nestedSetNode->getId()]);
    }

    /**
     * @param IRange $object
     * @param $dataNode
     * @param $priority
     * @return mixed
     */
    protected function getPriorityValue(
        IRange $object,
        $dataNode,
        $priority
    ) {
        if ($priority <= $dataNode['priority']) {
            $result = $object->getValue();
        } else {
            $result = $dataNode['object']->getValue();
        }
        return $result;
    }

    /**
     * @param IRange $object
     * @param IIndexed $nestedSetNode
     * @param $priority
     */
    protected function normalAdding(
        IRange $object,
        IIndexed $nestedSetNode,
        $priority
    ) {
        $this->data[$nestedSetNode->getId()]
            [$object->getFrom()]['object'] = clone $object;
        $this->data[$nestedSetNode->getId()]
            [$object->getFrom()]['priority'] = $priority;
    }

    protected function newNode($from, $to, $value, $nestingIndex, $priority)
    {
        $newObject = new $this->objectClass([], false);
        $newObject->setFrom($from);
        $newObject->setTo($to);
        $newObject->setValue($value);
        $newObject->setNestingIndex($nestingIndex);
        $node['object'] = $newObject;
        $node['priority'] = $priority;
        return $node;
    }

}