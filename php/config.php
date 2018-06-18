<?php

$conf = [
	'host' => '192.168.91.1',
	'port' => 5672,
	'user' => 'user_mmr',
	'pwd' => '123',
	'vhost' => '/vhost_mmr',
];
$exchangename = 'kd_sms_send_ex'; //交换机名
$queuename = 'kd_sms_send_q'; //队列名称
$routingkey = 'sms_send'; //路由关键字(也可以省略))

?>
