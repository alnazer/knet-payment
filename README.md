[![Latest Stable Version](https://img.shields.io/packagist/v/alnazer/knet-payment.svg)](https://packagist.org/packages/alnazer/knet-payment)
[![Total Downloads](https://img.shields.io/packagist/dt/alnazer/knet-payment.svg)](https://packagist.org/packages/alnazer/knet-payment)
[![Build Status](https://travis-ci.org/alnazer/knet-payment.svg?branch=knet-payment)](https://travis-ci.org/alnazer/knet-payment)
[![License](https://img.shields.io/packagist/l/alnazer/knet-payment)](https://packagist.org/packages/alnazer/knet-payment)
 



# knet-payment

to test payment choose KNET from banks list (بنك اختبار كنيت [KNET1])
```text
Card Number : 8888880000000001

for Captured 
Expiry Date : 09/25

for Not Captured
Expiry Date : Any

CVC : Any 4 digit
```
## Installation
```composer
composer require alnazer/knet-payment
```

## usage
```php
use Alnazer\KnetPayment\Knet;

$config = [
    "tranportal_id"=>"",
    "password"=>"",
    "resource_key"=>"",
    "response_url" => "https://yourdomain.com/result.php",
    "error_url" => "https://yourdomain.com/error.php",
    "amount"=> 1,
    "udf1"=> "",
    "udf2"=> "",
    "udf3"=> "",
    "udf4"=> "",
    "udf5"=> "",
];

$knet  = new Knet($config);

// **************  request from knet *************//
$request = $knet->request();

if($request["status"] == 1)
{
    // redirect to knet payment page using $request["data"]["url"];
}
else
{
    // display errors print_r($request["errors"]);
}

// ************** back from knet  *************//

$resutl = $knet->responce();

if($resutl["status"] == "success"){
    // get reult and update your database
}
else
{
    // print error $resutl["ErrorText"]
}
```

### For user Register after 4/9/2023
if you register after 4/9/2023 and want active KNET new policy set this active
```php
$is_redirection_mode = true;
```
to return redirect page using this function
```php
//$redirect_url the link will redirect (your order page or confiremed payment)
//$payment_id : payment id restrun from KNET
print_redirect($redirect_url,$payment_id);

// ************** back from knet  *************//

$resutl = $knet->responce();

if($resutl["status"] == "success"){
  // get reult and update your database
  echo $knet->print_redirect("The page url will redirect to it",$resutl["paymentid"]);
}
else
{

  // get reult and update your database
  echo $knet->print_redirect("The error page url will redirect to it",$resutl["paymentid"]);
}
```

### require information
get this information knet account
```php
$tranportal_id;
$password;
$resource_key;
```
if you want test payment
```php
$is_test = true;
```


### return urls
```php
$response_url; // url bank will return after payment operation success
$error_url;// url bank will return if faveing error in payment operation
```

### call request using function
```php
$this->request()
```
### call result using function
```php
$this->responce()
```
### validate
```php
$this->validate();
```

### to add error
```php
$this->addError("index_name","message");
```
## Eventes

** Must implement this functions

#### before validate
```php
public function beforeValidate()
{
    parent::beforeValidate();

    //code here ....

    return true;
}
```


#### after validate
```php
public function afterValidate()
{
    parent::afterValidate();

    //code here ....

    return true;
}
```

#### before request
called before call knet url 
```php
public function beforeRequest()
{
    parent::beforeRequest();

    //code here ....

    return true;
}
```

#### after request
called after call knet url 
```php
public function afterRequest($trackid, $pay_url)
{
    parent::afterRequest($trackid, $pay_url);

    //code here ....

    return true;
}
```

#### before responce
```php
public function beforeResponce()
{
    parent::beforeResponce();

    //code here ....

    return true;
}
```


#### after responce
```php
public function afterResponce($payment_id, $trackid, $result)
{
    parent::afterResponce($payment_id, $trackid, $result);

    //code here ....

    return true;
}
```

## Result

### request return

#### success
```php
[
"status" => 1,
  "data"=>
    [
      "url"=>"https://knetpayment.com",
      "trackid"=>"23492375295"
    ],
  "errors"=>false
]
```

#### error
```php
[
"status" => 0,
  "data"=>
    [
      "url"=>"",
      "trackid"=>"23492375295"
    ],
  "errors"=>
    [
      "index_error" = > "error message",
      "index_error2" = > "error message2",
    ]
]
```

### responce "result" return
#### success
```php
[
  "status" => "success",
  "paymentid" => "2423sdfsd723482582",
  "trackid" => "3424234234",
  "tranid" => "4234234234234",
  "ref" => "3523235252",
  "result" => "CAPTURED", //knet result CAPTURED,NOT CAPTURED,CANCELED ... ect
  "postdate" => "3234234",
  "auth" => "44445",
  "avr" => "4566",
  "amount" => "1.000",
  "udf1" => "", // you set this data in request function
  "udf2" => "",// you set this data in request function
  "udf3" => "",// you set this data in request function
  "udf4" => "",// you set this data in request function
  "udf5" => "",// you set this data in request function
  "data" => "", // all $_REQUEST data
  "ErrorText" => "",
  "Error" => "",
]
```

#### error

```php
[
  "status" => "error",
  "paymentid" => "2423sdfsd723482582",
  "trackid" => "3424234234",
  "tranid" => "4234234234234",
  "ref" => "3523235252",
  "result" => "error",
  "postdate" => "3234234",
  "auth" => "44445",
  "avr" => "4566",
  "amount" => "1.000",
  "udf1" => "", // you set this data in request function
  "udf2" => "",// you set this data in request function
  "udf3" => "",// you set this data in request function
  "udf4" => "",// you set this data in request function
  "udf5" => "",// you set this data in request function
  "ErrorText" => "get from knet responce",
  "Error" => "get from knet responce",
]
```
## Chnagelog

###version 1.1.0

* active redirection mode for KNET new policy

###version 1.0.2

* fixed call array of string php version 8


