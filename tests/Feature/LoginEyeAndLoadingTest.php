<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginEyeAndLoadingTest extends TestCase
{
    public function test_toggle_show_password_in_volt()
    {
        $component = Volt::test('auth.login-sso')
            ->set('password', 'SuperSecret123!')
            ->assertSet('showPassword', false)
            ->call('toggleShowPassword')
            ->assertSet('showPassword', true);
            
        $this->assertTrue(true);
    }
}
