<?php

    namespace examples;

    require '../src/RequestInterface.php';

    require '../src/Request.php';


    echo "REQUEST #1 :<br><br>";

    $request = new \curl\Request('https://github.com/scherbanich',1);

    #set GET params and request options

    $request
            ->setMethod('GET')
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

    echo 'Result 1:  '.strlen($result).'<br><br>';


    #change GET params

    $request->setParams(
        array(
            'tab' => 'activity'
        ));

    $result = $request->start();

    echo 'Result 2:  '.strlen($result).'<br><br>';


    #change user agent params

    $request->setUserAgent("Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0 like Mac OS X; en-us) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A293 Safari/6531.22.7");

    $result = $request->start();

    echo 'Result 3:  '.strlen($result).'<br><br>';

    $request->close();




    /*---------------------------------------------------------------------*/

    echo "<hr>REQUEST #2 :<br><br>";

    $request2 = new \curl\Request('http://php.net/', 2, function($request,$data){

        echo 'Callback result: '.strlen($data).'<br><br>';
    });

    $result = $request2     ->setOpt(CURLOPT_RETURNTRANSFER,true)
                            ->setOpt(CURLOPT_HEADER, 0)
                            ->setOpt(CURLOPT_NOBODY, 0)
                            ->start();

    echo 'Result: '.strlen($result).'<br><br>';

    echo 'HTTP code: '.$request2->getInfo(CURLINFO_HTTP_CODE);

    $request2->close();
