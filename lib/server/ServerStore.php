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
	
	public function setServer(Server $server)
	{
		$this->server = $server;
		return $this;
	}
	
	public function run()
	{
		$this->setCallback('start', 'onStart');
		$this->setCallback('WorkerStart', 'onWorkerStart');
		$this->setCallback('Connect', 'onConnect');
		$this->setCallback('Receive', 'onReceive');
		$this->sw->on('Receive', array($this, 'onReceive'));
		$this->setCallback('Close', 'onClose');
		$this->setCallback('Connect', 'onConnect');
		
		$this->sw->set($this->config);
		$this->sw->start();
	}
	
	public function onReceive($serv, $fd, $from_id, $data)
	{
		$response = $this->server->run($fd, $data);
		if (!is_null($response))
		{
			$serv->send($fd, $response);
			$serv->close($fd);
		} 
	}
	
	private function setCallback($name, $callback_method_name)
	{
		if (method_exists($this->server, $callback_method_name))
			$this->sw->on($name, array($this->server, $callback_method_name)); 
	}
}