<?php

declare(strict_types=1);

namespace ProxyManager\ProxyGenerator\LazyLoadingValueHolder\MethodGenerator;

use ProxyManager\Generator\MagicMethodGenerator;
use ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Zend\Code\Generator\Exception\InvalidArgumentException;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

/**
 * Magic `__unset` method for lazy loading value holder objects
 *
 */
class MagicUnset extends MagicMethodGenerator
{
    /**
     * Constructor
     *
     *
     * @throws InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(
        ReflectionClass $originalClass,
        PropertyGenerator $initializerProperty,
        PropertyGenerator $valueHolderProperty,
        PublicPropertiesMap $publicProperties
    ) {
        parent::__construct($originalClass, '__unset', [new ParameterGenerator('name')]);

        $hasParent   = $originalClass->hasMethod('__unset');
        $initializer = $initializerProperty->getName();
        $valueHolder = $valueHolderProperty->getName();
        $callParent  = '';

        if (! $publicProperties->isEmpty()) {
            $callParent = 'if (isset(self::$' . $publicProperties->getName() . "[\$name])) {\n"
                . '    unset($this->' . $valueHolder . '->$name);' . "\n\n    return;"
                . "\n}\n\n";
        }

        $callParent .= $hasParent
            ? 'return $this->' . $valueHolder . '->__unset($name);'
            : PublicScopeSimulator::getPublicAccessSimulationCode(
                PublicScopeSimulator::OPERATION_UNSET,
                'name',
                null,
                $valueHolderProperty
            );

        $this->setBody(
            '$this->' . $initializer . ' && $this->' . $initializer
            . '->__invoke($this->' . $valueHolder . ', $this, \'__unset\', array(\'name\' => $name), $this->'
            . $initializer . ');' . "\n\n" . $callParent
        );
    }
}
