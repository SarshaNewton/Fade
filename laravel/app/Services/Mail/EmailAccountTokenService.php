<?php

namespace App\Services\Mail;

use App\Models\EmailAccount;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EmailAccountTokenService{
    public function ensureValidAccessToken(EmailAccount $account): EmailAccount{
        if ($account->expires_at && now()->addMinutes(5)->lt($account->expires_at)) {
            return $account;
        }

        return $this->refreshAccessToken($account);
    }
    
    public function refreshAccessToken(EmailAccount $account): EmailAccount{
        
        if (empty($account->refresh_token)) {
            throw new \Exception("Cannot refresh {$account->provider} access token because no refresh token is stored.");
        }

        $refreshToken = $this->decrypt($account->refresh_token);

        if($account->provider == 'google'){
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' =>config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token'
            ]);
        } elseif ($account->provider === 'microsoft') {
            throw new \Exception('Microsoft token refresh is not implemented yet.');
        } else {
            throw new \Exception("Unsupported provider: {$account->provider}");
        }

        if(!$response->successful()){
            throw new \Exception("Failed to refresh {$account->provider} access token");
        }

        $data = $response->json();

        if (!isset($data['access_token'], $data['expires_in'])) {
            throw new \Exception("Invalid {$account->provider} token refresh response.");
        }

        $account->update([
            'access_token' => Crypt::encryptString($data['access_token']),
            'expires_at' => now()->addSeconds($data['expires_in'])
        ]);

        return $account->fresh();
    
    }  

    public function getAccessToken(EmailAccount $account): string{
        $account = $this->ensureValidAccessToken($account);
        return $this->decrypt($account->access_token);
    }

    public function encrypt(?string $token): string{
        // Filled ensures that 0 is not treated as false and ' ' is not treated as true
        return filled($token) ? Crypt::encryptString($token) : $token;
    }   

    private function decrypt(string $token): string{
        try{
            return Crypt::decryptString($token);
        } catch (DecryptException $e){
            return throw new \Exception("Token decryption failed.");
        }
    }

}