package com.mmr.rabbitmq.t;

import java.io.IOException;

import com.mmr.rabbitmq.util.ConnectionUtils;
import com.rabbitmq.client.AMQP.BasicProperties;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.Connection;
import com.rabbitmq.client.DefaultConsumer;
import com.rabbitmq.client.Envelope;

public class Consumer {
	private static final String	QUEUE_NAME	= "QUEUE_simple_tx";

	public static void main(String[] args) throws Exception {
		Connection connection = ConnectionUtils.getConnection();
		final Channel channel = connection.createChannel();

		channel.queueDeclare(QUEUE_NAME, false, false, false, null);

//消费者需要执行basicAck，并txCommit(自动应答模式自动处理，本例中采用手动应答模式) 
		DefaultConsumer consumer = new DefaultConsumer(channel) {
			//获取到达的消息
			@Override
			public void handleDelivery(String consumerTag, Envelope envelope, BasicProperties properties, byte[] body) throws IOException {
				String message = new String(body, "UTF-8");
				System.out.println(" [x] Received '" + message + "'");
				  channel.basicAck(envelope.getDeliveryTag(), false);  
			}
		};
		//监听队列
		channel.basicConsume(QUEUE_NAME, false, consumer);
	}
}
