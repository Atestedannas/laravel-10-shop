<?php

namespace App\Http\Controllers\Api\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    /**
     * 閼惧嘲褰囪ぐ鎾冲閻劍鍩涢崷鏉挎絻閸掓銆冮敍鍫ョ帛鐠併倕婀撮崸鈧幒鎺戝閿?     */
    public function list(Request $request)
    {
        $addresses = MemberAddress::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get();

        return api_success($addresses);
    }

    /**
     * 閼惧嘲褰囬崡鏇氶嚋閸︽澘娼冪拠锔藉剰
     */
    public function get(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $address = MemberAddress::where('user_id', $request->user()->id)
            ->find($request->id);

        if (!$address) {
            return api_error(500, '閸︽澘娼冩稉宥呯摠閸?);
        }

        return api_success($address);
    }

    /**
     * 閸掓稑缂撻崷鏉挎絻
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50',
            'mobile'      => 'required|regex:/^1[3-9]\d{9}$/',
            'province_id' => 'nullable|integer',
            'city_id'     => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'province'    => 'required|string|max:50',
            'city'        => 'required|string|max:50',
            'district'    => 'required|string|max:50',
            'detail'      => 'required|string|max:500',
            'is_default'  => 'nullable|integer|in:0,1',
        ]);

        $userId = $request->user()->id;
        $validated['user_id'] = $userId;

        // 鐠佸彞璐熸妯款吇閺冭绱濋崣鏍ㄧХ閸忔湹绮妯款吇閸︽澘娼?        if (!empty($validated['is_default'])) {
            MemberAddress::where('user_id', $userId)->update(['is_default' => 0]);
        }

        $address = MemberAddress::create($validated);

        return api_success($address, '閸︽澘娼冮崚娑樼紦閹存劕濮?);
    }

    /**
     * 閺囧瓨鏌婇崷鏉挎絻
     */
    public function update(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $validated = $request->validate([
            'name'        => 'nullable|string|max:50',
            'mobile'      => 'nullable|regex:/^1[3-9]\d{9}$/',
            'province_id' => 'nullable|integer',
            'city_id'     => 'nullable|integer',
            'district_id' => 'nullable|integer',
            'province'    => 'nullable|string|max:50',
            'city'        => 'nullable|string|max:50',
            'district'    => 'nullable|string|max:50',
            'detail'      => 'nullable|string|max:500',
            'is_default'  => 'nullable|integer|in:0,1',
        ]);

        $userId  = $request->user()->id;
        $address = MemberAddress::where('user_id', $userId)->find($request->id);

        if (!$address) {
            return api_error(500, '閸︽澘娼冩稉宥呯摠閸?);
        }

        // 鐠佸彞璐熸妯款吇閺冭绱濋崣鏍ㄧХ閸忔湹绮妯款吇閸︽澘娼?        if (!empty($validated['is_default'])) {
            MemberAddress::where('user_id', $userId)->update(['is_default' => 0]);
        }

        $address->update(array_filter($validated, fn($v) => $v !== null));

        return api_success($address, '閸︽澘娼冮弴瀛樻煀閹存劕濮?);
    }

    /**
     * 閸掔娀娅庨崷鏉挎絻
     */
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $address = MemberAddress::where('user_id', $request->user()->id)
            ->find($request->id);

        if (!$address) {
            return api_error(500, '閸︽澘娼冩稉宥呯摠閸?);
        }

        $address->delete();

        return api_success(null, '閸︽澘娼冨鎻掑灩闂?);
    }
}