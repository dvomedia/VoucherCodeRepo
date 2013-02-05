<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Exchange_Voucher extends Exchange
{
	protected $_exchangeName = 'voucherExchange';
	protected $_queues       = array('datafeed', 'frontend');
	protected $_key          = 'key1';
}