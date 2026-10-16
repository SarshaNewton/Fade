<?php

namespace App\Services\Mail\Security;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EmailEncryptionService{

    public function encrypt(?string $text): ?string{
        return filled($text) ? Crypt::encryptString($text) : $text;
    }

    public function decrypt(?string $text): ?string{
        
        if(blank($text)){
            return $text;
        }
        
        try{
            return Crypt::decryptString($text);
        } catch (DecryptException $e) {
            return $text;
        }
    }
}