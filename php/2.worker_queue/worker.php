<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$sec =isset($argv[1])?$argv[1]:0;
//RoundRobin:无差别均分任务,每个消费者均分任务
//Fair:只有收消费者确认后才发下一个任务,能力强的消费者会多消费
$worker_mode =isset($argv[2])?$argv[2]:'Fair';

echo '每次执行任务前睡眠:', $sec, "秒\n";
echo '工作模式:'.$worker_mode."\n";

require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg)  use($sec) {
	echo ' [x] Received ', $msg->body, "\n";
	if ($sec) {
		sleep($sec);
	}
	$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);//手动ACK
	echo " [x] Done\n";
};

//每个消费者发送确认之前,消息队列不发送下一个消息到消费者,每次只处理1个消息
if ($worker_mode == 'Fair') {
	$channel->basic_qos(null, 1, null);
}

$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
	$channel->wait();
}

$channel->close();
$connection->close();
