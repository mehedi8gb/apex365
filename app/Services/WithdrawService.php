<?php
namespace App\Services;

use App\Enums\WithdrawStatus;
use App\Models\Account;
use App\Models\Withdraw;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Throwable;

class WithdrawService
{
    protected LoggerInterface $logger;
    protected int $minWithdraw;      // in smallest unit (e.g., poisha)
    protected int $minRemaining;     // minimum balance to keep after withdraw

    public function __construct(LoggerInterface $logger, int $minWithdraw = 5000, int $minRemaining = 1000)
    {
        // minWithdraw = 5000 poisha => 50.00 taka
        // minRemaining = 1000 poisha => 10.00 taka
        $this->logger = $logger;
        $this->minWithdraw = $minWithdraw;
        $this->minRemaining = $minRemaining;
    }

    /**
     * Create a withdrawal request in a transaction with row locking / atomic decrement.
     *
     * @param int $userId
     * @param int $amountSmallestUnit  Amount in smallest unit (integer), e.g. poisha
     * @param string $paymentMethod
     * @param string|null $mobileNumber
     *
     * @return Withdraw
     * @throws Exception|Throwable
     */
    public function createWithdraw(int $userId, int $amountSmallestUnit, string $paymentMethod, ?string $mobileNumber): Withdraw
    {
        if ($amountSmallestUnit < $this->minWithdraw) {
            throw ValidationException::withMessages(['amount' => 'Minimum withdrawal amount is ' . ($this->minWithdraw / 100)]);
        }

        return DB::transaction(function () use ($userId, $amountSmallestUnit, $paymentMethod, $mobileNumber) {

            // Lock the row for update to prevent concurrent modifications
            $account = Account::where('user_id', $userId)->lockForUpdate()->first();

            if (!$account) {
                throw ValidationException::withMessages(['account' => 'Account not found']);
            }

            // Convert DB stored balance to integer the smallest unit if needed.
            // Assuming $account->balance_smallest_unit exists OR you convert decimal * 100
            $balance = (int) $account->balance * 100; // e.g., convert 100.50 to 10050 poisha

            // Ensure account has enough balance and leaves minRemaining after withdraw
            if ($balance < $amountSmallestUnit) {
                throw ValidationException::withMessages(['amount' => 'Insufficient balance']);
            }

            if (($balance - $amountSmallestUnit) < $this->minRemaining) {
                throw ValidationException::withMessages(['amount' => 'Account balance must be at least ' . ($this->minRemaining / 100) . ' after withdraw']);
            }

            // Decrement the balance atomically (we already locked the row, but do a safe update)
            $account->balance = ($balance - $amountSmallestUnit) / 100; // convert back to decimal for storage
            $account->save();

            // Create the withdrawal entry
            $withdraw = Withdraw::create([
                'user_id' => $userId,
                'amount' => $amountSmallestUnit / 100, // store as decimal in DB
                'payment_method' => $paymentMethod,
                'mobile_number' => $mobileNumber,
                'status' => WithdrawStatus::Pending->value,
            ]);

            // Audit log
            $this->logger->info('withdraw.created', [
                'user_id' => $userId,
                'withdraw_id' => $withdraw->id,
                'amount' => $amountSmallestUnit / 100,
                'payment_method' => $paymentMethod,
                'mobile_number' => $mobileNumber,
                'balance_before' => $balance,
                'balance_after' => $account->balance,
            ]);

            return $withdraw;
        });
    }
}
