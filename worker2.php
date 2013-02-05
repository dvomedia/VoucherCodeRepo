<?php

// demo file for testing the worker (frontend)

include 'library/Worker.php';
include 'library/Worker/Factory.php';
include 'library/Worker/Voucher.php';
include 'library/Worker/Exception.php';

$wf = new Worker_Factory;

// first param the exchange to connect to
// second param is what queue to listen to
$worker = $wf->create('voucher', 'frontend');

// listen & block for x times before ending
$worker->run(5);