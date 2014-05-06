<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers\thrift;
use \Thrift\Exception\TApplicationException;
use \lib\server\servers\thrift\ThriftSerializer;
use \Thrift\Transport\TMemoryBuffer;

/**
 * 
 * Service调度类
 *
 */
class ServiceScheduler 
{
	/**
	 * Service的名称
	 * @var String
	 */
	private $service;
	
	/**
	 * TMemoryBuffer对象
	 * @var TMemoryBuffer
	 */
	private $mb;
	
	/**
	 * TBinaryProtocol对象
	 * @var TBinaryProtocol
	 */
	private $protocol;
	
	/**
	 * Service处理器，需要具体服务自己封装
	 * @see \lib\server\servers\thrift\ServiceProcesser
	 * @var ServiceProcesser
	 */
	private $processer;
	
	/**
	 * method的名称
	 * @var String
	 */
	private $name;
	/**
	 * Trift的message的类型
	 * @see \Thrift\Type\TMessageType
	 * @var int
	 */
	private $type;
	/**
	 * 客户端请求的序列号
	 * @var int
	 */
	private $seqId;
	
	function __construct(TMemoryBuffer $mb, $service, $processer)
	{
		$this->service = $service;
		$this->mb = $mb;
		$this->processer = $processer;
	}
	
	/**
	 * 初始化调度类
	 * @param TMemoryBuffer $mb
	 * @param String $service
	 * @param \lib\server\servers\thrift\ServiceProcesser $processer
	 * @return \lib\server\servers\thrift\ServiceScheduler
	 */
	static function init(TMemoryBuffer $mb, $service, $processer)
	{
		return new self($mb, $service, $processer);
	}
	
	/**
	 * 调用Service
	 * @return string
	 */
	function callService()
	{
		$this->protocol = new \Thrift\Protocol\TBinaryProtocol($this->mb);
		$retValue = $this->getResult();
		return $this->writeResult($retValue);
	}
	
	/**
	 * 获取服务的结果集
	 */
	private function getResult()
	{
		$service_args = $this->getServiceArgs();
		$service_args->read($this->protocol);
		
		$this->protocol->readMessageEnd();
		$processor = new $this->processer($service_args);
		return $processor->execute($service_args);
	}
	
	/**
	 * 向buffer写入结果
	 * @param  $retValue
	 * @return string
	 */
	private function writeResult($retValue)
	{
		$service_result = $this->getServiceResult();
		$service_result->success = $retValue;
		
		$thriftTransport = new \lib\server\servers\thrift\ThriftTransport();
		$outTransport = new \Thrift\Transport\TFramedTransport($thriftTransport);
		$pro = new \Thrift\Protocol\TBinaryProtocol($outTransport);
		$pro->writeMessageBegin($this->name, \Thrift\Type\TMessageType::REPLY, $this->seqId);
		$service_result->write($pro);
		$pro->writeMessageEnd();
		$pro->getTransport()->flush();
		
		return $thriftTransport->getBuffer()->getBuffer();
	}
	
	private function getServiceArgs()
	{
		list($this->name, $this->type, $this->seqId) = ThriftSerializer::deserialize($this->protocol);
		$class_name = $this->service."_".$this->name."_args";
		if(!class_exists($class_name))
		{
			throw new TApplicationException("can not find args for method " + $this->name, TApplicationException::UNKNOWN_METHOD);
		}
		
		return new $class_name();
	}
	
	private function getServiceResult()
	{
		$class_name = $this->service."_".$this->name."_result";
		
		if(!class_exists($class_name))
		{
			throw new TApplicationException("can not find result for method " + $this->name, TApplicationException::UNKNOWN_METHOD);
		}
		
		return new $class_name();
	}
	
}
