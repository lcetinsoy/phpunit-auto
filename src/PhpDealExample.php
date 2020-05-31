<?php

namespace phpunitauto;

use PhpDeal\Annotation as Contract; //import DbC annotations


/**
 * @Contract\Invariant("$this->age > 0")
 * @Contract\Invariant("$this->age < 130")
 */
class PhpDealExample{
    
    
    /**     
     * @var integer 
     */
    protected $age;
    
    
    /**     
     * @var string 
     */
    protected $name;
    
}