<?php

namespace App\Http\Resources;

use App\Models\AdminRankSetting;
use App\Models\Commission;
use App\Models\ReferralUser;
use App\Models\Spinner;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class DashboardResource extends JsonResource
{
    public static function collectionData($revalidate = false, $ttl = 600)
    {
        $cacheKey = 'dashboard_summary';

        if ($revalidate) {
            Cache::forget($cacheKey);
        }

        $data = Cache::remember($cacheKey, $ttl, function () {
            return [
                'total_customers_count' => User::role('customer')->count(),
                'total_commissions' => 'Tk '.number_format((float) Commission::sum('amount'), 2, '.', ''),
                'total_transaction_ids_count' => Transaction::count(),
                'total_ranks_count' => AdminRankSetting::count(),
                'total_spinners' => Spinner::count(),
                'total_paid_withdrawals' => 'Tk '.number_format((float) Withdraw::where('status', 'paid')->sum('amount'), 2),
                'total_unpaid_withdrawals' => 'Tk '.number_format((float) Withdraw::where('status', 'due')->sum('amount'), 2),
                'total_referrals_count' => ReferralUser::distinct('user_id')->count('user_id'),
                'last_updated_at' => Carbon::now()->toDateTimeString(),
            ];
        });

        $lastUpdated = Carbon::parse($data['last_updated_at']);
        $nextUpdate = $lastUpdated->copy()->addSeconds($ttl);

        $data['last_updated_at'] = getFormatedDate($lastUpdated);
        $data['next_update_at'] = getFormatedDate($nextUpdate);

        return $data;
    }

    public function toArray($request): array
    {
        // $this->resource can be used if you pass pre-fetched data
        return $this->resource;
    }
}
