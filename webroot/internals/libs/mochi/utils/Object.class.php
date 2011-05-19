<?php
require_once('StringUtils.class.php');

abstract class Object
{
	private $reflectionClass;
	
	final function getClass() {
		if (is_null($this->reflectionClass)) 
			$this->reflectionClass = new ReflectionClass($this);
		return $this->reflectionClass;
	}
	
	final function getClassName() {
		return $this->getClass()->getName();
	}
	
	final function supportsPrivateAccess() {
		$propertyClass = new ReflectionClass('ReflectionProperty');
		try {
			$method = $propertyClass->getMethod('setAccessible');
			return !is_null($method) ? TRUE : FALSE;
		}
		catch (ReflectionException $e) {
			return FALSE;
		}
	}
	
	
	// Properties
	
	final function getPropertyValues() {
		$values = array();
		$properties = $this->getClass()->getProperties();
		foreach ($properties as $property) {
			$property->setAccessible(TRUE);
			$values[$property->getName()] = $property->getValue($this);
		}
		return $values;
	}
	
	final function getProperty($propertyName) {
		try {
			return $this->getClass()->getProperty($propertyName);
		}
		catch (ReflectionException $e) {
			return NULL;
		}
	}
	
	final function setPublicPropertyValue($propertyName, $value) {
		$property = $this->getProperty($propertyName);
		if (is_null($property) || !$property->isPublic()) 
			return FALSE;
			
		$property->setValue($this, $value);
		return TRUE;
	}
	
	final function setPublicPropertyValues($values) {
		foreach ($values as $name => $value) 
			$this->setPublicPropertyValue($name, $value);
	}
	
	
	// Getter
	
	function __get($propertyName) {
		$getter = $this->getGetter($propertyName);
		if (is_null($getter)) 
			throw new AccessorNotFoundException('Getter not found: <' . $propertyName . '>');
		
		return $getter->invoke($this);
	}
	
	final function getGetter($propertyName) {
		$name = StringUtils::toUpperCaseFirstChar($propertyName);
		try {
			$method = $this->getClass()->getMethod('get' . $name);
		}
		catch (ReflectionException $e1) {
			try {
				$method = $this->getClass()->getMethod('is' . $name);
			}
			catch (ReflectionException $e2) { 
				return NULL; 
			}
		}
		
		if (!$method->isPublic()) return NULL;
		if (count($method->getParameters()) > 0) return NULL;
		
		return $method;
	}
	
	final function getValueViaGetter($propertyName) {
		$getter = $this->getGetter($propertyName);
		if (is_null($getter)) return NULL;
		
		return $getter->invoke($this);
	}
	
	final function getValuesViaGetters(array $excludes = array()) {
		$values = array();
		$methods = $this->getClass()->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $method) {
			if ($method->getDeclaringClass()->getName() === "Object") continue;
			
			$propertyName = self::getPropertyNameFromGetter($method);
			if (is_null($propertyName)) continue;
			if (in_array($propertyName, $excludes)) continue;
			
			$values[$propertyName] = $method->invoke($this);
		}
		return $values;
	}
	
	static function getPropertyNameFromGetter($method) {
		if (count($method->getParameters()) > 0) return NULL;
		
		$propertyName = NULL;
		$methodName = $method->getName();
		if (StringUtils::startsWith($methodName, "get"))
			$propertyName = substr($methodName, 3);
		if (StringUtils::startsWith($methodName, "is"))
			$propertyName = substr($methodName, 2);
			
		if (StringUtils::isEmpty($propertyName)) return NULL;
		
		return StringUtils::toLowerCaseFirstChar($propertyName);
	}
	
	
	// Setter
	
	function __set($propertyName, $value) {
		// The return value of __set() is ignored because of 
		// the way PHP processes the assignment operator. 
		if (!$this->setValueViaSetter($propertyName, $value)) {
			throw new AccessorNotFoundException('Setter not found: <' . $propertyName . '>');
		}
	}
	
	final function getSetter($propertyName) {
		$setterName = "set" . StringUtils::toUpperCaseFirstChar($propertyName);	
		try {
			$method = $this->getClass()->getMethod($setterName);
		}
		catch (ReflectionException $e) {
			return NULL;
		}
		
		if (!$method->isPublic()) return NULL;
		if (count($method->getParameters()) !== 1) return NULL;
		
		return $method;
	}
	
	final function setValueViaSetter($propertyName, $value) {
		$setter = $this->getSetter($propertyName);
		if (is_null($setter)) return FALSE;
		
		$setter->invoke($this, $value);
		return TRUE;
	}
	
	final function setValuesViaSetters($values) {
		foreach ($values as $name => $value) 
			$this->setValueViaSetter($name, $value);
	}
	
	
	// values (as default implementations)
	
	function toValues() {
		return $this->getValuesViaGetters();
	}
	
	function bindValues($values) {
		$this->setValuesViaSetters($values);
	}
	
	
	// toValueTree
	
	final function toValueTree() {
		$stack = array();
		return $this->_toValueTree($stack);
	}
	
	private function _toValueTree(&$stack) {
		array_push($stack, $this);
		$values = $this->getValuesViaGetters();
		foreach ($values as $name => &$value) {
			if ($value instanceof Object) {
				if (self::containsSameObject($stack, $value))
					unset($values[$name]);
				else
					$values[$name] = $value->_toValueTree($stack);
			}
			else if (is_array($value)) {
				$this->arrayToValues($value, $stack);
			}
		}
		array_pop($stack);
		return $values;
	}
	
	private function arrayToValues(&$array, &$stack) {
		foreach ($array as $key => &$value) {
			if ($value instanceof Object) {
				if (self::containsSameObject($stack, $value))
					unset($array[$key]);
				else {
					$array[$key] = $value->_toValueTree($stack);
				}
			}
			else if (is_array($value)) {
				arrayToValues($value, $stack);
			}
		}
	}
	
	static function containsSameObject(array $array, $object) {
		foreach ($array as $element) {
			if ($element === $object) return TRUE;
		}
		return FALSE;
	}
}

class AccessorNotFoundException extends Exception {}
?>
