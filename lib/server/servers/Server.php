<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers;

use lib\protocol\Protocol;
use lib\main\ProtocolConst;

abstract class Server 
{
	protected $protocol_name = ProtocolConst::HTTP;
	private $protocol;
	private static $protocol_pool;
	
	public function __construct($protocol_name='')
	{
		if ($protocol_name) $this->protocol_name = $protocol_name;
	}
	
	abstract function process($request);
	
	public function run($fd, $data)
	{
		$this->protocol = $this->selectProtocol($fd);
		$this->protocol->setFileDescript($fd);

		$request = $this->getRequest($this->protocol, $data);
		
		if ($request === ProtocolConst::STATUS_ERR || $request === ProtocolConst::STATUS_WAIT) 
			return;

		$response = $this->process($request);
		$response = $this->getResponse($this->protocol, $response);

		return $response;
	}
	
	private function getRequest(Protocol $protocol, $data)
	{
		return $protocol->getRequest($data);
	}
	
	private function getResponse(Protocol $protocol, $response)
	{
		return $protocol->getResponse($response);
	}
	
	private function selectProtocol($fd)
	{
		$protocol = "\\lib\\protocol\\" . $this->protocol_name . 'Protocol';
		if (isset(self::$protocol_pool[$protocol])) 
			return self::$protocol_pool[$protocol];
		
		return self::$protocol_pool[$protocol] = new $protocol($fd);
	}
	
	function onStart($serv)
	{
		echo "MasterPid={$serv->master_pid}|Manager_pid={$serv->manager_pid}\n";
		echo "Server: start.Swoole version is [".SWOOLE_VERSION."]\n";
	
	} 
	
	function onWorkerStart($serv, $worker_id)
	{
		echo "WorkerStop[$worker_id]|pid=".posix_getpid().".\n";
	}
	
	
	function onClose($serv, $fd, $from_id)
	{
	
	}
	
	function onConnect($serv, $fd, $from_id)
	{
	
	}
}