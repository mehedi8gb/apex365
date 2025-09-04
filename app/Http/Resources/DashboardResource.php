<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\AdminRankSetting;
use App\Models\Spinner;
use App\Models\Withdraw;
use App\Models\ReferralUser;

class DashboardResource extends JsonResource
{
    public static function collectionData($revalidate = false, $ttl = 300)
    {
        $cacheKey = 'dashboard_summary';

        if ($revalidate) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, $ttl, function () {
            return [
                'total_customers_count'    => User::role('customer')->count(),
                'total_commissions_count'  => Commission::count(),
                'total_transactions_count' => Transaction::count(),
                'total_ranks_count'        => AdminRankSetting::count(),
                'total_spinners'           => Spinner::count(),
                'total_paid_withdrawals'   => (float) Withdraw::where('status', 'paid')->sum('amount'),
                'total_unpaid_withdrawals' => (float) Withdraw::where('status', 'due')->sum('amount'),
                'total_referrals_count'    => ReferralUser::distinct('user_id')->count('user_id'),
                'last_updated_at'          => Carbon::now()->toDateTimeString(),
            ];
        });

        $lastUpdated = Carbon::parse($data['last_updated_at']);
        $nextUpdate  = $lastUpdated->copy()->addSeconds($ttl);

        $data['last_updated_at'] = getFormatedDate($lastUpdated);
        $data['next_update_at']  = getFormatedDate($nextUpdate);

        return $data;
    }

    public function toArray($request): array
    {
        // $this->resource can be used if you pass pre-fetched data
        return $this->resource;
    }
}
