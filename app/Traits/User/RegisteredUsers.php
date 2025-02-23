<?php

namespace App\Traits\User;

use App\Models\Admin\Currency;
use App\Models\UserWallet;
use Exception;
use Illuminate\Support\Facades\Log;

trait RegisteredUsers
{
    protected function createUserWallets($user)
    {
        try {
            $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
            $wallets = [];
            foreach ($currencies as $currency_id) {
                $wallets[] = [
                    'user_id'       => $user->id,
                    'currency_id'   => $currency_id,
                    'balance'       => 0,
                    'status'        => true,
                    'created_at'    => now(),
                ];
            }
            try {
                UserWallet::insert($wallets);
            } catch (Exception $e) {
                // handle error
                $this->guard()->logout();
                $user->delete();
                return $this->breakAuthentication("Failed to create wallet! Please try again");
            }
        } catch (\Exception $e) {
            Log::error('Wallet creation error:', ['error' => $e->getMessage()]);
            throw $e; // Re-throw the exception to stop the process
        }
    }


    protected function breakAuthentication($error)
    {
        return back()->with(['error' => [$error]]);
    }
}
