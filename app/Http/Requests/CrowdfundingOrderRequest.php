<?php

namespace App\Http\Requests;

use App\Models\CrowdfundingProduct;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Validation\Rule;

class CrowdfundingOrderRequest extends Request
{
    public function rules(){
        return [
            'sku_id' => [
                'required',
                function($attribute,$value,$fail){
                    if(!$sku = ProductSku::find($value)){
//                        return $fail('该商品不存在');
                        return $fail('The Product does not exist');
                    }

                    if($sku->product->type !== Product::TYPE_CROWDFUNDING){
                        return $fail('The Product does not support CrowdFunding');
                    }

                    if(!$sku->product->on_sale){
//                        return $fail('该商品未上架');
//                        return $fail('The Product Not on the shelf');
                        return $fail('The Product No Sold');
                    }

                    if($sku->product->crowdfunding->status !== CrowdfundingProduct::STATUS_FUNDING){
//                        return $fail('该商品众筹已结束');
                        return $fail('Commodity CrowdfundingProduct is over');
                    }

                    if($sku->stock === 0){
//                        return $fail('该商品已售完');
                        return $fail('The Good are sold out');
                    }

                    if($this->input('amount') > 0 && $sku->stock < $this->input('amount')){
                        return $fail('该商品库存不足');
                    }
                }
            ],

            'amount' => ['required','integer','min:1'],
            'address_id' => [
                'required',
                Rule::exists('user_addresses','id')->where('user_id',$this->user()->id)
            ],
        ];
    }
}
