<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Exchange_Voucher extends Exchange
{
	protected $_exchangeName = 'voucherExchange';
	protected $_queues       = array(
                                  array('name' => 'datafeed', 'flag' => AMQP_DURABLE),
                                  array('name' => 'frontend', 'flag' => AMQP_NOPARAM)
                                );
	protected $_key          = 'key1';
}