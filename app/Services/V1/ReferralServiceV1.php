<?php

namespace App\Services\V1;

use App\Http\Resources\V1\ClientReferralProfilesResourceV1;
use App\Models\ReferralUser;
use Exception;

class ReferralServiceV1
{
    /**
     * @throws Exception
     */
    public function getAllReferralUsersForAuthUser(): array
    {
        $authId = auth()->id();

        $query = ReferralUser::query()
            ->select(['id', 'user_id', 'referrer_id']) // minimal columns
            ->where('referrer_id', $authId);

        return handleApiRequest(request(), $query, [ 'user:id,name'], ClientReferralProfilesResourceV1::class);
    }
}
