<?php

require_once __DIR__ . "/vendor/autoload.php";

use phpunitauto\ModelExample;
use phpunitauto\PhpDealReader;


$reader = new PhpDealReader();

$reader->getClassContracts(ModelExample::class);


