<?php

$urlProtocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') == FALSE ? 'http' : 'https';
$urlHost = $_SERVER['HTTP_HOST'];
