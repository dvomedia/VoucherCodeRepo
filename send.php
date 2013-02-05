<?php

// demo file for testing adding items to a queue

include 'library/Exchange.php';
include 'library/Exchange/Factory.php';
include 'library/Exchange/Voucher.php';

$ef = new Exchange_Factory;
$exchange = $ef->create('voucher');

$message = 'Custom Message (ts): '.time();
$exchange->sendMessage($message, $exchange->getKey());