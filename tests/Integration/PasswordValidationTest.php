<?php

namespace Tests\Integration;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\AuthRefreshDatabase;

class PasswordValidationTest extends TestCase
{
    /**
     * test Generate And Validate Password Flows
     */
    public function testGenerateAndValidatePasswordFlows()
    {
        //  1. Try STORE api without inputs
        $this
            ->json('POST', '/api/v1/passwords')
            ->assertStatus(400)
            ->assertJsonStructure(['errors' => ['validations' => ['user_id']]]);

        //  2. Try STORE api with valid inputs
        $password = $this
            ->json('POST', '/api/v1/passwords', ['user_id' => '10'])
            ->assertStatus(200)
            ->assertJsonStructure(['password'])
            ->decodeResponseJson('password');

        //  3. Try VALIDATE_PASSWORD api without inputs
        $this
            ->json('POST', '/api/v1/validate-password')
            ->assertStatus(400)
            ->assertJsonStructure(['errors' => ['validations' => ['user_id', 'password']]]);

        //  4. Try VALIDATE_PASSWORD api with invalid password
        $this
            ->json('POST', '/api/v1/validate-password', ['user_id'  => '10', 'password' => 'invalid_psw'])
            ->assertStatus(200)
            ->assertExactJson(['is_password_valid' => false]);

        //  5. Try VALIDATE_PASSWORD api with valid password
        $this
            ->json('POST', '/api/v1/validate-password', ['user_id'  => '10', 'password' => $password])
            ->assertStatus(200)
            ->assertExactJson(['is_password_valid' => true]);
    }
}
