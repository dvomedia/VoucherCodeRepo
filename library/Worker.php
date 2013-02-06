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
     * @var string Name of the AMQP exchange
     */
    protected $_queueFlag = AMQP_NOPARAM;


    /**
     * @var string the key of the amqp exhange
     */
    protected $_key = 'default';

    abstract public function doWork($envelope, $queue);

    protected function _getProcessLimit()
    {
        return $this->_processLimit;
    }

    public function __construct($queueName, $flag = null)
    {
        $this->_queueName = $queueName;
        if (false === empty($flag)) {
            $this->_queueFlag = $flag;
        }

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
        $this->_queue->setFlags($this->_queueFlag);
        $this->_queue->declare();
        $this->_queue->bind($this->_exchangeName, $this->_key);
    }

    public function run($processLimit)
    {
        $this->_processLimit = $processLimit;

        // consume!
        $self = $this;
        $this->_queue->consume(function($envelope, $queue) use ($self){
            return $self->consume($envelope, $queue);
        });
    }

    final public function consume($envelope, $queue)
    {
        try {
            $this->doWork($envelope, $queue);
            $queue->ack($envelope->getDeliveryTag());
            $this->_processed++;
        } catch (Worker_Exception_Retry $ex) {
            $queue->nack($envelope->getDeliveryTag());
        } catch (Worker_Exception_Fatal $ex) {
            $queue->ack($envelope->getDeliveryTag());
            // log the message so we don't forget about it
            // @TODO
        }

        if ($this->_processed >= $this->_getProcessLimit()) {
            return false;
        }

        return true;
    }
}
