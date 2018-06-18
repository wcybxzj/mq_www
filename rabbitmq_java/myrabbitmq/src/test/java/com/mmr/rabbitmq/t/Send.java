package com.mmr.rabbitmq.t;

import java.io.IOException;
import java.util.concurrent.TimeoutException;

import org.junit.Test;

import com.mmr.rabbitmq.util.ConnectionUtils;
import com.rabbitmq.client.Channel;
import com.rabbitmq.client.Connection;

public class Send {
	private static final String	QUEUE_NAME	= "QUEUE_simple_tx";

	@Test
	public void sendMsg() throws IOException, TimeoutException {
		/* 获取一个连接 */
		Connection connection = ConnectionUtils.getConnection();

		/* 从连接中创建通道 */
		Channel channel = connection.createChannel();

		channel.queueDeclare(QUEUE_NAME, false, false, false, null);

		String msg = "Hello  Simple QUEUE !";

		try {
			channel.txSelect();
			channel.basicPublish("", QUEUE_NAME, null, msg.getBytes());
		int result = 1 / 0;
			channel.txCommit();
		} catch (Exception e) {
			channel.txRollback();
			System.out.println("----msg rollabck ");
		}finally{
			System.out.println("---------send msg over:" + msg);
		}
		channel.close();
		connection.close();
	}
}
