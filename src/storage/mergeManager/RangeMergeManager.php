<?php

namespace market\storage\mergeManager;

use market\storage\nestedSet\IIndexed;

/**
 * Class RangeMergeManager
 *
 * @package market\storage\mergeManager
 */
class RangeMergeManager {

    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Adding without intersection
     *
     * @param $fields
     * @param IIndexed $levelNode
     */
    public function add($fields, IIndexed $levelNode) {
        $this->push($fields, $levelNode);
    }

    /**
     * @param $fields
     * @param IIndexed $levelNode
     * @param int $priority
     */
    protected function push($fields, IIndexed $levelNode, $priority = 0)
    {
        $this->projectionOnChild($fields, $levelNode, $priority);
        if (!empty($this->data[$levelNode->getId()])) {
            $this->merge($fields, $levelNode, $priority);
        } else {
            $this->data[$levelNode->getId()][$fields['S_from']] = $fields;
            $this->data[$levelNode->getId()][$fields['S_from']]['priority']
                = $priority;
        }
    }

    protected function projectionOnChild($fields, $region, $priority = 0) {
        $priority++;
        $childs = $this->regions->getSubregions($region['id']);
        if (!empty($childs)) {
            foreach ($childs as $child) {
                $this->push($fields, $child, $priority);
            }
        }
    }

    /**
     * @param $fields
     * @param $region
     * @param $priority
     */
    protected function merge($fields, $region, $priority)
    {
        $commit = 1;
        foreach ($this->regionPrices[$region['id']] as $dataFields) {
            if ($commit == 0) {
                $commit = 1;
            }
            if ($priority <= $dataFields['priority']) {
                $cost = $fields['Cost_Of_1_M2_RUR'];
            } else {
                $cost = $dataFields['Cost_Of_1_M2_RUR'];
            }

            if ($fields['S_to'] < $dataFields['S_from']) {
                $this->regionPrices[$region['id']][$fields['S_from']] = $fields;
                $this->regionPrices[$region['id']][$fields['S_from']]['priority'] = $priority;
                break;
            } elseif ($fields['S_from'] > $dataFields['S_to']) {
                $commit = 0;
                continue;
            } else {
                if ($fields['S_from'] < $dataFields['S_from']) {
                    $sub = 0;
                    if ($priority > $dataFields['priority']) {
                        $sub = -1;
                    }
                    $this->regionPrices[$region['id']][$fields['S_from']] = $fields;
                    $this->regionPrices[$region['id']][$fields['S_from']]['S_to'] = $dataFields['S_from'] - $sub;
                    $this->regionPrices[$region['id']][$fields['S_from']]['priority'] = $priority;

                    $fields['S_from'] = $dataFields['S_from'];
                }
                if ($fields['S_from'] >= $dataFields['S_from']) {
                    if ($fields['S_from'] > $dataFields['S_from']) {
                        $this->regionPrices[$region['id']][$dataFields['S_from']] = array(
                            'S_from' => $dataFields['S_from'],
                            'S_to' => $fields['S_from'],
                            'Cost_Of_1_M2_RUR' => $dataFields['Cost_Of_1_M2_RUR'],
                            'regionId' => $region['id'],
                            'priority' => $priority,
                        );
                    }
                    if ($fields['S_from'] < $dataFields['S_to']) {
                        if ($fields['S_to'] <= $dataFields['S_to']) {
                            $this->regionPrices[$region['id']][$fields['S_from']] = array(
                                'S_from' => $fields['S_from'],
                                'S_to' => $fields['S_to'],
                                'Cost_Of_1_M2_RUR' => $cost,
                                'regionId' => $region['id'],
                                'priority' => $priority,
                            );
                            if ($fields['S_to'] < $dataFields['S_to']) {
                                $this->regionPrices[$region['id']][$fields['S_to']] = array(
                                    'S_from' => $fields['S_to'],
                                    'S_to' => $dataFields['S_to'],
                                    'Cost_Of_1_M2_RUR' => $dataFields['Cost_Of_1_M2_RUR'],
                                    'regionId' => $region['id'],
                                    'priority' => $priority,
                                );
                            }
                            break;
                        } else {
                            $this->regionPrices[$region['id']][$fields['S_from']] = array(
                                'S_from' => $fields['S_from'],
                                'S_to' => $dataFields['S_to'],
                                'Cost_Of_1_M2_RUR' => $cost,
                                'regionId' => $region['id'],
                                'priority' => $priority,
                            );
                            $fields['S_from'] = $dataFields['S_to'];
                            $commit = 0;
                            continue;
                        }
                    }
                }
            }
        }
        if ($commit == 0) {
            $this->regionPrices[$region['id']][$fields['S_from']] = $fields;
            $this->regionPrices[$region['id']][$fields['S_from']]['priority'] = $priority;
        }
        asort($this->regionPrices[$region['id']]);
    }

}