<?php

namespace curl;

class Request implements RequestInterface {

    private $methods = ['POST','GET','PUT','HEAD','PATCH','DELETE','OPTIONS','PATCH'];

    private $headers = [];

    private $ch = null;

    private $params = [];

    private $cookie = [];

    private $referrer = '';

    private $userAgent = '';

    public $ResponseCallback = null;

    public $result = null;

    public $resultCallback = null;

    public $id = null;

    public $url = null;

    private function setId($id){

        $this->id = $id;

        return $this;
    }

    private function setUrl($url){

        $this->url = $url;

        return $this;
    }

    public function __construct($url, $id, $callback = null, $options = array()){

        $this->init();

        $this->setOpt(CURLOPT_URL, $url);

        $this->setId($id);

        $this->setUrl($url);

        $this->setResponseCallback($callback);

        if($options)
            $this->setOptArray($options);

        return $this;
    }

    public function init(){

        $this->ch = curl_init();

        return $this;
    }

    public function getResource(){

        return $this->ch;
    }

    public function copyHandle(){

        return curl_copy_handle($this->ch);
    }

    public function unescape($str){

        return curl_unescape( $this->ch, $str);
    }

    public function pause($bitmask){

        curl_pause($this->ch,$bitmask);

        return $this;
    }

    public function getInfo($opt = 0){

        return curl_getinfo($this->ch, $opt);
    }

    public function getError(){

        return curl_error($this->ch);
    }

    public function setResponse($response){

        $this->result = $response;

        if($this->ResponseCallback && is_callable($this->ResponseCallback))
            $this->resultCallback = call_user_func_array ( $this->ResponseCallback, array('object'=>$this,'data'=>$response));

        return $this;
    }

    public function setMethod($name){

        if(in_array($name,$this->methods)){

            $this->setOpt(CURLOPT_CUSTOMREQUEST, $name);
        }

        return $this;
    }

    private function saveParams(){

        $params = http_build_query($this->params, null, '&');

        return $this->setOpt(CURLOPT_POSTFIELDS, $params);
    }

    public function setParams(array $data){

        $this->params = $data;

        return $this->saveParams();
    }

    public function addParams($name, $value){

        $this->params[$name] = $value;

        return $this->saveParams();
    }

    public function removeParams($name){

        if(isset($this->params))
            unset($this->params[$name]);

        return $this->saveParams();
    }

    public function getParams($key = false){

        if($key)
            return isset($this->params[$key])?$this->params[$key]:null;

        return $this->params;
    }

    public function is_post(){

        $this->setOpt(CURLOPT_POST, true);

        return $this;
    }


    public function is_put(){

        $this->setMethod('PUT');

        return $this;
    }


    public function is_delete()
    {

        $this->setMethod('DELETE');

        return $this;
    }


    private function saveCookie(){

        $params = http_build_query($this->cookie, null, ';');

        return $this->setOpt(CURLOPT_COOKIE, $params);
    }


    public function addCookie($name, $value){

        $this->cookie[$name] = $value;

        return $this->saveCookie();
    }

    public function setCookie(array $params = array()){

        $this->cookie = $params;

        return $this->saveCookie();
    }

    public function getCookie($key = false){

        if($key)
            return isset($this->cookie[$key])?$this->cookie[$key]:null;

        return $this->cookie;
    }

    public function removeCookie($name){

        if(isset($this->cookie))
            unset($this->cookie[$name]);

        return $this->saveCookie();
    }

    public function setCookieFile($patch, $save = false){

        if(file_exists($patch)){

            if($save)
                $this->setOpt(CURLOPT_COOKIEJAR, $patch);

            $this->setOpt(CURLOPT_COOKIEFILE, $patch);
        }

        return $this;
    }

    public function setBasicAuthentication($username, $password){

        $this->setOpt(CURLOPT_USERPWD, $username . ":" . $password);

        return $this;
    }

    public function setProxy($proxy, $type = '', $username = '', $password = '', $type_auth = CURLAUTH_BASIC){

        if($type=='SOCKS5')
            $this->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

        if($username) {

            $this->setOpt(CURLOPT_PROXYUSERPWD, "$username:$password");

            $this->setOpt(CURLOPT_PROXYAUTH, $type);
        }

        $this->setOpt(CURLOPT_PROXY, $proxy);

        return $this;
    }

    public function setReferrer($referrer){

        $this->referrer = $referrer;

        $this->setOpt(CURLOPT_REFERER, $referrer);

        return $this;
    }

    public function getReferrer(){

        return $this->referrer;
    }

    public function setUserAgent($ua){

        $this->userAgent = $ua;

        $this->setOpt(CURLOPT_USERAGENT, $ua);

        return $this;
    }

    public function getUserAgent(){

        return $this->userAgent;
    }

    private function saveHeaders(){

        $headers = array();

        foreach($this->headers as $k=>$v){
            $headers[] = "$k:$v";
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    public function setHeaders(array $headers){

        $this->headers = $headers;

        return $this->saveHeaders();
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function removeHeader($key){

        unset($this->headers[$key]);

        return $this;
    }

    public function getHeaders($key = false){

        if(isset($this->headers[$key]))
            return $this->headers[$key];

        return $this->headers;
    }

    public function setOpt($opt, $value){

        curl_setopt($this->ch, $opt, $value);

        return $this;
    }

    public function setOptArray(array $data){

        foreach($data as $k=>$v){

            $this->setOpt($k, $v);
        }

        return $this;
    }

    public function setResponseCallback($callback){

        if(is_callable($callback))
            $this->ResponseCallback = $callback;

        return $this;
    }

    public function start(){

        $data = curl_exec($this->ch);

        $this->result = $data;

        if($this->ResponseCallback && is_callable($this->ResponseCallback)) {

            $this->resultCallback = call_user_func_array ( $this->ResponseCallback, array('object'=>$this,'data'=>$data));
        }

        return $data;
    }

    public function reset(){

        curl_reset($this->ch);

        return $this;
    }

    public function close(){

        curl_close($this->ch);

        return $this;
    }

}