<?php
/**
 * Created by PhpStorm.
 * User: Daniel Boldan
 * Date: 2020-06-12
 * Time: 21:35
 */

namespace App\Services\Passwords;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class ValidatePassword
{
    /**
     * @var array
     */
    private $inputs;

    /**
     * StorePassword constructor.
     *
     * @param array $inputs
     */
    public function __construct(array $inputs)
    {
        $this->inputs = [
            'user_id'  => "user_id:{$inputs['user_id']}",
            'password' => $inputs['password'],
        ];
    }

    /**
     * Generate password
     *
     * @return array
     */
    public function call(): array
    {
        return Cache::has($this->inputs['user_id']) ? $this->response() : $this->invalidResponse();
    }

    /**
     * Old password - cached
     *
     * @return array
     */
    private function response(): array
    {
        try {
            $password = Crypt::decryptString(Cache::get($this->inputs['user_id']));
        } catch (DecryptException $e) {
            return $this->invalidResponse();
        }

        return $password === $this->inputs['password'] ? $this->validResponse() : $this->invalidResponse();
    }

    /**
     * Valid response
     *
     * @return array
     */
    private function validResponse(): array
    {
        return ['is_password_valid' => true];
    }

    /**
     * Invalid response
     *
     * @return array
     */
    private function invalidResponse(): array
    {
        return ['is_password_valid' => false];
    }
}
