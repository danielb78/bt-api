<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Tests\Traits\AuthRefreshDatabase;

class PasswordsControllerTest extends TestCase
{
    /**
     * Test Store With Invalid Inputs
     *
     * @return void
     */
    public function testStoreWithInvalidInputs()
    {
        $response = $this->post('api/v1/passwords', ['user_ids' => '10']);

        $response->assertStatus(400);
    }

    /**
     * Test Store With Valid Inputs
     *
     * @return void
     */
    public function testStoreWithValidInputs()
    {
        $response = $this->post('api/v1/passwords', ['user_id' => '10']);

        $response->assertStatus(200);
    }

    /**
     * Test Validate Password With Invalid Inputs
     *
     * @return void
     */
    public function testValidatePasswordWithInvalidInputs()
    {
        $response = $this->post('api/v1/validate-password', ['user_ids' => '10']);

        $response->assertStatus(400);
    }

    /**
     * Test Validate Password With Valid Inputs
     *
     * @return void
     */
    public function testValidatePasswordWithValidInputs()
    {
        $response = $this->post('api/v1/validate-password', ['user_id' => '10', 'password' => 'fake_psw']);

        $response->assertStatus(200);
    }
}
