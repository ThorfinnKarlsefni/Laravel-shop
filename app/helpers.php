<?php

function test_helper(){
    return "OK";
}

function route_class(){
    return str_replace('.','_',Route::currentRouteName());
}
