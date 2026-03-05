<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Suspend = 'suspend';
    case Activate = 'activate';
}
