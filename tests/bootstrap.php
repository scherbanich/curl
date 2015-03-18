<?php


define('TEST_SERVER', 'http://'.SERVER_HOST.':'.SERVER_PORT.'/');

$command_server = sprintf(
    PHP_PATH.' -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
    SERVER_HOST,
    SERVER_PORT,
    __DIR__.'/remote'
);

$output = array();
exec($command_server, $output);

if (!isset($output[0])) {
    die('Failed to start php server. Aborting.'.PHP_EOL);
}

$pid_server = (int) $output[0];

register_shutdown_function(function () use ($pid_server) {
    exec('kill '.$pid_server.' >/dev/null 2>&1');
});

sleep(1);
