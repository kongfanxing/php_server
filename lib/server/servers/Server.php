<?php
/**
 * 
 * @author 孔繁兴(erickung)
 *
 */

namespace lib\server\servers;

use lib\protocol\Protocol;
use lib\main\ProtocolConst;

/**
 * Server基类
 *
 */
abstract class Server 
{
	/**
	 * 协议名称
	 * @var String
	 */
	protected $protocol_name = ProtocolConst::HTTP;
	/**
	 * 协议对象
	 * @var Protocol
	 */
	private $protocol;
	
	/**
	 * 协议对象池
	 * @var array
	 */
	private static $protocol_pool = array();
	
	public function __construct($protocol_name='')
	{
		if ($protocol_name) $this->protocol_name = $protocol_name;
	}
	
	/**
	 * Server业务处理
	 * @param String $request
	 */
	abstract function process($request);
	
	/**
	 * 对每次请求的执行、响应
	 * @param int $fd
	 * @param Sting $data
	 * @return null|String
	 * 
	 */
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
	
	public function onReceive($serv, $fd, $from_id, $data)
	{
		$response = $this->run($fd, $data);
		if (!is_null($response))
		{
			$serv->send($fd, $response);
			$serv->close($fd);
		}
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
}