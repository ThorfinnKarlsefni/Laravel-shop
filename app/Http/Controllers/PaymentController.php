<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order){
        $this->authorize('own',$order);

        if($order->paid_at || $order->closed){
            return new InvalidRequestException('订单状态不正确');
        }

        return app('alipay')->web([
            'out_trade_no' => $order->no,
            'total_amount'  => $order->total_amount,
            'subject' => '支付 Laravel Shop 的订单' . $order->no,
        ]);
    }

    public function alipayReturn(){
        $data = app('alipay')->callback();
        dd($data);
    }

    public function alipayNotify(){
        $data = app('alipay')->callback();
        \Log::debug('Alipay notify',$data->all());
    }
}
