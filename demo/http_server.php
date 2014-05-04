<?php
require __DIR__ . '/../lib/main/Base.php';
lib\main\Base::init();

$server = new lib\server\ServerStore('192.168.135.248', '9502', 
				array('worker_num' => 2, 'daemonize'  => 0,));
$server->setServer(new lib\server\servers\HttpServer())->run();