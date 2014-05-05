<?php
define('SERVER_ROOT', __DIR__ . '/../');
require SERVER_ROOT . 'lib/main/Base.php';
require __DIR__.'/media_rpc'.'/MediaRetrieveService.php';
require __DIR__.'/media_rpc'. '/Types.php';
require __DIR__.'/media_rpc'.'/MediaRetrieveProcesser.php';

$server = new lib\server\ServerStore(
				'192.168.135.248', 
				'9501', 
				array('worker_num' => 2, 'daemonize'  => 0,)
			);
$rpc_server = new lib\server\servers\ThriftRpcServer();
//MediaRetrieveProcesser这个类是需求自己开发的
$rpc_server->setServiceName('MediaRetrieveService')->setProcesser('MediaRetrieveProcesser');

$server->setServer($rpc_server)->run();