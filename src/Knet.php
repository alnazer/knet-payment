<?php


namespace Alnazer\KnetPayment;


class Knet
{

    public $config;
    public $erros = array();
    public $is_test = false;
    public $amount;
    public $tranportal_id = "";
    public $password = "";
    public $resource_key = "";
    public $udf1 = "";
    public $udf2 = "";
    public $udf3 = "";
    public $udf4 = "";
    public $udf5 = "";
    protected $trackId;
    public $response_url = "";
    public $error_url = "";
    public $language = "AR";
    public $currencycode = "414";
    private $gateway_url='https://kpay.com.kw/';

    private $payment_url = "id={id}&password={password}&action=1&langid={language}&currencycode={currencycode}&amt={amt}&responseURL={responseURL}&errorURL={errorURL}&trackid={trackid}&udf1={udf1}&udf2={udf2}&udf3={udf3}&udf4={udf4}&udf5={udf5}";

    public function __construct($config = array())
    {
        $this->config = array_merge(
            [
                "is_test"=>false,
                "tranportal_id"=>"",
                "password"=>"",
                "resource_key"=>"",
                "response_url"=> "",
                "error_url"=> "",
                "trackId"=> time().mt_rand(1000,100000),
                "amount" => 0,
                "language" => "AR",
                "currencycode" => "414"
            ],
            $config);
        if(is_array($this->config) && count($this->config) > 0){
            foreach ($this->config as $k => $val){
                if($val){
                    $this->{$k} = $val;
                }
            }
        }
        if($this->is_test == true){
            $this->gateway_url='https://kpaytest.com.kw/';
        }
    }

    public function request(){
        // call before request event
        $this->beforeRequest();

        if($this->validate())
        {
            // make knet request
            $this->formatUrlParames();
            $param  =    $this->encryptAES($this->payment_url,$this->resource_key)."&tranportalId=".$this->tranportal_id."&responseURL=".$this->response_url."&errorURL=".$this->error_url;
            $payURL =    $this->gateway_url."kpg/PaymentHTTP.htm?param=paymentInit"."&trandata=".$param;

            // call after request event
            $this->afterRequest($this->trackId,$payURL);

            // return final result
            return ["status" => 1, "data"=>["url"=>$payURL,"trackid"=>$this->trackId], "errors"=>false];
        }

        //return error result
        return ["status" => 0,"data"=>[],"errors"=>$this->erros];
    }

    public function responce(){
        //call before Responce event
        $result= [];
        $this->beforeResponce();
        $ResErrorText   =   (isset($_REQUEST['ErrorText'])) ? $this->sanitize_field($_REQUEST['ErrorText']) : null; 	  	//Error Text/message
        $ResPaymentId   =   (isset($_REQUEST['paymentid'])) ? $this->sanitize_field($_REQUEST['paymentid']) : null; 		//Payment Id
        $ResTrackID     =   (isset($_REQUEST['trackid']))   ? $this->sanitize_field($_REQUEST['trackid']) : null;       	//Merchant Track ID
        $ResErrorNo     =   (isset($_REQUEST['Error']))     ? $this->sanitize_field($_REQUEST['Error']) : null;           //Error Number
        $ResResult      =   (isset($_REQUEST['result']))    ? $this->sanitize_field($_REQUEST['result']) : null;           //Transaction Result
        $ResPosdate     =   (isset($_REQUEST['postdate']))  ? $this->sanitize_field($_REQUEST['postdate']) : null;         //Postdate
        $ResTranId      =   (isset($_REQUEST['tranid']))    ? $this->sanitize_field($_REQUEST['tranid']) : null;         //Transaction ID
        $ResAuth        =   (isset($_REQUEST['auth']))  ? $this->sanitize_field($_REQUEST['auth']) : null;               //Auth Code
        $ResAVR         =   (isset($_REQUEST['avr']))   ? $this->sanitize_field($_REQUEST['avr']) : null;                //TRANSACTION avr
        $ResRef         =   (isset($_REQUEST['ref']))   ? $this->sanitize_field($_REQUEST['ref']) : null;                //Reference Number also called Seq Number
        $ResAmount      =   (isset($_REQUEST['amt']))   ? $this->sanitize_field($_REQUEST['amt']) : null;             //Transaction Amount
        $Resudf1        =   (isset($_REQUEST['udf1']))  ? $this->sanitize_field($_REQUEST['udf1']) : null;              //UDF1
        $Resudf2        =   (isset($_REQUEST['udf2']))  ? $this->sanitize_field($_REQUEST['udf2']) : null;               //UDF2
        $Resudf3        =   (isset($_REQUEST['udf3']))  ? $this->sanitize_field($_REQUEST['udf3']) : null;                //UDF3
        $Resudf4        =   (isset($_REQUEST['udf4']))  ? $this->sanitize_field($_REQUEST['udf4']) : null;    //UDF4
        $Resudf5        =   (isset($_REQUEST['udf5']))  ? $this->sanitize_field($_REQUEST['udf5']) : null;    //UDF5
        if($ResErrorText==null && $ResErrorNo==null && $ResPaymentId != null)
        {
            // success
            $ResTranData= (isset($_REQUEST['trandata'])) ? $this->sanitize_field($_REQUEST['trandata']) : null;
            $decrytedData=$this->decrypt($ResTranData,$this->resource_key);
            parse_str($decrytedData, $output);
                if($ResTranData !=null)
                {
                    $result['status'] = 'success';
                    $result['paymentid'] = $ResPaymentId;
                    $result['trackid'] = $ResTrackID;
                    $result['tranid'] = $output['tranid'];
                    $result['ref'] = $output['ref'];
                    $result['result'] = $output['result'];
                    $result['postdate'] = $output['postdate'];
                    $result['auth'] = $output['auth'];
                    $result['avr'] = $output['avr'];                 //TRANSACTION avr
                    $result['amount'] = $output['amt'];              //Transaction Amount
                    $result['udf1'] = $output['udf1'];               //UDF1
                    $result['udf2'] = $output['udf2'];               //UDF2
                    $result['udf3'] = $output['udf3'];               //UDF3
                    $result['udf4'] = $output['udf4'];               //UDF4
                    $result['udf5'] = $output['udf5'];
                    //Decryption logice starts
                    $result['data']=$decrytedData;
                    $result['ErrorText']= $ResErrorText; 	  	//Error
                    $result['Error'] = $ResErrorNo;
                }else{
                    $result['status'] = 'error';
                    $result['paymentid'] = $ResPaymentId;
                    $result['trackid'] = $ResTrackID;
                    $result['tranid'] = $ResTranId;
                    $result['ref'] = $ResRef;
                    $result['result'] =  'error';
                    $result['data']= http_build_query($_REQUEST);
                    $result['postdate'] = $ResPosdate;
                    $result['auth'] = $ResAuth;
                    $result['avr'] = $ResAVR;                 //TRANSACTION avr
                    $result['amount'] = $ResAmount;              //Transaction Amount
                    $result['udf1'] = $Resudf1;               //UDF1
                    $result['udf2'] = $Resudf2;               //UDF2
                    $result['udf3'] = $Resudf3;               //UDF3
                    $result['udf4'] = $Resudf4;               //UDF4
                    $result['udf5'] = $Resudf5;
                    $result['ErrorText']= $ResErrorText; 	  	//Error
                    $result['Error'] = $ResErrorNo;
                }

            }
            else
            {
                // error
                $result['status'] = "error";
                $result['paymentid'] = $ResPaymentId;
                $result['trackid'] = $ResTrackID;
                $result['tranid'] = $ResTranId;
                $result['ref'] = $ResRef;
                $result['result'] = "error";
                $result['data']= http_build_query($_REQUEST);
                $result['postdate'] = $ResPosdate;
                $result['auth'] = $ResAuth;
                $result['avr'] = $ResAVR;
                $result['amount'] = $ResAmount;
                $result['udf1'] = $Resudf1;
                $result['udf2'] = $Resudf2;
                $result['udf3'] = $Resudf3;
                $result['udf4'] = $Resudf4;
                $result['udf5'] = $Resudf5;
                $result['ErrorText']= $ResErrorText;
                $result['Error'] = $ResErrorNo;
            }

            //call after Responce event
            $this->afterResponce($result['paymentid'],$result['trackid'],$result);

        return  $result;
    }

    /** return validate
     * @return bool
     */
    function validate(){
        $this->beforeValidate();
        if($this->hasError()){
            return false;
        }
        $this->afterValidate();
        return  true;
    }

    /**
     * @param $attr field index
     * @param $message error message
     * add error
     */
    public function addError($attr,$message){
        $this->erros[$attr]= $message;
    }

    /**
     * if having error
     * @return bool
     */
    public function hasError(){
        return count($this->erros) > 0;
    }




    /**
     * ========== Events ==========
     */

    /**
     * call before make a request
     */
    protected function beforeRequest()
    {
        if(empty($this->tranportal_id)){
            $this->addError("tranportal_id","tranportal_id can not by empty");
        }
        if(empty($this->password)){
            $this->addError("password","password can not by empty");
        }
        if(empty($this->resource_key)){
            $this->addError("resource_key","resource_key can not by empty");
        }
        if(empty($this->response_url)){
            $this->addError("response_url","response_url can not by empty");
        }
        if(empty($this->error_url)){
            $this->addError("error_url","error_url can not by empty");
        }
        if(empty($this->amount) || $this->amount < 0)
        {
            $this->addError("amount","amount can not by less than zero");
        }
        if(empty($this->language)){
            $this->addError("language","language can not by empty");
        }
        if(empty($this->currencycode)){
            $this->addError("currency_code","currency code can not by empty");
        }
    }
    /**
     * call after make a request
     */
    protected function afterRequest($trak_id, $pay_url)
    {
        return true;
    }

    /**
     * @param $result
     * @return bool
     */
    protected function afterResponce($payment_id, $trackid, $result)
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function beforeResponce()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function beforeValidate()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function afterValidate()
    {
        return true;
    }

    /**
     * @param $text
     * @return mixed
     */
    private function sanitize_field($text){
        return $text;
    }
    /**
     * prepare pay url parames to kent
     * this update pay url var
     */
    private function formatUrlParames()
    {
        $replace_array = array();
        $replace_array['{id}'] = $this->tranportal_id;
        $replace_array['{password}'] = $this->password;
        $replace_array['{amt}'] = $this->amount;
        $replace_array['{trackid}'] = $this->trackId;
        $replace_array['{responseURL}'] = $this->response_url;
        $replace_array['{errorURL}'] = $this->error_url;
        $replace_array['{language}'] = $this->language;
        $replace_array['{currencycode}'] = $this->currencycode;

        $replace_array['{udf1}'] = $this->udf1;
        $replace_array['{udf2}'] = $this->udf2;
        $replace_array['{udf3}'] =$this->udf3;
        $replace_array['{udf4}'] = $this->udf4;
        $replace_array['{udf5}'] = $this->udf5;
        $this->payment_url = str_replace(array_keys($replace_array),array_values($replace_array),$this->payment_url);
    }


    /** ======== Payment Encrypt Functions Started ======
     * this functions created by knet devolper don't change any thing
     */
    private function encryptAES($str,$key)
    {
        $str = $this->pkcs5_pad($str);
        $encrypted = openssl_encrypt($str, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $key);
        $encrypted = base64_decode($encrypted);
        $encrypted=unpack('C*', ($encrypted));
        $encrypted=$this->byteArray2Hex($encrypted);
        $encrypted = urlencode($encrypted);
        return $encrypted;
    }

    private function pkcs5_pad ($text)
    {
        $blocksize = 16;
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    private function byteArray2Hex($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        $bin = join($chars);
        return bin2hex($bin);
    }

    private function decrypt($code,$key)
    {
        $code =  $this->hex2ByteArray(trim($code));
        $code=$this->byteArray2String($code);
        $iv = $key;
        $code = base64_encode($code);
        $decrypted = openssl_decrypt($code, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $iv);
        return $this->pkcs5_unpad($decrypted);
    }

    private function hex2ByteArray($hexString)
    {
        $string = hex2bin($hexString);
        return unpack('C*', $string);
    }


    private function byteArray2String($byteArray)
    {
        $chars = array_map("chr", $byteArray);
        return join($chars);
    }

    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }
    /** ======== Payment Encrypt Functions Ended ====== */
}
