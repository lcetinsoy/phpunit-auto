<?php

namespace phpunitauto;



class PraspelExample{
    

    /**     
     * @invariant age: boundinteger(0, 140);
     */    
    protected $age;
    
    /**     
     * @requires age: boundinteger(0, 140);
     * @ensures _result: integer();
     */
    public function setAge($age) {
        
    }
  
}
