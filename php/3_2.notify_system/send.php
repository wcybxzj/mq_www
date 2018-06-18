<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

$channel->exchange_declare('notify_system', 'fanout', false, true, false);

//https://www.rabbitmq.com/confirms.html
//保证消息持久化在rabbitmq,使用此方法
$channel->confirm_select(false);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = "info: Hello World!";
}
$msg = new AMQPMessage(
    $data,
    array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
);

$channel->basic_publish($msg, 'notify_system');

echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();
