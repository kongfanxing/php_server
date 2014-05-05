<?php
namespace lib\server;

use lib\server\servers\Server;

class ServerStore
{
	static $sw_mode = SWOOLE_PROCESS;
	
	private $sw;
	private $server;
	private $config;
	
	public function __construct($host, $port, array $config)
	{
		$this->sw = new \swoole_server($host, $port, self::$sw_mode, SWOOLE_SOCK_TCP);
		$this->config = $config;
	}
	
	/**
	 * 选择服务器
	 * @param Server $server
	 * @return \lib\server\ServerStore
	 */
	public function setServer(Server $server)
	{
		$this->server = $server;
		return $this;
	}
	
	public function run()
	{
		if (!$this->server instanceof Server)
			throw new \Exception("there is no server setted!"); 
		
		$this->setCallback('start', 'onStart');
		$this->setCallback('WorkerStart', 'onWorkerStart');
		$this->setCallback('Connect', 'onConnect');
		$this->setCallback('Receive', 'onReceive');
		$this->setCallback('Close', 'onClose');
		$this->setCallback('Connect', 'onConnect');
		
		$this->sw->set($this->config);
		$this->sw->start();
	}
	
	private function setCallback($name, $callback_method_name)
	{
		if (method_exists($this->server, $callback_method_name))
			$this->sw->on($name, array($this->server, $callback_method_name)); 
	}
}