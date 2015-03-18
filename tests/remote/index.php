<?php

 header('Content-Type: application/json');

 setcookie("TEST", "HELLO WORLD", time()+3600,'/');

 $data = array();

 $data['SERVER'] = $_SERVER;

 $data['POST'] = $_POST;

 $data['GET'] = $_GET;

 $data['FILES'] = $_FILES;

 $data['COOKIE'] = $_COOKIE;

 $st_data = file_get_contents('php://input');

 parse_str($st_data, $output);

 $data['input'] = $output;

 $data['headers'] = array();
 foreach ($_SERVER as $key => $value) {
  if (substr($key, 0, 6) === 'HTTP_X' && $key !== 'HTTP_X_HTTP_METHOD_OVERRIDE') {
   $data['headers'][substr($key, 5)] = $value;
  }
 }

 echo json_encode($data);