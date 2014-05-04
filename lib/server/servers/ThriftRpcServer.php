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
$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', VENDORS);
$loader->register();

class ThriftRpcServer extends RpcServer 
{
	protected $protocol_name = ProtocolConst::Binary;
	private $service_name;
	private $processer;
	
	public function process($request) 
	{
		$TMemoryBuffer = new \lib\server\servers\thrift\ThriftTransport();
		$left = substr($request, 4, strlen($request) - 4);
		$TMemoryBuffer->write($left);

		$request = \lib\server\servers\thrift\ThriftServiceScheduler::
			init($TMemoryBuffer->getBuffer(), $this->service_name, $this->processer)->callService();
		
		return $request;
	}
	
	public function setServiceName($service_name)
	{
		$this->service_name = $service_name;
		return $this;
	}
	
	public function setProcesser($processer)
	{
		$this->processer = $processer;
		return $this;
	}
}
