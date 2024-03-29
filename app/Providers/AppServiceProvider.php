<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Yansongda\Pay\Pay;
use Monolog\Logger;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //往服务容器中注入一个名为alipay的单例对象
        $this->app->singleton('alipay',function(){
            //此处$config = config('pay.alipay');
            $config = config('pay');
            //判断当前项目运行环境是否为线上环境
            if(app()->environment() !== 'production'){
                //修改此处$config['mode'] = 'dev';
                $config['alipay']['default']['mode']  = 1;
                $config['log']['level'] = Logger::DEBUG;
            }else{
                $config['log']['level'] = Logger::WARNING;
            }
            //调用Yansongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay');
            if (app()->environment() !== 'production') {
                $config['wechat']['default']['mode']  = 1;
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \Illuminate\Pagination\Paginator::useBootstrap();
        // 当 Laravel 渲染 products.index 和 products.show 模板时，就会使用 CategoryTreeComposer 这个来注入类目树变量
        // 同时 Laravel 还支持通配符，例如 products.* 即代表当渲染 products 目录下的模板时都执行这个 ViewComposer
        \View::composer(['products.index','products.show'],
        \App\Http\ViewComposers\CategoryTreeComposer::class);
    }
}
