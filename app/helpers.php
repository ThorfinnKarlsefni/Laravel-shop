<?php

function test_helper(){
    return "OK";
}

function route_class(){
    return str_replace('.', '-', Route::currentRouteName());
}

function ngrok_url($routeName,$parameters = []){
    if(app()->environment('lcoal') && $url = config('app.ngork_url')){
        return $url.route($routeName,$parameters,false);
    }

    return route($routeName,$parameters);
}

