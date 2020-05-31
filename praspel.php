<?php

require_once __DIR__ . "/vendor/autoload.php";

use phpunitauto\PhpUnitPraspelGenerator;
use phpunitauto\PraspelExample;


$reader = new PhpUnitPraspelGenerator();

$reader->generateTestFile(PraspelExample::class, "./test-exemple.php");



