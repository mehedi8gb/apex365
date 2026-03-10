<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Validation\ValidationException;

class TransactionValidatorService
{
    /**
     * Validate a transaction for commission application.
     * Throws HttpException on failure (caught by Laravel's handler → JSON response).
     */
    public function validateForCommission(string $transactionId): Transaction
    {
        $transaction = Transaction::where('transactionId', $transactionId)->firstOrFail();

        if ($transaction->status === TransactionStatus::Suspend) {
            throw ValidationException::withMessages([
                'transactionId' => 'This transaction is suspended. Commissions cannot be applied.',
            ]);
        }

        if ($transaction->status !== TransactionStatus::Activate) {
            throw ValidationException::withMessages([
                'transactionId' => 'Transaction is not active.',
            ]);
        }

        if (isset($transaction->userId)) {
            throw ValidationException::withMessages([
                'transactionId' => 'Transaction ID is already in use.',
            ]);
        }

        return $transaction;
    }
}
