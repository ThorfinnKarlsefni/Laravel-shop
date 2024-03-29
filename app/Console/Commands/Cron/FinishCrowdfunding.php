<?php

namespace App\Console\Commands\Cron;

use App\Jobs\RefundCrowdfundingOrders;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FinishCrowdfunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:finish-crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结束众筹';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CrowdfundingProduct::query()
            ->where('end_at','<=',Carbon::now())
            ->where('status',CrowdfundingProduct::STATUS_FUNDING)
            ->get()
            ->each(function(CrowdfundingProduct $crowdfunding){
                if($crowdfunding->target_amount > $crowdfunding->total_amount){
                    $this->crowdfundingFailed($crowdfunding);
                }else{
                    $this->crowdfudingSucceed($crowdfunding);
                }
            });
    }

    protected function crowdfundingSucceed(CrowdfundingProduct $crowdfunding){
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_SUCCESS
        ]);
    }

    protected function crowdfundingFailed(CrowdfundingProduct $crowdfunding){
        $crowdfunding->update([
            'status' => CrowdfundingProduct::STATUS_FAIL
        ]);
        dispatch(new RefundCrowdfundingOrders($crowdfunding));
//        $orderService = app(OrderService::class);
//        Order::query()
//            ->where('type',Order::TYPE_CROWDFUNDING)
//            ->whereNotNull('paid_at')
//            ->whereHas('imtes',function($query) use ($crowdfunding){
//                $query->where('product_id',$crowdfunding->product_id);
//            })
//            ->get()
//            ->each(function(Order $order) use ($orderService){
//                $orderService->refundOrder($order);
//            });
    }
}
