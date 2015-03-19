<?php

    namespace examples;

    require '../src/RequestInterface.php';

    require '../src/CurlMultiInterface.php';

    require '../src/Request.php';

    require '../src/CurlMulti.php';


    $callback = function($request,$data){

        echo "Callback result {$request->id}: ".strlen($data)." status: {$request->getInfo(CURLINFO_HTTP_CODE)} error: {$request->getError()}".'<br><br>';
    };



    $request = new \curl\Request('https://github.com/shcherbanich',1, $callback);

    $request
            ->setMethod('GET')
            ->setParams(
                array(
                    'tab' => 'repositories'
            ))
            ->setOptArray(
                array(
                    CURLOPT_RETURNTRANSFER  => true
            ));




    $request2 = new \curl\Request('http://php.net/', 2, $callback);

    $request2->setOpt(CURLOPT_RETURNTRANSFER, true);


    $curlMulti = new \curl\CurlMulti;

    $curlMulti->addRequest($request)

                ->addRequest($request2)

                ->addRequest(new \curl\Request('http://vk.ru/', 3, $callback, array(CURLOPT_RETURNTRANSFER => true)))

                ->addRequest(new \curl\Request('http://test.test/', 4, $callback, array(CURLOPT_RETURNTRANSFER => true)))

                ->start();


    echo "Get result: ".strlen($request->result);


    echo "<br><hr>";

    $curlMulti = new \curl\CurlMulti();

    $curlMulti->createRequest('https://github.com/shcherbanich', 2, function($r,$d){

        print_r(strlen($d));

    }, array(CURLOPT_RETURNTRANSFER => true));

    $curlMulti->start();

    $curlMulti->close();
