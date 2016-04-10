<?php

namespace market\storage;

use market\dataProvider\IDataProvider;
use market\model\Cost;
use market\storage\mergeManager\Range;
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
     * @var Range
     */
    protected $mergeManager;

    /**
     * Price constructor.
     *
     * @param IDataProvider $costs
     * @param Regions $regions
     * @param $storageClass
     * @param IValidator $validator
     * @param Range $mergeManager
     * @throws \Exception
     */
    function __construct(
        IDataProvider $costs,
        Regions $regions,
        $storageClass,
        IValidator $validator,
        Range $mergeManager
    ) {
        $this->mergeManager = $mergeManager;
        $this->mergeManager->setNestedSet($regions);
        $this->storageClass = $storageClass;
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
     * Create storage by merge manager
     */
    protected function createStorage() {
        foreach ($this->mergeManager->getData() as $key => $data) {
            $this->storage[$key] = new $this->storageClass();
            foreach ($data as $cost) {
                $this->storage[$key]->add($cost);
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
            return $node->getValue()->getValue() * $s;
        }
        return false;
    }

}