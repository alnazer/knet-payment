<?php

namespace Payments\KnetPayment;
//use Payments\KnetPayment\Knet;
require_once "TestKnet.php";
global  $testknet;
print_r($testknet->responce());
/*
 * Array
(
    [status] => success
    [paymentid] => 100202117641275691
    [trackid] => 16245825122539
    [tranid] => 202117641286402
    [ref] => 117610000022
    [result] => CAPTURED
    [postdate] => 0625
    [auth] => B36659
    [avr] => N
    [ammount] => 1.000
    [udf1] =>
    [udf2] =>
    [udf3] =>
    [udf4] =>
    [udf5] =>
    [data] => paymentid=100202117641275691&result=CAPTURED&auth=B36659&avr=N&ref=117610000022&tranid=202117641286402&postdate=0625&trackid=16245825122539&udf1=&udf2=&udf3=&udf4=&udf6=&udf7=&udf8=&udf9=&udf10=&udf5=&amt=1.000&authRespCode=00&
    [ErrorText] =>
    [Error] =>
)

 */