<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DataRt;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SuperAdminRtAccountManagementTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
        $this->superAdmin = Admin::where('role', 'super_admin')->first();
    }

    /**
     * Test 1: Super Admin can register any RT from anywhere and manage its data.
     */
    public function test_superadmin_can_create_new_rt_anywhere(): void
    {
        $this->actingAs($this->superAdmin, 'web');

        $newRt = DataRt::create([
            'nomor_rt' => '12',
            'nomor_rw' => '07',
            'kelurahan' => 'Gondangdia',
            'kecamatan' => 'Menteng',
            'kota' => 'Jakarta Pusat',
            'nama_ketua_rt' => 'Bpk. H. Suryo Prabowo',
            'no_telp_ketua_rt' => '081299998888',
        ]);

        $this->assertDatabaseHas('DATA_RT', [
            'ID_RT' => $newRt->ID_RT,
            'nomor_rt' => '12',
            'nomor_rw' => '07',
            'kelurahan' => 'Gondangdia',
        ]);
    }

    /**
     * Test 2: Super Admin creates RT account using Username and Password.
     * The RT Chairman can then authenticate successfully using either Username or Email.
     */
    public function test_superadmin_creates_rt_account_with_username_and_password(): void
    {
        $newRt = DataRt::create([
            'nomor_rt' => '09',
            'nomor_rw' => '03',
            'kelurahan' => 'Cilandak Barat',
            'kecamatan' => 'Cilandak',
            'kota' => 'Jakarta Selatan',
            'nama_ketua_rt' => 'Bpk. Farhan Malik',
        ]);

        $ketuaRtAccount = Admin::create([
            'Username' => 'ketua_rt09',
            'email' => 'ketua.rt09@gmail.com',
            'Password_hash' => Hash::make('SandiKetua09!'),
            'role' => 'admin_rt',
            'ID_RT' => $newRt->ID_RT,
        ]);

        // A. Login using Username
        $responseUsername = $this->post('/login', [
            'identity' => 'ketua_rt09',
            'password' => 'SandiKetua09!',
        ]);
        $responseUsername->assertRedirect('/cms');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($ketuaRtAccount->Admin_ID, Auth::guard('web')->user()->Admin_ID);
        $this->assertEquals($newRt->ID_RT, Auth::guard('web')->user()->ID_RT);

        Auth::guard('web')->logout();

        // B. Login using Email directly in the login form
        $responseEmail = $this->post('/login', [
            'identity' => 'ketua.rt09@gmail.com',
            'password' => 'SandiKetua09!',
        ]);
        $responseEmail->assertRedirect('/cms');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($ketuaRtAccount->Admin_ID, Auth::guard('web')->user()->Admin_ID);
    }

    /**
     * Test 3: Super Admin creates RT account with Google Email.
     * The RT Chairman can log in with 1-click Google OAuth without needing a manual password.
     */
    public function test_superadmin_creates_rt_account_with_google_email_and_officer_logs_in_via_google(): void
    {
        $newRt = DataRt::create([
            'nomor_rt' => '03',
            'nomor_rw' => '02',
            'kelurahan' => 'Senen',
            'kecamatan' => 'Senen',
            'kota' => 'Jakarta Pusat',
            'nama_ketua_rt' => 'Ibu Ratna Dewi',
        ]);

        $ketuaRtAccount = Admin::create([
            'Username' => 'ketua_rt03_senen',
            'email' => 'ratna.ketuart03@gmail.com',
            'Password_hash' => Hash::make('google_sso_random_fallback'),
            'role' => 'admin_rt',
            'ID_RT' => $newRt->ID_RT,
        ]);

        // Mock Google OAuth Socialite User
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')->andReturn('google-id-ratna-987');
        $abstractUser->shouldReceive('getEmail')->andReturn('ratna.ketuart03@gmail.com');
        $abstractUser->shouldReceive('getName')->andReturn('Ratna Dewi');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/ratna.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // RT Chairman hits Google Callback
        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($ketuaRtAccount->Admin_ID, Auth::guard('web')->user()->Admin_ID);
        $this->assertEquals('admin_rt', session('active_role'));

        $ketuaRtAccount->refresh();
        $this->assertEquals('google-id-ratna-987', $ketuaRtAccount->google_id);
        $this->assertEquals('https://lh3.googleusercontent.com/avatar/ratna.jpg', $ketuaRtAccount->avatar);
    }

    /**
     * Test 4: Relationship helper ketuaRtAdmin retrieves the proper admin_rt.
     */
    public function test_datart_model_has_working_ketua_rt_admin_relationship(): void
    {
        $rt = DataRt::create([
            'nomor_rt' => '11',
            'nomor_rw' => '04',
            'kelurahan' => 'Menteng',
            'kecamatan' => 'Menteng',
            'kota' => 'Jakarta Pusat',
            'nama_ketua_rt' => 'Bpk. Ahmad Syarif',
        ]);

        $this->assertNull($rt->ketuaRtAdmin);

        $admin = Admin::create([
            'Username' => 'ketua_rt11',
            'email' => 'ketua.rt11@gmail.com',
            'Password_hash' => Hash::make('Sandi123!'),
            'role' => 'admin_rt',
            'ID_RT' => $rt->ID_RT,
        ]);

        $rt->refresh();
        $this->assertNotNull($rt->ketuaRtAdmin);
        $this->assertEquals($admin->Admin_ID, $rt->ketuaRtAdmin->Admin_ID);
        $this->assertEquals('ketua_rt11', $rt->ketuaRtAdmin->Username);
    }
}
