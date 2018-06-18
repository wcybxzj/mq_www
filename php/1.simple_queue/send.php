<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

//hello队列不是持久化的
$channel->queue_declare('hello', false, false, false, false);

$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, '', 'hello');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();
