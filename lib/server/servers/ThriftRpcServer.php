<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers;

require_once VENDORS . 'Thrift/ClassLoader/ThriftClassLoader.php';
use lib\main\ProtocolConst;
use Thrift\ClassLoader\ThriftClassLoader;
// 自动加载Thrift的类
$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', VENDORS);
$loader->register();

class ThriftRpcServer extends RpcServer 
{
	protected $protocol_name = ProtocolConst::Binary;
	
	/**
	 * service的名称
	 * @var String
	 */
	private $service_name;
	/**
	 * Service处理器，需要具体服务自己封装
	 * @see \lib\server\servers\thrift\ServiceProcesser
	 * @var ServiceProcesser
	 */
	private $processer;
	
	public function process($request) 
	{
		$TMemoryBuffer = new \lib\server\servers\thrift\ThriftTransport();
		$left = substr($request, 4, strlen($request) - 4);
		$TMemoryBuffer->write($left);

		$response = \lib\server\servers\thrift\ServiceScheduler::
			init($TMemoryBuffer->getBuffer(), $this->service_name, $this->processer)->callService();
		
		return $response;
	}
	
	/**
	 * 加载服务名称
	 * @param String $service_name
	 * @return \lib\server\servers\ThriftRpcServer
	 */
	public function setServiceName($service_name)
	{
		$this->service_name = $service_name;
		return $this;
	}
	
	/**
	 * 加载 Service处理器，需要具体服务自己封装
	 * @param ServiceProcesser $processer
	 * @return \lib\server\servers\ThriftRpcServer
	 */
	public function setProcesser($processer)
	{
		$this->processer = $processer;
		return $this;
	}
}
