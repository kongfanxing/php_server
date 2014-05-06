<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\main;
define('LIB', __DIR__ . '/../');
define('VENDORS', LIB . 'vendors/');
Base::init();

class Base 
{
	const LIB = 'lib';
	
	private static $root_path;
	private static $reqiured_files = array();
	private static $user_autoload = array();
	
	static function init()
	{
		self::$root_path = LIB . '../';
		spl_autoload_register('self::autoload');
	}
	
	/**
	 * 自动加载类
	 * @param string $class
	 */
	static function autoload($class)
	{
		$file = self::$root_path .  str_replace('\\', '/', $class).'.php';
		if( substr($class, 0, 3) == self::LIB && file_exists($file) )
		{
			if (!isset(self::$reqiured_files[$file]))
			{
				self::$reqiured_files[$file] = $file;
				require $file;
			}
		}
		else 
		{
			if (!empty(self::$user_autoload)) 
				call_user_func(self::$user_autoload, $class);
		}
	}
	
	/**
	 * 设置自己的自动加载类
	 * @param Object|String $object
	 * @param String $method_name
	 * @throws \Exception
	 */
	static function setAutoLoad($object, $method_name)
	{
		if (!method_exists($object, $method_name))
			throw new \Exception('there is no class and method in autoloaded function! ');
		self::$user_autoload = array($object, $method_name);
	}
}