<?php

namespace curl;

interface RequestInterface {

    public function init();

    public function close();

    public function reset();

    public function copyHandle();

    public function pause($bitmask);

    public function unescape($str);

    public function setUserAgent($ua);

    public function getUserAgent();

    public function setReferrer($referrer);

    public function addHeader($header, $value);

    public function setHeaders(array $headers);

    public function removeHeader($key);

    public function getHeaders($key = false);

    public function setCookie(array $value);

    public function addCookie($name, $value);

    public function getCookie($key = false);

    public function removeCookie($name);

    public function setCookieFile($patch, $save = false);

    public function setBasicAuthentication($username, $password);

    public function setProxy($proxy, $type, $username = '', $password = '', $type_auth = CURLAUTH_BASIC);

    public function setMethod($name);

    public function is_post();

    public function is_put();

    public function is_delete();

    public function setParams(array $data);

    public function addParams($name, $value);

    public function getParams($key = false);

    public function removeParams($name);

    public function setOpt($opt, $value);

    public function setOptArray(array $data);

    public function getResource();

    public function getInfo();

    public function getError();

}