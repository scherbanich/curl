<?php

namespace curl;

interface CurlMultiInterface {

    public function addRequest(Request $data);

    public function createRequest($url, $id, $callback = null, $options = array());

    public function close();
}