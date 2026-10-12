<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\DataRt;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Livewire\Volt\Volt;
use Mockery;

class GoogleAuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    /**
     * Test SSO Login Page renders "Masuk dengan Google" button.
     */
    public function test_login_page_displays_google_sign_in_button(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk dengan Google');
        $response->assertSee(route('auth.google.redirect'));
    }

    /**
     * Test Google redirect warns if client credentials are not filled in .env.
     */
    public function test_google_redirect_warns_if_credentials_not_configured(): void
    {
        config(['services.google.client_id' => null]);
        config(['services.google.client_secret' => null]);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('warning');
    }

    /**
     * Test Google redirect redirects to Google OAuth endpoint when configured.
     */
    public function test_google_redirect_works_when_configured(): void
    {
        config(['services.google.client_id' => 'dummy-client-id']);
        config(['services.google.client_secret' => 'dummy-client-secret']);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.redirect'));
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    /**
     * Test existing citizen logs in automatically via Google email match.
     */
    public function test_google_callback_logs_in_existing_warga_by_email(): void
    {
        $warga = Warga::withoutTenant()->first();
        $warga->email = 'hendra.warga@gmail.com';
        $warga->save();

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-warga-123');
        $abstractUser->shouldReceive('getEmail')->andReturn('hendra.warga@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn($warga->Nama_Lengkap);
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar123');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/portal');
        $this->assertTrue(Auth::guard('warga')->check());
        $this->assertEquals($warga->NIK, Auth::guard('warga')->user()->NIK);

        $warga->refresh();
        $this->assertEquals('google-warga-123', $warga->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/a/avatar123', $warga->avatar);
    }

    /**
     * Test existing admin logs in automatically via Google email match.
     */
    public function test_google_callback_logs_in_existing_admin_by_email(): void
    {
        $admin = Admin::first();
        $admin->email = 'ketua.rt@gmail.com';
        $admin->save();

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-admin-999');
        $abstractUser->shouldReceive('getEmail')->andReturn('ketua.rt@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Ketua RT');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/admin-avatar');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($admin->Admin_ID, Auth::guard('web')->user()->Admin_ID);

        $admin->refresh();
        $this->assertEquals('google-admin-999', $admin->google_id);
    }

    /**
     * Test first-time Google user is redirected to account link view.
     */
    public function test_first_time_google_user_redirected_to_linking_flow(): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-new-777');
        $abstractUser->shouldReceive('getEmail')->andReturn('newuser@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Warga Baru Google');
        $abstractUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('register'));
        $this->assertFalse(Auth::guard('warga')->check());
        $this->assertFalse(Auth::guard('web')->check());

        // Check Link View renders Google email badge
        $linkViewResponse = $this->withSession(['google_auth_pending' => [
            'google_id' => 'google-new-777',
            'email' => 'newuser@gmail.com',
            'name' => 'Warga Baru Google',
            'avatar' => null,
        ]])->get(route('auth.google.link'));

        $linkViewResponse->assertStatus(200);
        $linkViewResponse->assertSee('newuser@gmail.com');
        $linkViewResponse->assertSee('Hubungkan Akun Google');
    }

    /**
     * Test successful one-time linking with citizen credentials.
     */
    public function test_link_google_account_with_citizen_credentials(): void
    {
        $warga = Warga::withoutTenant()->first();

        $response = $this->withSession([
            'google_auth_pending' => [
                'google_id' => 'google-linked-555',
                'email' => 'linked.warga@gmail.com',
                'name' => 'Hendra Link',
                'avatar' => null,
            ]
        ])->post(route('auth.google.link.post'), [
            'identity' => $warga->NIK,
            'password' => 'WargaHarmoni2026!',
        ]);

        $response->assertRedirect('/portal');
        $this->assertTrue(Auth::guard('warga')->check());

        $warga->refresh();
        $this->assertEquals('google-linked-555', $warga->google_id);
        $this->assertEquals('linked.warga@gmail.com', $warga->email);
    }

    /**
     * Test unlinking Google account works for citizen.
     */
    public function test_unlink_google_account_clears_google_id(): void
    {
        $warga = Warga::withoutTenant()->first();
        $warga->google_id = 'google-to-unlink';
        $warga->save();

        $this->actingAs($warga, 'warga');

        $response = $this->post(route('auth.google.unlink'));
        $response->assertStatus(302);

        $warga->refresh();
        $this->assertNull($warga->google_id);
    }

    /**
     * Test citizen completes registration directly with Google auth pending.
     */
    public function test_citizen_registers_via_google_flow(): void
    {
        $rt = DataRt::first();
        $newNik = '3201059988771122';
        $newKk = '3201059988773344';

        $this->withSession([
            'google_auth_pending' => [
                'google_id' => 'google-reg-12345',
                'email' => 'warga.baru.google@example.com',
                'name' => 'Budi Santoso Google',
                'avatar' => 'https://example.com/avatar.jpg',
            ]
        ]);

        Volt::test('auth.register-warga')
            ->assertSet('nama_lengkap', 'Budi Santoso Google')
            ->set('id_rt', $rt->ID_RT)
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('id_kk', $newKk)
            ->set('nik', $newNik)
            ->set('tgl_lahir', '1992-04-15')
            ->set('no_telp', '0812-3456-7890')
            ->set('alamat', 'Jl. Harmoni Raya Blok B No. 12')
            ->call('registerWithGoogle')
            ->assertHasNoErrors()
            ->assertRedirect('/portal');

        $this->assertTrue(Auth::guard('warga')->check());

        $warga = Warga::withoutTenant()->where('NIK', $newNik)->first();
        $this->assertNotNull($warga);
        $this->assertEquals('google-reg-12345', $warga->google_id);
        $this->assertEquals('warga.baru.google@example.com', $warga->email);
        $this->assertEquals('https://example.com/avatar.jpg', $warga->avatar);
        $this->assertEquals($rt->ID_RT, $warga->ID_RT);

        $this->assertDatabaseHas('APPROVAL', [
            'tabel_sumber' => 'WARGA',
            'id_referensi' => $newNik,
            'status' => 'pending',
        ]);
    }
}

