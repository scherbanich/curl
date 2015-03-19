<?php

namespace curl;

class CurlMulti implements CurlMultiInterface {

    private $tasks = null;

    private $requests = null;

    private $cmh = null;

    private function findTask($data){

        $this->tasks->rewind();

        while($this->tasks->valid()){

            $object = $this->tasks->current();

            if(current($object)===$data)
                return $this->tasks->getInfo();

            $this->tasks->next();
        }

        return null;
    }

    private function findRequestByTask($data){

        return $this->requests[$this->findTask($data)];
    }

    public function __construct(){

        $this->cmh = curl_multi_init();

        $this->tasks = new \SplObjectStorage();

        $this->requests = [];

        return $this;
    }

    public function addRequest(Request $data){

        $curl = $data->getResource();

        curl_multi_add_handle($this->cmh, $data->getResource());

        $this->tasks->attach((object)$curl,$data->id);

        $this->requests[$data->id] = $data;

        return $this;
    }


    public function createRequest($url, $id, $callback = null, $options = array()){

        return $this->addRequest(new Request($url, $id, $callback, $options));
    }


    public function start(){

        $active = null;

        do {
            $mrc = curl_multi_exec($this->cmh, $active);
        }
        while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && ($mrc == CURLM_OK)) {

            if (curl_multi_select($this->cmh) != -1) {
                do {
                    $mrc = curl_multi_exec($this->cmh, $active);

                    $info = curl_multi_info_read($this->cmh);

                    if(is_array($info) && $info['msg']==CURLMSG_DONE){

                        $ch = $info['handle'];

                        $content = @curl_multi_getcontent($ch);

                        $request = $this->findRequestByTask($ch);

                        $request->setResponse($content);

                        curl_multi_remove_handle($this->cmh, $ch);

                        $request->close();
                    }
                }
                while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        return $this->requests;
    }

    public function close(){

        $this->tasks->removeAllExcept(new \SplObjectStorage());

        curl_multi_close($this->cmh);

        return $this;
    }
}