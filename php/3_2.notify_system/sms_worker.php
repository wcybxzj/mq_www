<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$sec =isset($argv[1])?$argv[1]:0;
echo "每次请求前sleep".$sec."秒\n";
require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

$channel->exchange_declare('notify_system', 'fanout', false, true, false);

list($queue_name, ,) = $channel->queue_declare("sms", false, true, false,false);

$channel->queue_bind($queue_name, 'notify_system');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) use ($sec) {
	echo ' [x] Received ', $msg->body, "\n";
	if ($sec) {
		sleep($sec);
	}
	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);//手动ACK
	echo " [x] Done\n";
};

//通知队列 worker是fair模式
$channel->basic_qos(null, 1, null);

//手动ACK
$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
