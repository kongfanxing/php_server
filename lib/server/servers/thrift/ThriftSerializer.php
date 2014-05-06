<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers\thrift;

class ThriftSerializer 
{
	public static function serialize($object) 
	{
		
	}
	
	public static function deserialize(\Thrift\Protocol\TProtocol $protocol) 
	{
		$name = $type = $seqId = "";
		$protocol->readMessageBegin($name, $type, $seqId);
		return array($name, $type, $seqId);
	}
}

?>