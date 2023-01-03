<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例'
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
//        'description'
    ];

    protected $casts = [
        'enabled' =>  'boolean',
    ];

    protected $dates = ['not_before','not_after'];

    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }

        return $str.'减'.str_replace('.00', '', $this->value);
    }

    public static function findAvailableCode($length = 16){
        do {
            $code = strtoupper(Str::random($length));
        }while (self::query()->where('code',$code)->exists());

        return $code;
    }
}