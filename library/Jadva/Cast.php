<?php

class Jadva_Cast
{
	public static $checkAll = FALSE;

	public static function _(&$value, $toUserType)
	{
		self::_checkUserTypeExists($toUserType);
		$toBaseType = self::$_userTypeMap[$toUserType];

		if( is_object($value) ) {
			$fromType = get_class($value);
		} else {
			$fromType = gettype($value);
		}
		self::_checkCastKnown($fromType, $toUserType);

		$info = self::$_castMap[$toUserType][$fromType];

		switch($info['type']) {
		case 'property':
			$return = $value->$info['property'];
			break;
		case 'method':
			$return = $value->{$info['method']}();
			break;
		}

		switch($toBaseType) {
		case 'boolean':
			return (boolean) $return;
		case 'float':
			return (float) $return;
		case 'integer':
			return (integer) $return;
		case 'string':
			return (string) $return;
		}

		/** @see Jadva_Cast_Exception */
		require_once 'Jadva/Cast/Exception.php';
		throw new Jadva_Cast_Exception('Internal error: Missing cast for original type');
	}

	public static function _R($toUserType, &$value)
	{
		return self::_($value, $toUserType);
	}

	public static function registerType($name, $baseType)
	{
		//Map double names
		switch($baseType) {
		case 'int': $baseType = 'integer'; break;
		case 'bool': $baseType = 'boolean'; break;
		case 'double': $baseType = 'float'; break;
		}

		self::_checkBaseType($baseType);

		self::$_userTypeMap[$name] = $baseType;
		self::$_castMap[$name] = array();
	}

	public static function registerMethod($toBaseType, $class, $method)
	{
		self::_checkUserTypeExists($toBaseType);
		self::_checkClass($class, 'method', $method);

		self::$_castMap[$toBaseType][$class] = array(
			'type' => 'method',
			'class' => $class,
			'method' => $method,
		);
	}

	public static function registerProperty($toBaseType, $class, $property)
	{
		self::_checkUserTypeExists($toBaseType);
		self::_checkClass($class, 'property', $property);

		self::$_castMap[$toBaseType][$class] = array(
			'type' => 'property',
			'class' => $class,
			'property' => $property,
		);
	}

	public static function clear()
	{
		self::$_userTypeMap = array(
			'boolean' => 'boolean',
			'float'   => 'float',
			'integer' => 'integer',
			'string'  => 'string',
		);

		self::$_castMap = array(
			'boolean' => array(),
			'float'   => array(),
			'integer' => array(),
			'string'  => array(),
		);
	}

	protected static $_userTypeMap = array(
		'boolean' => 'boolean',
		'float'   => 'float',
		'integer' => 'integer',
		'string'  => 'string',
	);

	protected static $_castMap = array(
		'boolean' => array(),
		'float'   => array(),
		'integer' => array(),
		'string'  => array(),
	);

	protected static function _checkUserTypeExists($userType)
	{
		if( !self::$checkAll ) return;

		if( !array_key_exists($userType, self::$_userTypeMap) ) {
			/** @see Jadva_Cast_Exception_NoSuchUserType */
			require_once 'Jadva/Cast/Exception/NoSuchUserType.php';
			throw new Jadva_Cast_Exception_NoSuchUserType($userType);
		}
	}

	protected static function _checkBaseType($baseType)
	{
		if( !self::$checkAll ) return;

		switch($baseType) {
		case 'boolean':
		case 'float':
		case 'integer':
		case 'string':
			break;
		default:
			/** @see Jadva_Cast_Exception_NoSuchBaseType */
			require_once 'Jadva/Cast/Exception/NoSuchBaseType.php';
			throw new Jadva_Cast_Exception_NoSuchBaseType($baseType);
		}
	}

	protected static function _checkClass($className, $checkType, $checkValue)
	{
		if( !self::$checkAll ) return;

		if( !class_exists($className, FALSE) ) {
			/** @see Jadva_Cast_Exception_NoSuchClass */
			require_once 'Jadva/Cast/Exception/NoSuchClass.php';
			throw new Jadva_Cast_Exception_NoSuchClass($className);
		}

		switch($checkType) {
		case 'method':
			if( !method_exists($className, $checkValue) ) {
				/** @see Jadva_Cast_Exception_NoSuchClassMethod */
				require_once 'Jadva/Cast/Exception/NoSuchClassMethod.php';
				throw new Jadva_Cast_Exception_NoSuchClassMethod($checkValue);
			}
			break;
		case 'property':
			if( !property_exists($className, $checkValue) ) {
				/** @see Jadva_Cast_Exception_NoSuchClassProperty */
				require_once 'Jadva/Cast/Exception/NoSuchClassProperty.php';
				throw new Jadva_Cast_Exception_NoSuchClassProperty($checkValue);
			}
		}
	}

	protected static function _checkCastKnown($fromType, $toBaseType)
	{
		if( !array_key_exists($fromType, self::$_castMap[$toBaseType]) ) {
			/** @see Jadva_Cast_Exception_InvalidCast */
			require_once 'Jadva/Cast/Exception/InvalidCast.php';
			throw new Jadva_Cast_Exception_InvalidCast($fromType, $toBaseType);
		}
	}
}
