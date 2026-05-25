<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;

class RegionController extends Controller
{
    /**
     * 所有地区平铺列表     * 杩斿洖: [{ id, name, parent_id, level }]
     */
    public function all()
    {
        $regions = Region::orderBy('id')->get();

        $list = $regions->map(function ($r) {
            return [
                'id'        => $r->id,
                'name'      => $r->name,
                'parent_id' => $r->parent_id,
                'level'     => $r->level,
            ];
        });

        return api_response($list);
    }

    /**
     * 鍦板尯鏍戝舰缁撴瀯锛堢渷-甯?鍖猴級
     * 杩斿洖: { list: [{ id, name, city: [{ id, name, region: [{ id, name }] }] }] }
     */
    public function tree()
    {
        $regions = Region::orderBy('id')->get();

        // 鐪侊紙level=1锛?        $provinces = $regions->where('level', 1)->values();

        $list = $provinces->map(function ($province) use ($regions) {
            // 甯傦紙level=2, parent_id=鐪乮d锛?            $cities = $regions->where('level', 2)->where('parent_id', $province->id)->values();

            $cityList = $cities->map(function ($city) use ($regions) {
                // 鍖猴紙level=3, parent_id=甯俰d锛?                $districts = $regions->where('level', 3)->where('parent_id', $city->id)->values();

                $regionList = $districts->map(function ($district) {
                    return [
                        'id'   => $district->id,
                        'name' => $district->name,
                    ];
                });

                return [
                    'id'     => $city->id,
                    'name'   => $city->name,
                    'region' => $regionList->values()->toArray(),
                ];
            });

            return [
                'id'   => $province->id,
                'name' => $province->name,
                'city' => $cityList->values()->toArray(),
            ];
        });

        return api_response([
            'list' => $list->values()->toArray(),
        ]);
    }
}