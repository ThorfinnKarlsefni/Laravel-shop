<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $addresses = [
            ['北京市','市辖区','东城区'],
            ['贵州省','六盘水','场坝中心'],
            ["江苏省", "南京市", "浦口区"],
            ["江苏省", "苏州市", "相城区"],
            ["广东省", "深圳市", "福田区"]
        ];

        $addresses = $this->faker->randomElement($addresses);
        return [
            'province'  =>  $addresses[0],
            'city'  =>  $addresses[1],
            'district'  =>  $addresses[2],
            'address'   =>  sprintf('第%d街道第%d号',$this->faker->randomNumber(2),$this->faker->randomNumber(3)),
            'zip'   =>  $this->faker->postcode,
            'contact_name'  =>  $this->faker->name,
            'contact_phone' => $this->faker->phoneNumber,
        ];
    }
}
