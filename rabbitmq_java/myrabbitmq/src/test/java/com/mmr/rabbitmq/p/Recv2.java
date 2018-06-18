package com.mmr.rabbitmq.p;

import java.io.IOException;

import com.mmr.rabbitmq.util.ConnectionUtils;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.Connection;
import com.rabbitmq.client.Consumer;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;
import com.rabbitmq.client.AMQP.BasicProperties;

public class Recv2 {

	private final static String QUEUE_NAME = "test_queue_fanout_2";

	private final static String EXCHANGE_NAME = "test_exchange_fanout";

	public static void main(String[] argv) throws Exception {

		// 获取到连接以及mq通道
		Connection connection = ConnectionUtils.getConnection();
		final Channel channel = connection.createChannel();

		// 声明队列
		channel.queueDeclare(QUEUE_NAME, false, false, false, null);

		// 绑定队列到交换机
		channel.queueBind(QUEUE_NAME, EXCHANGE_NAME, "");

		// 同一时刻服务器只会发一条消息给消费者
		// 定义一个消费者
		Consumer consumer = new DefaultConsumer(channel) {
			// 消息到达 触发这个方法
			@Override
			public void handleDelivery(String consumerTag, Envelope envelope,
					BasicProperties properties, byte[] body) throws IOException {

				String msg = new String(body, "utf-8");
				System.out.println("[2] Recv msg:" + msg);

				try {
					Thread.sleep(1000);
				} catch (InterruptedException e) {
					e.printStackTrace();
				} finally {
					System.out.println("[2] done ");
					// 手动回执
					channel.basicAck(envelope.getDeliveryTag(), false);
				}
			}
		};

		boolean autoAck = false;
		channel.basicConsume(QUEUE_NAME, autoAck, consumer);

	}
}