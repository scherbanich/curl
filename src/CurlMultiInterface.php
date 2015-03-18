<?php

namespace curl;

interface CurlMultiInterface {

    public function addRequest(Request $data);

    public function close();
}