<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

//  implements ShouldQueue 代表此监听器是异步执行的
class UpdateProductSoldCount implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();
        $order->load('items.product');
        foreach ($order->items as $item){
            $product = $item->product;
            $soldCount = OrderItem::query()
                ->where('product_id',$product->id)
                ->whereHas('order',function ($query){
                    $query->whereNotNull('paid_at');
                })->sum('amount');

            $product->update([
                'sold_count' => $soldCount
            ]);
        }
    }
}
