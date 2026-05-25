<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponTemplate;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * 浼樻儬鍒稿垎椤?     */
    public function page(Request $request)
    {
        $user = auth('sanctum')->user();
        $status = $request->input('status');
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        $query = Coupon::with('template')
            ->where('user_id', $user->id);

        if ($status !== null) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $list = $query->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        return api_success([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * 领取优惠券
     */
    public function take(Request $request)
    {
        $user = auth('sanctum')->user();
        $templateId = $request->input('templateId') ?? $request->input('coupon_template_id');
        
        if (!$templateId) {
            return api_error(500, '优惠券模板ID不能为空');
        }
        
        $template = CouponTemplate::find($templateId);
        if (!$template) {
            return api_error(404, '优惠券模板不存在');
        }
        
        // 检查是否已领取
        $exists = Coupon::where('user_id', $user->id)
            ->where('template_id', $templateId)
            ->exists();
            
        if ($exists) {
            return api_error(500, '您已领取过此优惠券');
        }
        
        // 检查库存
        if ($template->total_count > 0 && $template->take_count >= $template->total_count) {
            return api_error(500, '优惠券已领完');
        }
        
        // 创建优惠券
        $coupon = Coupon::create([
            'user_id' => $user->id,
            'template_id' => $templateId,
            'name' => $template->name,
            'discount_type' => $template->discount_type,
            'discount_price' => $template->discount_price,
            'discount_percent' => $template->discount_percent,
            'use_price' => $template->use_price,
            'valid_start_time' => $template->valid_start_time,
            'valid_end_time' => $template->valid_end_time,
            'status' => 0, // 未使用
        ]);
        
        // 更新领取数量
        $template->increment('take_count');
        
        return api_success($coupon, '领取成功');
    }

    /**
     * 浼樻儬鍒歌鎯?     */
    public function get(Request $request)
    {
        $id = $request->input('id');
        $coupon = Coupon::with('template')->find($id);

        if (!$coupon) {
            return api_error(404, '浼樻儬鍒镐笉瀛樺湪');
        }

        return api_success($coupon);
    }

    /**
     * 鏈娇鐢ㄤ紭鎯犲埜鏁伴噺
     */
    public function getUnusedCount()
    {
        $user = auth('sanctum')->user();

        $count = Coupon::where('user_id', $user->id)
            ->where('status', 0)
            ->count();

        return api_success(['count' => $count]);
    }
}