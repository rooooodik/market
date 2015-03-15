<?php

require_once "Prices.php";
require_once "Regions.php";

$regions = new Regions("tmp/city.csv");
$prices = new Prices("tmp/cost.csv", $regions);

$data = true;
while ($data !== false && $data !== "exit") {
    $data = trim(fgets(STDIN));
    $data = explode(",", $data);
    if (count($data) == 2) {
        echo $prices->getPrice($data[0], $data[1]) . "\n";
    } else {
        if ($data[0] != "") {
            echo "Неверно указаны значения\n";
        }
        $data = false;
    }
}