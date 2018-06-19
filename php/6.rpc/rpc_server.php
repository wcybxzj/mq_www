<?php

require_once __DIR__ . '/../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$sec =isset($argv[1])?$argv[1]:0;
echo '每次执行任务前睡眠:', $sec, "秒\n";
echo "工作模式:Fair \n";

require_once '../config.php';
$connection = new AMQPStreamConnection($conf['host'],$conf['port'],$conf['user'], $conf['pwd'], $conf['vhost']);
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);

function fib($n)
{
	return $n;
    //if ($n == 0) {
    //    return 0;
    //}
    //if ($n == 1) {
    //    return 1;
    //}
    //return fib($n-1) + fib($n-2);
}

echo " [x] Awaiting RPC requests\n";
$callback = function ($req) use($sec){
    $n = intval($req->body);
    echo ' [.] fib(', $n, ")\n";


	for ($i = 0; $i < $sec; $i++) {
		echo "working sec:$sec\n";
		sleep(1);
	}

    $msg = new AMQPMessage(
        (string) fib($n),
        array('correlation_id' => $req->get('correlation_id'))
    );

    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']
    );

	echo "delivery_tag:".$req->delivery_info['delivery_tag']."\n";
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
