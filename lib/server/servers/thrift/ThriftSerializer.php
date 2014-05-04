<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers\thrift;

class ThriftSerializer 
{
	public static function serialize($object) {
		/*
		$transport = new TMemoryBuffer();
		$protocol = new TBinaryProtocolAccelerated($transport);
		if (function_exists('thrift_protocol_write_binary')) {
			thrift_protocol_write_binary($protocol, $object->getName(),
			TMessageType::REPLY, $object,
			0, $protocol->isStrictWrite());
	
			$protocol->readMessageBegin($unused_name, $unused_type,
					$unused_seqid);
		} else {
			$object->write($protocol);
		}
		$protocol->getTransport()->flush();
		return $transport->getBuffer();
		*/
	}
	
	public static function deserialize(\Thrift\Protocol\TProtocol $protocol) 
	{
		$name = $type = $seqId = "";
		$protocol->readMessageBegin($name, $type, $seqId);
		return array($name, $type, $seqId);
	}
}

?>