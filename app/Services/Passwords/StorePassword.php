<?php
/**
 * Created by PhpStorm.
 * User: Daniel Boldan
 * Date: 2020-06-12
 * Time: 20:45
 */

namespace App\Services\Passwords;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class StorePassword
{
    /**
     * @var string
     */
    private $userId;

    /**
     * StorePassword constructor.
     *
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = "user_id:{$userId}";
    }

    /**
     * Generate password
     *
     * @return array
     */
    public function call(): array
    {
        return Cache::has($this->userId) ? $this->oldPassword() : $this->newPassword();
    }

    /**
     * Old password - cached
     *
     * @return array
     */
    private function oldPassword(): array
    {
        return ['password' => Crypt::decryptString(Cache::get($this->userId))];
    }

    /**
     * New password
     *
     * @return array
     */
    private function newPassword(): array
    {
        $psw = $random = Str::random(8);

        Cache::add($this->userId, Crypt::encryptString($psw), 120);

        return ['password' => $psw];
    }
}
