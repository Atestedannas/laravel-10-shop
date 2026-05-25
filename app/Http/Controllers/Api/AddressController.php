<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * 收货地址列表     */
    public function list()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $addresses = MemberAddress::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $list = $addresses->map(function ($addr) {
            $region = implode(' ', array_filter([$addr->province, $addr->city, $addr->district]));

            return [
                'address_id' => $addr->id,
                'name'       => $addr->name,
                'phone'      => $addr->mobile,
                'region'     => $region,
                'detail'     => $addr->detail,
                'is_default' => (bool) $addr->is_default,
            ];
        });

        return api_response($list);
    }

    /**
     * 获取默认地址ID
     */
    public function defaultId()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $default = MemberAddress::where('user_id', $user->id)
            ->where('is_default', 1)
            ->first();

        return api_response([
            'address_id' => $default ? $default->id : 0,
        ]);
    }

    /**
     * 地址详情
     * 参数: addressId
     */
    public function detail(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $addressId = $request->input('addressId');
        if (!$addressId) {
            return api_response(null, '地址ID不能为空', 500);
        }

        $addr = MemberAddress::where('user_id', $user->id)->find($addressId);
        if (!$addr) {
            return api_response(null, '地址不存在', 400);
        }

        return api_response([
            'address_id'  => $addr->id,
            'name'        => $addr->name,
            'phone'       => $addr->mobile,
            'region'      => [$addr->province_id, $addr->city_id, $addr->district_id],
            'region_text' => implode(' ', array_filter([$addr->province, $addr->city, $addr->district])),
            'detail'      => $addr->detail,
            'is_default'  => (bool) $addr->is_default,
        ]);
    }

    /**
     * 添加收货地址
     * 参数: form.name, form.phone, form.region[], form.detail
     */
    public function add(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $form = $request->input('form', []);

        $name   = $form['name'] ?? '';
        $phone  = $form['phone'] ?? '';
        $region = $form['region'] ?? [];
        $detail = $form['detail'] ?? '';

        if (empty($name) || empty($phone)) {
            return api_response(null, '姓名和手机号不能为空', 400);
        }

        $province  = $region[0] ?? '';
        $city      = $region[1] ?? '';
        $district  = $region[2] ?? '';

        // 获取省市区名称
        $provinceName = '';
        $cityName     = '';
        $districtName = '';

        if (is_numeric($province)) {
            $p = \App\Models\Region::find($province);
            $provinceName = $p ? $p->name : '';
        } else {
            $provinceName = $province;
        }

        if (is_numeric($city)) {
            $c = \App\Models\Region::find($city);
            $cityName = $c ? $c->name : '';
        } else {
            $cityName = $city;
        }

        if (is_numeric($district)) {
            $d = \App\Models\Region::find($district);
            $districtName = $d ? $d->name : '';
        } else {
            $districtName = $district;
        }

        $isDefault = MemberAddress::where('user_id', $user->id)->count() == 0 ? 1 : 0;

        $addr = MemberAddress::create([
            'user_id'     => $user->id,
            'name'        => $name,
            'mobile'      => $phone,
            'province_id' => (int) $province,
            'city_id'     => (int) $city,
            'district_id' => (int) $district,
            'province'    => $provinceName,
            'city'        => $cityName,
            'district'    => $districtName,
            'detail'      => $detail,
            'is_default'  => $isDefault,
        ]);

        return api_response([
            'address_id' => $addr->id,
        ], '地址添加成功);
    }

    /**
     * 编辑收货地址
     * 参数: addressId, form
     */
    public function edit(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $addressId = $request->input('addressId');
        $form      = $request->input('form', []);

        if (!$addressId) {
            return api_response(null, '地址ID不能为空', 500);
        }

        $addr = MemberAddress::where('user_id', $user->id)->find($addressId);
        if (!$addr) {
            return api_response(null, '地址不存在', 400);
        }

        if (isset($form['name'])) {
            $addr->name = $form['name'];
        }
        if (isset($form['phone'])) {
            $addr->mobile = $form['phone'];
        }
        if (isset($form['region'])) {
            $region = $form['region'];
            $addr->province_id = (int) ($region[0] ?? 0);
            $addr->city_id     = (int) ($region[1] ?? 0);
            $addr->district_id = (int) ($region[2] ?? 0);

            // 更新省市区名称
            $p = \App\Models\Region::find($addr->province_id);
            $addr->province = $p ? $p->name : '';

            $c = \App\Models\Region::find($addr->city_id);
            $addr->city = $c ? $c->name : '';

            $d = \App\Models\Region::find($addr->district_id);
            $addr->district = $d ? $d->name : '';
        }
        if (isset($form['detail'])) {
            $addr->detail = $form['detail'];
        }

        $addr->save();

        return api_response(null, '地址修改成功);
    }

    /**
     * 设置默认地址     * 参数: addressId
     */
    public function setDefault(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $addressId = $request->input('addressId');
        if (!$addressId) {
            return api_response(null, '地址ID不能为空', 500);
        }

        $addr = MemberAddress::where('user_id', $user->id)->find($addressId);
        if (!$addr) {
            return api_response(null, '地址不存在', 400);
        }

        // 清除原有默认地址标记
        MemberAddress::where('user_id', $user->id)
            ->where('is_default', 1)
            ->update(['is_default' => 0]);

        // 设置当前地址为默认        $addr->is_default = 1;
        $addr->save();

        return api_response(null, '默认地址设置成功');
    }

    /**
     * 删除收货地址
     * 参数: addressId
     */
    public function remove(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return api_response(null, '未登录', 401);
        }

        $addressId = $request->input('addressId');
        if (!$addressId) {
            return api_response(null, '地址ID不能为空', 500);
        }

        $addr = MemberAddress::where('user_id', $user->id)->find($addressId);
        if (!$addr) {
            return api_response(null, '地址不存在', 400);
        }

        $wasDefault = $addr->is_default;
        $addr->delete();

        // 删除默认地址后，自动设置最新地址为默认        if ($wasDefault) {
            $latest = MemberAddress::where('user_id', $user->id)
                ->orderBy('id', 'desc')
                ->first();
            if ($latest) {
                $latest->is_default = 1;
                $latest->save();
            }
        }

        return api_response(null, '地址删除成功);
    }
}