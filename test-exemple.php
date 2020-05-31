<?php


use PHPUnit\Framework\TestCase;

class TestPraspelExample extends TestCase{

    public function testsetAge(){
    
        
        $testedObject = new phpunitauto\PraspelExample();

        $age = 47;

        $result = $testedObject->setAge($age);
                                                
        $this->assertEquals($expected, $result)
        
    }
}