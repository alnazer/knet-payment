# knet-payment



## usage

### require information
get this information knet account
```php
$tranportal_id;
$password;
$resource_key;
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
$this->addError("index_name,"message");
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
public function afterRequest($trak_id, $pay_url)
{
    parent::afterRequest($trak_id, $pay_url);

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
  "ErrorText" => "",
  "Error" => "",
]
```


