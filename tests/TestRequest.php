<?php

namespace tests;

require __DIR__.'/../src/RequestInterface.php';

require __DIR__.'/../src/Request.php';

class TestRequest extends \PHPUnit_Framework_TestCase {

    public function testExtensionLoaded() {

        $this->assertTrue(extension_loaded('curl'));
    }


    public function testCreate()
    {

        $request = new \curl\Request(TEST_SERVER,11);

        $this->assertTrue($request instanceof \curl\Request);

        $this->assertEquals($request->url,TEST_SERVER);

        $this->assertEquals($request->id,11);

        $this->assertNotInternalType('object',$request->ResponseCallback);

        $request->close();

        $callback = function($o,$r){

            return $o;
        };

        $request = new \curl\Request(TEST_SERVER,9,$callback);

        $this->assertEquals($request->url,TEST_SERVER);

        $this->assertEquals($request->id,9);

        $this->assertInternalType('object',$request->ResponseCallback);

        $request->close();
    }

    public function testGetResource(){

        $request = new \curl\Request(TEST_SERVER,1);

        $resource = $request->getResource();

        $this->assertTrue(is_resource($resource));

        $this->assertEquals(get_resource_type($resource),'curl');

        $request->close();
    }

    public function testCloseConnection(){

        $request = new \curl\Request(TEST_SERVER,1);

        $resource = $request->getResource();

        $this->assertTrue(is_resource($resource));

        $request->close();

        $this->assertNotTrue(is_resource($resource));
    }

    public function testCopyHandle(){

        $request = new \curl\Request(TEST_SERVER,1);

        $resource1 = $request->getResource();

        $resource2 = $request->copyHandle();

        $this->assertTrue(is_resource($resource2));

        $this->assertNotTrue($resource1 == $resource2);

        $request->close();
    }

    public function testGettersAndSetters(){

        $data = array('data_key'=>'data_value');

        $cookie = array('cookie_key'=>'cookie_value');

        $headers = array('header_key'=>'header_value');

        $ua = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7';

        $referrer = 'http://test.com';

        $request = new \curl\Request(TEST_SERVER,1);

        $request->setParams($data)
                ->setCookie($cookie)
                ->setHeaders($headers)
                ->setUserAgent($ua)
                ->setReferrer($referrer);

        $this->assertEquals($request->getParams(), $data);

        $this->assertEquals($request->getCookie(), $cookie);

        $this->assertEquals($request->getHeaders(), $headers);

        $this->assertEquals($request->getUserAgent(), $ua);

        $this->assertEquals($request->getReferrer(), $referrer);

        $request->close();
    }

    public function testStartRequest(){

        $request = new \curl\Request(TEST_SERVER,2);

        $request
            ->is_post()
            ->setParams(
                array(
                    'tab' => 'repositories'
                ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
                ));

        $result = $request->start();

        $this->assertInternalType('object',json_decode($result));

        $request->close();
    }

    public function testPostRequest(){

        $request = new \curl\Request(TEST_SERVER,1);

        $request
            ->is_post()
            ->setParams(
                array(
                    'tab' => 'repositories'
                ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
                ));

        $result = $request->start();

        $result = json_decode($result,true);

        $this->assertTrue(isset($result['POST']['tab']));

        $this->assertEquals('repositories',$result['POST']['tab']);

        $request->close();
    }

    public function testPutRequest(){

        $request = new \curl\Request(TEST_SERVER,1);

        $request
            ->is_put()
            ->setParams(
                array(
                    'tab' => 'repositories'
                ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
                ));

        $result = $request->start();

        $result = json_decode($result,true);

        $this->assertTrue(isset($result['input']['tab']));

        $this->assertTrue(isset($result['SERVER']['REQUEST_METHOD']));

        $this->assertEquals($result['SERVER']['REQUEST_METHOD'], 'PUT');

        $this->assertEquals('repositories',$result['input']['tab']);

        $request->close();
    }


    public function testDeleteRequest(){

        $request = new \curl\Request(TEST_SERVER,1);

        $request
            ->is_delete()
            ->setParams(
                array(
                    'tab' => 'repositories'
                ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
                ));

        $result = $request->start();

        $result = json_decode($result,true);

        $this->assertTrue(isset($result['input']['tab']));

        $this->assertTrue(isset($result['SERVER']['REQUEST_METHOD']));

        $this->assertEquals($result['SERVER']['REQUEST_METHOD'], 'DELETE');

        $this->assertEquals('repositories',$result['input']['tab']);

        $request->close();
    }

    public function testSetMethodRequest(){

        $request = new \curl\Request(TEST_SERVER,1);

        $request
            ->setMethod('PATCH')
            ->setParams(
                array(
                    'tab' => 'repositories'
                ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
                ));

        $result = $request->start();

        $result = json_decode($result,true);

        $this->assertTrue(isset($result['input']['tab']));

        $this->assertTrue(isset($result['SERVER']['REQUEST_METHOD']));

        $this->assertEquals($result['SERVER']['REQUEST_METHOD'], 'PATCH');

        $this->assertEquals('repositories',$result['input']['tab']);

        $request->close();
    }

    public function testCookie(){

        $request = new \curl\Request(TEST_SERVER,1);

        $cookie_file = __DIR__.'/remote/cookie.txt';

        file_put_contents($cookie_file,'');

        $request
            ->setCookie(array('test'=>1))
            ->addCookie('test_add',12)
            ->addCookie('test_remove',11)
            ->removeCookie('test_remove')
            ->setCookieFile($cookie_file, true)
            ->setOpt(CURLOPT_RETURNTRANSFER,true);

        $result = $request->start();

        $this->assertNotTrue(strlen(file_get_contents($cookie_file))>10);

        $request->close();

        $result = json_decode($result,true);

        $this->assertTrue(strlen(file_get_contents($cookie_file))>10);

        $this->assertTrue(isset($result['COOKIE']['test']));

        $this->assertTrue(isset($result['COOKIE']['test_add']));

        $this->assertNotTrue(isset($result['COOKIE']['test_remove']));
    }

    public function testHeaders(){

        $request = new \curl\Request(TEST_SERVER,1);

        $request
            ->setHeaders(
                array(
                    'X-TEST'=>'hello'
                )
            )
            ->setOpt(CURLOPT_RETURNTRANSFER,true);

        $result = $request->start();

        $result = json_decode($result,true);

        $this->assertTrue(isset($result['headers']['X_TEST']));

        $this->assertEquals($result['headers']['X_TEST'], 'hello');

        $request->close();
    }
}
