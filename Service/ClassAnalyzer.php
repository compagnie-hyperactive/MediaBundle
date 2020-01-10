<?php

/**
 *
 * Created by PhpStorm.
 * User: bilel AZRI
 * Date: 10/01/2020
 * Time: 13:36
 */


namespace Lch\MediaBundle\Service;

use \ReflectionClass;
/**
 * Class ClassAnalyzer
 *
 * @package App\Helper
 */
class ClassAnalyzer
{
    /**
     * Return TRUE if the given object use the given trait, FALSE if not
     * @param ReflectionClass $class
     * @param string $traitName
     * @param boolean $isRecursive
     * @return bool
     */
    public function hasTrait(ReflectionClass $class, $traitName, $isRecursive = false)
    {
        if (in_array($traitName, $class->getTraitNames())) {
            return true;
        }
        $parentClass = $class->getParentClass();
        if ((false === $isRecursive) || (false === $parentClass) || (null === $parentClass)) {
            return false;
        }
        return $this->hasTrait($parentClass, $traitName, $isRecursive);
    }
    /**
     * Return TRUE if the given object has the given method, FALSE if not
     * @param ReflectionClass $class
     * @param string $methodName
     * @return bool
     */
    public function hasMethod(ReflectionClass $class, $methodName)
    {
        return $class->hasMethod($methodName);
    }
    /**
     * Return TRUE if the given object has the given property, FALSE if not
     * @param ReflectionClass $class
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty(ReflectionClass $class, $propertyName)
    {
        if ($class->hasProperty($propertyName)) {
            return true;
        }
        $parentClass = $class->getParentClass();
        if (false === $parentClass) {
            return false;
        }
        return $this->hasProperty($parentClass, $propertyName);
    }
}