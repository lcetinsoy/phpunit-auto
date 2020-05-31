<?php


namespace phpunitauto;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpDeal\Annotation\Invariant;
use PhpDeal\Contract\Fetcher\Parent\InvariantFetcher;
use ReflectionClass;



class PhpDealReader{


    public function getClassContracts($class){

        $reflectionClass = new ReflectionClass($class);
        
        AnnotationRegistry::registerLoader('class_exists');
       
        $reader = new AnnotationReader();
        
        $annotations = $reader->getClassAnnotations($reflectionClass);
        dump($annotations);
        
        $fetcher = new InvariantFetcher([Invariant::class], $reader);
        
        $allContracts = $fetcher->getConditions($reflectionClass);
        foreach ($reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof Invariant) {
                $allContracts[] = $annotation;
            }
        }
        
        
        
        dump($allContracts);
        
        
        
    }

}


