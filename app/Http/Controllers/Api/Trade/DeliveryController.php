<?php

namespace App\Http\Controllers\Api\Trade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * 蹇€掑叕鍙稿垪琛?     */
    public function expressList()
    {
        $list = [
            ['code' => 'SF', 'name' => '椤轰赴閫熻繍'],
            ['code' => 'YTO', 'name' => '鍦嗛€氶€熼€?],
            ['code' => 'ZTO', 'name' => '涓€氬揩閫?],
            ['code' => 'STO', 'name' => '鐢抽€氬揩閫?],
            ['code' => 'YD', 'name' => '闊佃揪蹇€?],
            ['code' => 'EMS', 'name' => 'EMS'],
            ['code' => 'JD', 'name' => '浜笢鐗╂祦'],
            ['code' => 'DB', 'name' => '寰烽偊蹇€?],
        ];

        return api_success($list);
    }

    /**
     * 鑷彁闂ㄥ簵鍒楄〃
     */
    public function pickUpStoreList(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        // 妯℃嫙鏁版嵁
        $stores = [
            [
                'id' => 1,
                'name' => '骞垮窞澶╂渤鏃楄埌搴?,
                'address' => '骞垮窞甯傚ぉ娌冲尯澶╂渤璺?28鍙?,
                'phone' => '020-88888888',
                'hours' => '09:00-21:00',
            ],
            [
                'id' => 2,
                'name' => '骞垮窞瓒婄搴?,
                'address' => '骞垮窞甯傝秺绉€鍖哄寳浜矾168鍙?,
                'phone' => '020-66666666',
                'hours' => '10:00-22:00',
            ],
            [
                'id' => 3,
                'name' => '娣卞湷鍗楀北搴?,
                'address' => '娣卞湷甯傚崡灞卞尯绉戞妧鍥矾88鍙?,
                'phone' => '0755-88888888',
                'hours' => '09:00-21:00',
            ],
        ];

        return api_success([
            'list' => $stores,
            'total' => count($stores),
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 鑷彁闂ㄥ簵璇︽儏
     */
    public function pickUpStoreGet(Request $request)
    {
        $id = $request->input('id');

        $stores = [
            1 => [
                'id' => 1,
                'name' => '骞垮窞澶╂渤鏃楄埌搴?,
                'address' => '骞垮窞甯傚ぉ娌冲尯澶╂渤璺?28鍙?,
                'phone' => '020-88888888',
                'hours' => '09:00-21:00',
                'latitude' => '23.129163',
                'longitude' => '113.264435',
            ],
            2 => [
                'id' => 2,
                'name' => '骞垮窞瓒婄搴?,
                'address' => '骞垮窞甯傝秺绉€鍖哄寳浜矾168鍙?,
                'phone' => '020-66666666',
                'hours' => '10:00-22:00',
                'latitude' => '23.125643',
                'longitude' => '113.273420',
            ],
            3 => [
                'id' => 3,
                'name' => '娣卞湷鍗楀北搴?,
                'address' => '娣卞湷甯傚崡灞卞尯绉戞妧鍥矾88鍙?,
                'phone' => '0755-88888888',
                'hours' => '09:00-21:00',
                'latitude' => '22.543099',
                'longitude' => '113.949692',
            ],
        ];

        if (!isset($stores[$id])) {
            return api_error(404, '闂ㄥ簵涓嶅瓨鍦?);
        }

        return api_success($stores[$id]);
    }
}