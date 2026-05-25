<?php

namespace App\Http\Controllers\Api\Pay;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\BalanceLog;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    use ApiResponse;

    /**
     * 钱包流水分页查询
     */
    public function page(Request $request)
    {
        $request->validate([
            'pageNo'   => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:100',
        ]);

        $userId   = $request->user()->id;
        $pageNo   = (int) $request->input('pageNo', 1);
        $pageSize = (int) $request->input('pageSize', 15);

        $query = BalanceLog::where('user_id', $userId)
            ->orderByDesc('created_at');

        // 时间范围过滤
        if ($request->filled('createTime')) {
            $createTime = $request->input('createTime');
            if (is_array($createTime) && count($createTime) >= 2) {
                $query->whereBetween('created_at', [$createTime[0], $createTime[1]]);
            }
        }

        $total = $query->count();
        $list  = $query->skip(($pageNo - 1) * $pageSize)->take($pageSize)->get();

        return $this->success([
            'list'     => $list,
            'total'    => $total,
            'pageNo'   => $pageNo,
            'pageSize' => $pageSize,
        ]);
    }

    /**
     * 钱包流水统计
     */
    public function getSummary(Request $request)
    {
        $userId = $request->user()->id;

        $query = BalanceLog::where('user_id', $userId);

        if ($request->filled('createTime')) {
            $createTime = $request->input('createTime');
            if (is_array($createTime) && count($createTime) >= 2) {
                $query->whereBetween('created_at', [$createTime[0], $createTime[1]]);
            }
        }

        $data = [
            'total_income'  => (float) $query->clone()->where('type', 'income')->sum('amount'),
            'total_expense' => (float) $query->clone()->where('type', 'expense')->sum('amount'),
            'count'         => $query->count(),
        ];

        return $this->success($data);
    }
}