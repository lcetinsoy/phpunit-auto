<?php

namespace phpunitauto;

use Exception;
use Hoa\Math\Sampler\Random;
use Hoa\Praspel as HoaPraspel;
use Hoa\Praspel\Visitor\Compiler;
use Hoa\Realdom\Realdom;
use HoaPraspel\Model\Specification;
use ReflectionClass;
use ReflectionMethod;

/**
 * Generate PhpUnit Test from a class
 * using Praspel contract specifications
 * 
 */
class PhpUnitPraspelGenerator {

    /**
     * Compiler.
     *
     * @var Compiler
     */
    protected $_compiler = null;

    /**
     * Constructor.
     */
    public function __construct() {

        $this->_compiler = new HoaPraspel\Visitor\Compiler();

        return;
    }

    public function generateTestFile($class, $outputPath) {

        $code = $this->generateTestCode($class);
        
        file_put_contents($outputPath, $code);
    }

    public function generateTestCode($class) {

        $reflectionClass = new ReflectionClass($class);
        $sampler = new Random();
        Realdom::setDefaultSampler($sampler);

        return $this->generateTestClass($reflectionClass);
    }

    protected function generateTestClass(ReflectionClass $reflectionClass) {

        $tests = $this->generateTestMethods($reflectionClass);

        $fqn = $reflectionClass->getNamespaceName();
        $className = $reflectionClass->getName();
        $splits = explode('\\', $className);
        $className = $splits[count($splits) - 1];
        
        
        $strTests = join("\n", $tests);

$code = <<<EOT
<?php


use PHPUnit\Framework\TestCase;

class Test$className extends TestCase{

    $strTests
}
EOT;

        return $code;
    }

    protected function generateTestMethods(ReflectionClass $reflectionClass) {

        $tests = [];
        $className = '\\' . $reflectionClass->getName();

        /* @var $method ReflectionMethod */
        foreach ($reflectionClass->getMethods() as $method) {

            $contract = HoaPraspel\Praspel::extractFromComment(
                            $method->getDocComment()
            );

            if (empty($contract)) {
                continue;
            }

            if (!$method->isPublic()) {
                continue; // only public method tested
            }

            try {
                $specification = HoaPraspel\Praspel::interpret($contract, $className);
            } 
            catch (Exception $e) {
                echo $e->getMessage(), "\n";
                continue;
            }

            $tests[] = $this->generateTestMethodFromSpecification($reflectionClass, $method, $specification);
        }

        return $tests;
    }

    protected function generateTestMethodFromSpecification(
            ReflectionClass $reflectionClass,
            ReflectionMethod $method,
            HoaPraspel\Model\Specification $specification) 
    {
        

        $methodName = $method->getName();
        $className = $reflectionClass->getName();

        list($params,$paramInitialisation)  = $this->buildMethodArguments($specification);
        
        $constructorArgsNames = "";
        $constructorArgsDefinition = "";
        
        
        $assertionChecking = $this->buildOutputAssertion($specification);
        
        $code = <<<EOT
public function test$methodName(){
    
        $constructorArgsDefinition
        \$testedObject = new $className($constructorArgsNames);

        $paramInitialisation
        \$result = \$testedObject->$methodName($params);
                                                
        $assertionChecking
        
    }
EOT;

        return $code;
    }

    protected function buildMethodArguments(HoaPraspel\Model\Specification $specification) {

        $code = "";
        $names = [];
        $clause = $specification->getClause('requires');
        
        foreach ($clause->getIterator() as $variable) {
            
            $names[] = '$' .$variable->getName();
            
            $variableName = $variable->getName();
            $value = $variable->sample();
            $code = $code . "\$$variableName = $value;" . "\n";
        }
        
        $strNames = join(',', $names);
        
        
        return [$strNames, $code];
    }
    
    
    protected function buildOutputAssertion(HoaPraspel\Model\Specification $specification) {
        
        $code = "\$this->assertEquals(\$expected, \$result)";
        
        $clause = $specification->getClause('ensures');
                
        /* @var $variable class */
        foreach ($clause->getIterator() as $variable){
            echo $variable->getName();
        }  
        
        return $code;
    }
    
//
//    protected function managePropertyContracts(ReflectionClass $reflectionClass) {
//
//
//        $out = "";
//        $className = '\\' . $reflectionClass->getName();
//
//        foreach ($reflectionClass->getProperties() as $property) {
//
//            $propertyName = $property->getName();
//            $contract = HoaPraspel\Praspel::extractFromComment($property->getDocComment());
//            $id = $className . '::$' . $propertyName;
//
//            if (empty($contract)) {
//
//                continue;
//            }
//
//            try {
//                $specification = HoaPraspel\Praspel::interpret($contract);
//            } catch (Exception $e) {
//
//                echo $e->getMessage(), "\n";
//                continue;
//            }
//
//            if (false === $specification->clauseExists('invariant')) {
//                $out .= "\n" . $__ .
//                        '// Property ' . $propertyName . ' is not specificied' .
//                        ' (no @invariant).';
//
//                continue;
//            }
//
//            $registry[ltrim($id, '\\')] = $specification;
//
//            $out .= "\n" .
//                    $__ . '/**' . "\n" .
//                    $__ . ' * ' . str_replace(
//                            "\n",
//                            "\n" . $__ . ' * ',
//                            $contract
//                    ) . "\n" .
//                    $__ . ' */' .
//                    "\n" . $__ .
//                    '$registry[\'' . ltrim($className, '\\') . '::$' .
//                    $propertyName . '\'] = ' .
//                    str_replace(
//                            ["\n\n", "\n"],
//                            ["\n", "\n" . $__],
//                            $this->_compiler->visit($specification)
//                    ) .
//                    '$praspel->bindToClass(\'' . $className . '\');' . "\n";
//        }
//
//        echo $out;
//    }

}
