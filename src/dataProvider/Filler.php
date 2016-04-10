<?php

namespace market\dataProvider;

/**
 * Class Filler
 *
 * @package market\dataProvider
 */
class Filler implements IDataProvider
{
    /**
     * @var IDataProvider
     */
    protected $dp;

    /**
     * Names for result assoc array
     *
     * @var IDataProvider
     */
    protected $fields;

    /**
     * Filler constructor.
     *
     * @param IDataProvider $dp
     * @param array $fields
     */
    public function __construct(IDataProvider $dp, array $fields)
    {
        $this->dp = $dp;
        $this->fields = $fields;
    }

    /**
     * Returns assoc array
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getData()
    {
        $row = 0;
        $cFields = count($this->fields);
        foreach ($this->dp->getData() as $data) {
            $cData = count($data);
            if ($cFields != $cData) {
                throw new \Exception(
                    "Data contains " . $cData . " fields, instead of ".
                    $cFields . " iteration #" . $row
                );
            }
            yield array_combine($this->fields, $data);
            $row++;
        }
    }

}