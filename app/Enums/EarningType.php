<?php
// app/Enums/EarningType.php
namespace App\Enums;

enum EarningType: string
{
    case ReferralEarnings = 'Referral Earnings';
    case Bonus = 'Bonus';
    case Purchase = 'Purchase';
    case Adjustment = 'Adjustment';

    // Add more types as needed
}
