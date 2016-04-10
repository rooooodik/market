<?php

namespace market\storage;

use market\dataProvider\IDataProvider;
use market\model\Region;
use market\storage\nestedSet\IFinder;
use market\storage\nestedSet\INestedSet;
use market\storage\validator\IValidator;

/**
 * Class Regions
 *
 * @package market\storage
 */
class Regions implements IFinder {

    /**
     * @var INestedSet
     */
    protected $storage;

    /**
     * Regions constructor.
     *
     * @param IDataProvider $regions
     * @param INestedSet $storage
     * @param IValidator $validator
     * @throws \Exception
     */
    public function __construct(
        IDataProvider $regions,
        INestedSet $storage,
        IValidator $validator
    ) {
        $this->storage = $storage;
        foreach ($regions->getData() as $region) {
            if ($validator->validate($region)) {
                $this->storage->add(
                    $region,
                    $region->getId(),
                    $region->getParentId()
                );
            } else {
                throw new \Exception($validator->getError());
            }
        }
    }

    /**
     * @param $id
     * @return Region
     */
    public function getRegion($id) {
        return $this->find($id);
    }

    /**
     * @param $id
     * @return Region
     */
    public function find($id) {
        return $this->storage->find($id);
    }

    /**
     * @param $parentId
     * @return array
     */
    public function findChildren($parentId) {
        return $this->storage->findChildren($parentId);
    }

}