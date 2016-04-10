<?php

namespace market;

use market\dataProvider\IDataProvider;
use market\storage\Regions;

/**
 * Class PriceFinder
 *
 * @package market\dataProvider
 */
class PriceFinder {

    protected $regions = array();
    protected $regionPricesTrees = array();
    protected $regionPricesTreesData = array();

    /**
     * Prices constructor.
     *
     * @param IDataProvider $costs
     * @param Regions $regions
     */
    function __construct(IDataProvider $costs, Regions $regions) {
        $this->regions = $regions;
        $this->createRegionPricesTrees($costs);
        unset($this->regionPricesTreesData);
    }

    /**
     * Готовит данные к занесению их в дерево
     *
     * @param $fields
     * @throws \Exception
     */
    protected function prepareRegionPricesTreesData($fields) {
        $region = $this->regions->getRegion($fields['regionId']);
        if (empty($region)) {
            throw new \Exception("Region " . $fields['regionId'] . " not found");
        }
        $this->pushToData($fields, $region);
    }

    protected function projectionOnChild($fields, $region, $priority = 0) {
        $priority++;
        $childs = $this->regions->getSubregions($region['id']);
        if (!empty($childs)) {
            foreach ($childs as $child) {
                $this->pushToData($fields, $child, $priority);
            }
        }
    }

    /**
     * Заливает данные в массив regionPricesTreesData так чтобы они не пересекались
     * @param $fields
     * @param $priority
     * @param $region
     */
    protected function pushToData($fields, $region, $priority = 0)
    {
        //TODO: Переписать весь метод (вобще не тру)
        $this->projectionOnChild($fields, $region, $priority);
        if (!empty($this->regionPricesTreesData[$region['id']])) {
            $commit = 1;
            foreach ($this->regionPricesTreesData[$region['id']] as $dataFields) {
                if ($commit == 0) {
                    $commit = 1;
                }
                //Определим что будем делать при пересечении данных
                if ($priority < $dataFields['priority']) {
                    $cost = $fields['Cost_Of_1_M2_RUR'];
                } elseif ($priority > $dataFields['priority']) {
                    $cost = $dataFields['Cost_Of_1_M2_RUR'];
                } else {
                    $cost = $fields['Cost_Of_1_M2_RUR']; // Можно высчитывать среднее значения (решил присвоить значение находящиеся в файле ниже)
                }

                if ($fields['S_to'] < $dataFields['S_from']) {
                    $this->regionPricesTreesData[$region['id']][$fields['S_from']] = $fields;
                    $this->regionPricesTreesData[$region['id']][$fields['S_from']]['priority'] = $priority;
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
                        $this->regionPricesTreesData[$region['id']][$fields['S_from']] = $fields;
                        $this->regionPricesTreesData[$region['id']][$fields['S_from']]['S_to'] = $dataFields['S_from'] - $sub;
                        $this->regionPricesTreesData[$region['id']][$fields['S_from']]['priority'] = $priority;

                        $fields['S_from'] = $dataFields['S_from'];
                    }
                    if ($fields['S_from'] >= $dataFields['S_from']) {
                        if ($fields['S_from'] > $dataFields['S_from']) {
                            $this->regionPricesTreesData[$region['id']][$dataFields['S_from']] = array(
                                'S_from' => $dataFields['S_from'],
                                'S_to' => $fields['S_from'],
                                'Cost_Of_1_M2_RUR' => $dataFields['Cost_Of_1_M2_RUR'],
                                'regionId' => $region['id'],
                                'priority' => $priority,
                            );
                        }
                        if ($fields['S_from'] < $dataFields['S_to']) {
                            if ($fields['S_to'] <= $dataFields['S_to']) {
                                $this->regionPricesTreesData[$region['id']][$fields['S_from']] = array(
                                    'S_from' => $fields['S_from'],
                                    'S_to' => $fields['S_to'],
                                    'Cost_Of_1_M2_RUR' => $cost,
                                    'regionId' => $region['id'],
                                    'priority' => $priority,
                                );
                                if ($fields['S_to'] < $dataFields['S_to']) {
                                    $this->regionPricesTreesData[$region['id']][$fields['S_to']] = array(
                                        'S_from' => $fields['S_to'],
                                        'S_to' => $dataFields['S_to'],
                                        'Cost_Of_1_M2_RUR' => $dataFields['Cost_Of_1_M2_RUR'],
                                        'regionId' => $region['id'],
                                        'priority' => $priority,
                                    );
                                }
                                break;
                            } else {
                                $this->regionPricesTreesData[$region['id']][$fields['S_from']] = array(
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
                $this->regionPricesTreesData[$region['id']][$fields['S_from']] = $fields;
                $this->regionPricesTreesData[$region['id']][$fields['S_from']]['priority'] = $priority;
            }
            asort($this->regionPricesTreesData[$region['id']]);
        } else {
            $this->regionPricesTreesData[$region['id']][$fields['S_from']] = $fields;
            $this->regionPricesTreesData[$region['id']][$fields['S_from']]['priority'] = $priority;
        }
    }

    /**
     * Создает дерево RegionPricesTree на основании массива $regionPricesTreesData
     * @param $costs
     * @throws \Exception
     */
    protected function createRegionPricesTrees($costs) {
        $this->prepareRegionPricesTreesData($costs);
        foreach ($this->regionPricesTreesData as $key => $data) {
            $this->regionPricesTrees[$key] = new \RegionPricesTree();
            foreach ($data as $fields) {
                $this->regionPricesTrees[$key]->add($fields);
            }
        }
    }

    /**
     * Вычисляет стоимость площади
     * @param $regionId
     * @param $S
     * @return bool|string
     */
    public function getPrice($regionId, $S) {
        if (!empty($this->regionPricesTrees[$regionId])){
            $node = $this->regionPricesTrees[$regionId]->findValue($S);
            if ($node->getValue() == null) {
                return "false";
            }
            return $node->getValue()['Cost_Of_1_M2_RUR'] * $S;
        }
        return false;
    }

}