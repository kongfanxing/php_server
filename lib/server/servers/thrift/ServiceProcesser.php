<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers\thrift;

/**
 * 服务处理基类
 *
 */
abstract class ServiceProcesser 
{
	public $service_args;
	 
	function __construct($args)
	{
		$this->service_args = $args;
	}
	
	abstract function execute();
	
}

