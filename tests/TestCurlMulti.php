<?php

namespace tests;

require __DIR__.'/../src/CurlMultiInterface.php';

require __DIR__.'/../src/CurlMulti.php';


class TestCurlMulti extends \PHPUnit_Framework_TestCase {

    public function testCreate()
    {

        $curlMulti = new \curl\CurlMulti();

        $this->assertTrue($curlMulti instanceof \curl\CurlMulti);

        $curlMulti->close();
    }

    public function testAddRequest(){

        $request = new \curl\Request(TEST_SERVER, 1, function($r,$d){

            $this->assertInternalType('object',json_decode($d));

        }, array(CURLOPT_RETURNTRANSFER => true));


        $curlMulti = new \curl\CurlMulti();

        $curlMulti->addRequest($request);

        $this->assertEquals($curlMulti->findRequestById(1), $request);

        $curlMulti->close();
    }

    public function testCreateRequest(){

        $curlMulti = new \curl\CurlMulti();

        $curlMulti->createRequest(TEST_SERVER, 2, function($r,$d){

            $this->assertInternalType('object',json_decode($d));

        }, array(CURLOPT_RETURNTRANSFER => true));

        $this->assertInstanceOf('\curl\Request',$curlMulti->findRequestById(2));

        $curlMulti->close();
    }
}
