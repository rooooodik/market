<?php

namespace market\storage;

use market\dataProvider\IDataProvider;
use market\model\Cost;
use market\storage\mergeManager\RangeMergeManager;
use market\storage\validator\IValidator;

/**
 * Class Price
 *
 * @package market\storage
 */
class Price {

    /**
     * @var Regions
     */
    protected $regions;

    /**
     * @var string
     */
    protected $storageClass;

    /**
     * @var array
     */
    protected $storage;

    /**
     * @var RangeMergeManager
     */
    protected $mergeManager;

    /**
     * Price constructor.
     *
     * @param IDataProvider $costs
     * @param Regions $regions
     * @param $storageClass
     * @param IValidator $validator
     * @param RangeMergeManager $mergeManager
     * @throws \Exception
     */
    function __construct(
        IDataProvider $costs,
        Regions $regions,
        $storageClass,
        IValidator $validator,
        RangeMergeManager $mergeManager
    ) {
        $this->mergeManager = $mergeManager;
        $this->regions = $regions;
        foreach ($costs->getData() as $cost) {
            if ($validator->validate($cost)) {
                $this->add($cost);
            } else {
                throw new \Exception($validator->getError());
            }
        }
        $this->createStorage();
    }

    /**
     * @param Cost $cost
     * @throws \Exception
     */
    protected function add(Cost $cost) {

        $region = $this->regions->getRegion($cost->getRegionId());
        if (empty($region)) {
            throw new \Exception(
                "Region " . $cost->getRegionId() . " not found"
            );
        }
        $this->mergeManager->add($cost, $region);
    }

    /**
     * Создает дерево RegionPricesTree на основании массива $regionPricesTreesData
     */
    protected function createStorage() {
        foreach ($this->mergeManager->getData() as $key => $data) {
            $this->storage[$key] = new $this->storageClass();
            foreach ($data as $fields) {
                $this->storage[$key]->add($fields);
            }
        }
    }

    /**
     * Calculate price
     *
     * @param $regionId
     * @param $s
     * @return bool|string
     */
    public function getPrice($regionId, $s) {
        if (!empty($this->storage[$regionId])){
            $node = $this->storage[$regionId]->findValue($s);
            if ($node->getValue() == null) {
                return "false";
            }
            return $node->getValue()['Cost_Of_1_M2_RUR'] * $s;
        }
        return false;
    }

}