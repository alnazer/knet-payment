<?php
use Alnazer\KnetPayment\Knet;

class MyPaymentName extends Knet
{
    /**
     * if you want add error just add function 
     * $this->addError("index_name,"message);
     */
    public $is_test = false;
    public $amount = 1;

    // get from bank information
    public $tranportal_id = "";
    public $password = "";
    public $resource_key = "";

    // important information
    public $response_url = "";
    public $error_url = "";


    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function run()
    {
        return $this->request();
    }
    
    public function result()
    {
        return $this->responce();
    }

    // *****************************************************
    //                      events
    // *****************************************************
    // add your validation error like add errors
    public function beforeValidate()
    {
        parent::beforeValidate();

        //code here ....

        return true;
    }
    // add your afterValidate
    public function afterValidate()
    {
        parent::afterValidate();

        //code here ....

        return true;
    } 

    // add your beforeRequest event
    protected function beforeRequest()
    {
        parent::beforeRequest();

        //code here ....

    }

    // add your afterRequest event
    public function afterRequest($trak_id, $pay_url)
    {
        parent::afterRequest($trak_id, $pay_url);

        //code here ....

        return true;
    }

    // add your beforeResponce event
    public function beforeResponce()
    {
        parent::beforeResponce();

        //code here ....

        return true;
    } 
    // add your afterResponce
    public function afterResponce($payment_id, $trackid, $result)
    {
        parent::afterResponce($payment_id, $trackid, $result);
        
        //code here ....

        return true;
    }


}
