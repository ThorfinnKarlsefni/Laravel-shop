<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crownfunding';
    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品'
    ];
    protected $fillable = [
        'title','description','image','on_sale',
        'rating','sold_count','review_count','price','type'
    ];

    protected $casts = [
        'on_sale' => 'boolean'  // on_sale 是一个布尔类型的字段
    ];
    public function skus(){
        return $this->hasMany(ProductSku::class);
    }

    public function getImageUrlAttribute(){
        if(Str::startsWith($this->attributes['image'],['http://','https://'])){
            return $this->attributes['image'];
        }else{
//            abort(2133,\Storage::disk('public'));exit;
            return \Storage::disk('public')->url($this->attributes['image']);
        }
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function crowdfunding(){
        return $this->hasOne(CrowdfundingProduct::class);
    }
}
