<?php

require 'action.php';

$REQUEST_URI = $_SERVER['REQUEST_URI'];
if ($REQUEST_URI === '/') {
    $router = 'index';
} else {
    $router = substr($REQUEST_URI, 1);
}

$router();
