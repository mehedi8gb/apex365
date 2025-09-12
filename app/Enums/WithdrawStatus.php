<?php

namespace App\Enums;

enum WithdrawStatus: string
{
    case Approved = 'approved';
    case Suspended = 'suspended';
    case Pending = 'pending';
}
