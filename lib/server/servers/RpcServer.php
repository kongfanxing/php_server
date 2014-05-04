<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers;
abstract class RpcServer extends Server
{	
	public function onReceive($serv, $fd, $from_id, $data)
	{
		$response = $this->run($fd, $data);
		if (!is_null($response))
		{
			$serv->send($fd, $response);
			$serv->close($fd);
		}
	}
}
