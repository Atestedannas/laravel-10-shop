<?php

namespace App\Http\Controllers\Api\Member;


use App\Http\Controllers\Controller;
use App\Models\MemberPointRecord;
use App\Models\MemberSignInConfig;
use App\Models\MemberSignInRecord;
use App\Models\User;
use Illuminate\Http\Request;

class SignInController extends Controller
{

    /**
     * 缁涙儳鍩岀憴鍕灟閸掓銆?     */
    public function configList()
    {
        $configs = MemberSignInConfig::orderBy('day')->get();

        return api_success($configs);
    }

    /**
     * 娑擃亙姹夌粵鎯у煂缂佺喕顓?     */
    public function getSummary(Request $request)
    {
        $userId = $request->user()->id;

        $totalDays    = MemberSignInRecord::where('user_id', $userId)->count();
        $totalPoints  = MemberSignInRecord::where('user_id', $userId)->sum('point');
        $todaySigned  = MemberSignInRecord::where('user_id', $userId)
            ->whereDate('sign_date', today())
            ->exists();

        // 鐠侊紕鐣绘潻鐐电敾缁涙儳鍩屾径鈺傛殶
        $continuousDays = 0;
        $records = MemberSignInRecord::where('user_id', $userId)
            ->orderByDesc('sign_date')
            ->get();

        $expectedDate = today();
        foreach ($records as $record) {
            if ($record->sign_date->format('Y-m-d') === $expectedDate->format('Y-m-d')) {
                $continuousDays++;
                $expectedDate = $expectedDate->subDay();
            } else {
                break;
            }
        }

        return api_success([
            'totalDays'      => $totalDays,
            'continuousDays' => $continuousDays,
            'totalPoints'    => $totalPoints,
            'todaySigned'    => $todaySigned,
        ]);
    }

    /**
     * 閹笛嗩攽缁涙儳鍩?     */
    public function create(Request $request)
    {
        $userId = $request->user()->id;

        // 濡偓閺屻儰绮栨径鈺傛Ц閸氾箑鍑＄粵鎯у煂
        $alreadySigned = MemberSignInRecord::where('user_id', $userId)
            ->whereDate('sign_date', today())
            ->exists();

        if ($alreadySigned) {
            return api_error(500, '娴犲﹥妫╁鑼劮閸?);
        }

        // 鐠侊紕鐣绘潻鐐电敾缁涙儳鍩屾径鈺傛殶
        $yesterdayRecord = MemberSignInRecord::where('user_id', $userId)
            ->whereDate('sign_date', today()->subDay())
            ->first();

        $continuousDay = $yesterdayRecord ? $yesterdayRecord->day + 1 : 1;

        // 閼惧嘲褰囩€电懓绨叉径鈺傛殶閻ㄥ嫮袧閸掑棗顨涢崝?        $config = MemberSignInConfig::where('day', $continuousDay)->first();
        $point  = $config ? $config->point : 1;

        // 閸愭瑥鍙嗙粵鎯у煂鐠佹澘缍?        $record = MemberSignInRecord::create([
            'user_id'   => $userId,
            'sign_date' => today(),
            'point'     => $point,
            'day'       => $continuousDay,
        ]);

        // 婢х偛濮為悽銊﹀煕缁夘垰鍨?        $user = User::find($userId);
        $user->increment('point', $point);

        // 閸愭瑥鍙嗙粔顖氬瀻鐠佹澘缍?        MemberPointRecord::create([
            'user_id'     => $userId,
            'title'       => '缁涙儳鍩屾總鏍уС閿涘牏顑? . $continuousDay . '婢垛晪绱?,
            'point'       => $point,
            'total_point' => $user->point,
            'add_status'  => 1,
        ]);

        return api_success([
            'day'   => $continuousDay,
            'point' => $point,
            'total' => $user->point,
        ], '缁涙儳鍩岄幋鎰');
    }

    /**
     * 缁涙儳鍩岀拋鏉跨秿閸掑棝銆?     */
    public function recordPage(Request $request)
    {
        $pageNo   = (int) $request->get('pageNo', 1);
        $pageSize = (int) $request->get('pageSize', 10);

        $query = MemberSignInRecord::where('user_id', $request->user()->id)
            ->orderByDesc('sign_date');

        $paginator = $query->paginate($pageSize, ['*'], 'page', $pageNo);

        return api_success([
            'total' => $paginator->total(),
            'list'  => $paginator->items(),
        ]);
    }
}