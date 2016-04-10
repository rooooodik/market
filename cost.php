<?php

require(__DIR__ . '/vendor/autoload.php');

$prices = new \market\storage\Price(
    new market\dataProvider\Fabric(
        new market\dataProvider\Filler(
            new market\dataProvider\Csv('tmp/cost.csv'),
            \market\model\Cost::getAttributes()
        ),
        \market\model\Cost::class
    ),
    new \market\storage\Regions(
        new market\dataProvider\Fabric(
            new market\dataProvider\Filler(
                new market\dataProvider\Csv('tmp/city.csv'),
                \market\model\Region::getAttributes()
            ),
            \market\model\Region::class
        ),
        new \market\storage\nestedSet\NestedSet(),
        new \market\storage\validator\ObjectType(\market\model\Region::class)
    ),
    \market\storage\rbTree\RbTree::class,
    new \market\storage\validator\ObjectType(\market\model\Cost::class),
    new \market\storage\mergeManager\Range()
);
$a = 1;
//$data = true;
//while ($data !== false && $data !== "exit") {
//    $data = trim(fgets(STDIN));
//    $data = explode(",", $data);
//    if (count($data) == 2) {
//        echo $prices->getPrice($data[0], $data[1]) . "\n";
//    } else {
//        if ($data[0] != "") {
//            echo "Неверно указаны значения\n";
//        }
//        $data = false;
//    }
//}