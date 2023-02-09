<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrowdfundingProduct extends Model
{
    use HasFactory;

    const STATUS_FUNDING = 'funding';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    protected $fillable = [
        'total_amount',
        'target_amount',
        'user_count',
        'status',
        'end_at',
    ];
    // end_at 转为carbon 类型
    protected $dates = ['end_at'];

    public $timestamps = false;

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function getPercentAttribute($key)
    {
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];
        return floatval(number_format($value * 100,2,'.',''));
    }
}

