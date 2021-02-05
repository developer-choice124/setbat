<?php

function ms_isset($x){
    return isset($x) && $x != null && strlen(trim($x))>0;
}
function current_url() {
    return "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function projectPath() {
    return "foodorder/Daksh247New";
}

function ms_is_local() {
    $whitelist = array(
        '127.0.0.1',
        '::1'
    );

    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

function DocRoot($path) {
    if (ms_is_local()) {
        return $_SERVER["DOCUMENT_ROOT"] . "/" . projectPath() . "/" . $path;
    } else {
        return $_SERVER["DOCUMENT_ROOT"] . "/" . $path;
    }
}

function BaseUrl($url) {
    if (ms_is_local()) {
        return "http://localhost/" . projectPath() . "/" . $url;
    } else {
        return "http://www.daksh247.com" . '/' . $url;
    }
}
function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
        header('Content-Type: application/json');
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
    
}
