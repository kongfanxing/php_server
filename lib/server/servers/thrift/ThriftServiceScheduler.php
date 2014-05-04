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

class ThriftServiceScheduler 
{
	private $service;
	private $mb;
	private $protocol;
	private $processer;
	
	private $name;
	private $type;
	private $seqId;
	
	function __construct(TMemoryBuffer $mb, $service, $processer)
	{
		$this->service = $service;
		$this->mb = $mb;
		$this->processer = $processer;
	}
	
	static function init(TMemoryBuffer $mb, $service, $processer)
	{
		return new self($mb, $service, $processer);
	}
	
	function callService()
	{
		$this->protocol = new \Thrift\Protocol\TBinaryProtocol($this->mb);
		
		$service_args = $this->getServiceArgs();
		$service_args->read($this->protocol);

		$this->protocol->readMessageEnd();
		$processor = new $this->processer($service_args);
		$retValue = $processor->execute($service_args);
		
		$service_result = $this->getServiceResult();
		$service_result->success = $retValue;
		
		$swTrans = new \lib\server\servers\thrift\ThriftTransport();
		$outTransport = new \Thrift\Transport\TFramedTransport($swTrans);
		$pro = new \Thrift\Protocol\TBinaryProtocol($outTransport);
		$pro->writeMessageBegin($this->name, \Thrift\Type\TMessageType::REPLY, $this->seqId);
		$service_result->write($pro);
		$pro->writeMessageEnd();
		$pro->getTransport()->flush();
		
		return $swTrans->getBuffer()->getBuffer();
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
