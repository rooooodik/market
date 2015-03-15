<?php

/**
 * Class Regions
 */
class Regions {


    const COUND_FIELDS = 4;

    protected $regions = array();
    protected $indexByParent = array();

    public function __construct($filepath)
    {
        if (!empty($filepath) && file_exists($filepath) && pathinfo($filepath, PATHINFO_EXTENSION) === "csv") {
            $row = 0;
            if (($handle = fopen($filepath, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                    $num = count($data);
                    if ($num != self::COUND_FIELDS) {
                        throw new Exception(
                            "String #".($row + 1)." contains $num fields, instead of ".
                            self::COUND_FIELDS . " in " . $filepath
                        );
                    }
                    $fields = array(
                        'name'        => $data[0],
                        'type'        => $data[1],
                        'id'          => $data[2],
                        'parentId'    => $data[3],
                    );
                    $this->regions[$fields['id']] = $fields;
                    $this->indexByParent[$fields['parentId']][] = &$this->regions[$fields['id']];
                    $row++;
                }
                fclose($handle);
            } else {
                throw new Exception('Could not open file' . $filepath);
            }
        } else {
            throw new Exception("File " . $filepath . " not found");
        }
    }

    public function getRegion($id) {
        if (!empty($this->regions[$id])) {
            return $this->regions[$id];
        } else {
            return null;
        }
    }

    public function getChilds($parentId) {
        if (!empty($this->indexByParent[$parentId])) {
            return $this->indexByParent[$parentId];
        } else {
            return null;
        }
    }

}