<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

$channel->exchange_declare('logs', 'fanout', false, false, false);

//创建临时队列:
//队列名为没有指定,rabbitmq将会给一个随机队列名
//队列是exclusive为true,意为独占一个连接对应一个队列,连接一关闭队列自动删除
list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'logs');

echo " [*] Waiting for logs. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
