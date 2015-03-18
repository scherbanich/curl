[![Build Status](https://travis-ci.org/scherbanich/curl.svg?branch=master)](https://travis-ci.org/scherbanich/curl)

Usage Request class
-----

```php

$request = new \curl\Request('http://www.example.com',1);

$request->start();

```


```php

$request = new \curl\Request('http://www.example.com',1,function($request,$data){

    if($request->getInfo(CURLINFO_HTTP_CODE)==200)
        return strlen($data);

    return 0;
});

$request->setOpt(CURLOPT_RETURNTRANSFER, true);

$request->start();

if(strlen($request->result) == $request->resultCallback){

    echo "OK!";
}

```


```php

$request = new \curl\Request('http://www.example.com', 1, null, array(CURLOPT_RETURNTRANSFER => true));

$request ->is_post()
         ->addCookie('test_cookie','hello')
         ->setUserAgent('Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7')
         ->setProxy('66.96.200.39:80')
         ->setHeaders(
                array(
                    'X-TEST'=>'hello'
                )
         )
         ->setParams(
                array(
                    'test' => 'test'
                )
         );

$request->start();

echo $request->result;

```


Usage CurlMulti class
-----

```php

    $callback = function($request,$data){

        echo "Callback result {$request->id}: ".strlen($data)." status: {$request->getInfo(CURLINFO_HTTP_CODE)} error: {$request->getError()}".'<br><br>';
    };



    $request = new \curl\Request('http://www.example.com',1, $callback);

    $request
            ->setMethod('POST')
            ->setParams(
                array(
                    'test' => 'test'
            ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true,
                    CURLOPT_NOBODY          => 0
            ));




    $request2 = new \curl\Request('http://www.example.com', 2, $callback);

    $request2->setOpt(CURLOPT_RETURNTRANSFER, true);


    $curlMulti = new \curl\CurlMulti;

    $curlMulti->addRequest($request)

                ->addRequest($request2)

                ->addRequest(new \curl\Request('http://www.example.com', 3, $callback, array(CURLOPT_RETURNTRANSFER => true)))

                ->addRequest(new \curl\Request('http://www.example.com', 4, $callback, array(CURLOPT_RETURNTRANSFER => true)))

                ->start();


    echo "Get result: ".strlen($request2->result);

```
