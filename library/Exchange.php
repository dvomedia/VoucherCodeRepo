<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Exchange
{
	protected $_exchange     = null;
	protected $_exchangeName = 'default';
	protected $_queues       = array();
	protected $_key          = 'default';

	protected function _getExchangeName() {
		return $this->_exchangeName;
	}

	protected function _getQueues() {
		return $this->_queues;
	}

	public function __construct()
	{
		$connection = new AMQPConnection();
		$connection->connect();
		if (!$connection->isConnected()) {
			die('Not connected :(' . PHP_EOL);
		}
		// Open Channel
		$channel    = new AMQPChannel($connection);

		// Declare exchange
		$this->_exchange   = new AMQPExchange($channel);
		$this->_exchange->setName($this->_getExchangeName());
		$this->_exchange->setType(AMQP_EX_TYPE_FANOUT);
		$this->_exchange->setFlags(AMQP_DURABLE);
		$this->_exchange->declare();

		// Create Queues
		foreach ($this->_getQueues() as $queueName) {
			$q = new AMQPQueue($channel);
			$q->setName($queueName);
			// flags should be on a per queue basis
			$q->setFlags(AMQP_DURABLE);
			$q->declare();	
		}
	}

	public function sendMessage($message, $key)
	{
		$message = $this->_exchange->publish('Custom Message (ts): '.time(), 'key1');
		if (!$message) {
		    echo 'Message not sent', PHP_EOL;
		} else {
		    echo 'Message sent!', PHP_EOL;
		}
	}

	public function getKey() {
		return $this->_key;
	}
}
