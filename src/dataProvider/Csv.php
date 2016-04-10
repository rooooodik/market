<?php

namespace market\dataProvider;

/**
 * Class Csv
 */
class Csv implements IDataProvider
{
    /**
     * Path to file
     *
     * @var string
     */
    protected $filepath;

    /**
     * Csv constructor.
     *
     * @param $filepath
     * @throws \Exception
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
        if (!$this->isExists()) {
            throw new \Exception("File " . $filepath . " not found");
        }
    }

    /**
     * Check file exists
     *
     * @return bool
     */
    protected function isExists()
    {
        return !empty($this->filepath)
        && file_exists($this->filepath)
        && pathinfo($this->filepath, PATHINFO_EXTENSION) === "csv";
    }

    /**
     * Returns data from resource
     *
     * @return \Generator
     * @throws \Exception
     */
    public function getData()
    {
        $row = 0;
        if (($handle = fopen($this->filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                yield $data;
                $row++;
            }
            fclose($handle);
        } else {
            throw new \Exception('Could not open file' . $this->filepath);
        }
    }

}