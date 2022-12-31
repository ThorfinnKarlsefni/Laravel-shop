<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

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
            'ngrok-skip-browser-warning' => true
        ]);
    }

    public function alipayReturn(){
        try{
            app('alipay')->callback();
        }catch (\Exception $e){
            return view('pages.error',['msg' => '数据不正确']);
        }
        return view('pages.success',['msg' => '付款成功']);
    }

    public function alipayNotify(){
        $data = app('alipay')->callback();
        if(!in_array($data,['TREAD_SUCCESS','TRADE_FINISHED'])){
            return app('alipay')->success();
        }

        $order = Order::where('no',$data->out_trade_no)->first();
        if(!$order){
            return 'fail';
        }

        if($order->paid_at){
            return app('alipay')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'alipay',
            'payment_no'    => $data->trade_no,
        ]);

        return app('alipay')->success();
    }
}
