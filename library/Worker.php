<?php

/**
 * AMQP Worker Class
 *
 * @package default
 * @author  Bobby DeVeaux
 **/

abstract class Worker
{
    /**
     * @var integer How many messages should this block for?
     */
    protected $_processLimit = 10;

    /**
     * @var integer How many have we processed so far?
     */
    protected $_processed = 0;

    /**
     * @var AMQPConnection The AMQP Connection
     */
    protected $_connection = null;

    /**
     * @var AMQPChannel The AMQP Channel
     */
    protected $_channel = null;

    /**
     * @var AMQPQueue The AMQP Queue
     */
    protected $_queue = null;

    /**
     * @var string Name of the queue to connect to
     */
    protected $_queueName  = 'default';

    /**
     * @var string Name of the AMQP exchange
     */
    protected $_exchangeName = 'default';

    /**
     * @var string the key of the amqp exhange
     */
    protected $_key = 'default';

    abstract public function doWork($envelope, $queue);

    protected function _getProcessLimit()
    {
        return $this->_processLimit;
    }

    public function __construct($queueName)
    {
        $this->_queueName = $queueName;

        $this->_connection = new AMQPConnection();
        $this->_connection->connect();
        if (false === $this->_connection->isConnected()) {
            throw new Worker_Exception('[ERROR] Could not connect to AMQP (' . get_class() . ' - ' . get_called_class(). ')');
        }
        
        $this->_setChannel();
        $this->_setQueue();

    }

    protected function _setChannel()
    {
        // Open channel
        $this->_channel = new AMQPChannel($this->_connection);
    }

    protected function _setQueue()
    {
        // Open Queue and bind to exchange
        $this->_queue = new AMQPQueue($this->_channel);
        $this->_queue->setName($this->_queueName);
        $this->_queue->declare();
        $this->_queue->bind($this->_exchangeName, $this->_key);
    }

    public function run($processLimit)
    {
        $this->_processLimit = $processLimit;

        // consume!
        $this->_queue->consume(function($envelope, $queue){
            return $this->consume($envelope, $queue);
        }, AMQP_AUTOACK);
    }

    final public function consume($envelope, $queue)
    {
        try {
            $this->doWork($envelope, $queue);
            $this->_processed++;
        } catch (Exception $ex) {
            return false;
        }

        if ($this->_processed >= $this->_getProcessLimit()) {
            return false;
        }

        return true;
    }
}