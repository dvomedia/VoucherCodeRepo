<?php

// DOC TO DO - OK OK I didn't use doc_c :(
class Worker_Voucher extends Worker {

	protected $_exchangeName = 'voucherExchange';
	protected $_key          = 'key1';

    public function doWork($envelope, $queue) {
        //sleep(2);
        echo ($envelope->isRedelivery()) ? 'Redelivery' : 'New Message';
        echo PHP_EOL;
        echo $envelope->getBody(), PHP_EOL;
    }
}