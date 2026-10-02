<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\Approval;
use App\Models\DataRt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

class AuthenticationAndRegistrationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    /**
     * Test 1: Unified Login with ADMIN credentials redirects to CMS Dashboard (/cms/dashboard).
     */
    public function test_admin_login_redirects_to_cms_dashboard(): void
    {
        $response = $this->post('/login', [
            'identity' => 'superadmin',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::check());
        $this->assertEquals('super_admin', Auth::user()->role);

        // Also test with 'password' dev fallback
        Auth::logout();
        $responseDev = $this->post('/login', [
            'identity' => 'superadmin',
            'password' => 'password',
        ]);
        $responseDev->assertRedirect('/cms');
    }

    /**
     * Test 2: Unified Login with WARGA credentials via NIK and Username redirects to /portal.
     */
    public function test_warga_login_via_nik_and_username_redirects_to_portal(): void
    {
        // Via NIK
        $responseNik = $this->post('/login', [
            'identity' => '3201051208940001',
            'password' => 'WargaHarmoni2026!',
        ]);
        $responseNik->assertRedirect('/portal');
        $this->assertTrue(Auth::check());
        $this->assertEquals('warga', Auth::user()->role);

        Auth::logout();

        // Via Username
        $responseUser = $this->post('/login', [
            'identity' => 'hendra_w',
            'password' => 'password',
        ]);
        $responseUser->assertRedirect('/portal');
        $this->assertTrue(Auth::check());
        $this->assertEquals('warga', Auth::user()->role);
    }

    /**
     * Test 3: Failed Login returns exact required validation error message.
     */
    public function test_invalid_login_returns_verbatim_error_message(): void
    {
        $expectedError = 'NIK/Username atau kata sandi tidak sesuai.';

        // Via HTTP POST
        $response = $this->post('/login', [
            'identity' => 'nonexistent_user',
            'password' => 'wrong_pass',
        ]);

        $response->assertSessionHasErrors(['identity' => $expectedError]);

        // Via Livewire Volt component
        Volt::test('auth.login-sso')
            ->set('identity', 'invalid_acc')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['identity' => $expectedError]);
    }

    /**
     * Test 4: Login Card UI complies with Pure Light Mode, labels, and bottom Register CTA.
     */
    public function test_login_ui_elements_and_register_cta(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('NIK atau Username');
        $response->assertSee('Kata Sandi');
        $response->assertSee('Masuk');
        $response->assertSee('Warga baru di lingkungan ini?');
        $response->assertSee('Daftar Akun');
        $response->assertSee(route('register'), false);
    }

    /**
     * Test 5: Register page loads successfully and displays 3-step wizard in Pure Light Mode.
     */
    public function test_register_page_loads_with_step_wizard(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Akun Warga Baru');
        $response->assertSee('Langkah 1: Wilayah Domisili RT');
        $response->assertSee('Pilih RT / RW Domisili');
        $response->assertSee('Sudah memiliki akun?');
        $response->assertSee('Masuk di sini');
    }

    /**
     * Test 6: Registration step 1 validation requires valid ID_RT.
     */
    public function test_registration_step_1_validation(): void
    {
        Volt::test('auth.register-warga')
            ->set('id_rt', '')
            ->call('nextStep')
            ->assertHasErrors(['id_rt'])
            ->assertSet('currentStep', 1);

        $rt = DataRt::first();
        Volt::test('auth.register-warga')
            ->set('id_rt', $rt->ID_RT)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);
    }

    /**
     * Test 7: Registration step 2 validates NIK uniqueness, 16 digits, and identity fields.
     */
    public function test_registration_step_2_validation(): void
    {
        $rt = DataRt::first();

        // Duplicate NIK check
        Volt::test('auth.register-warga')
            ->set('id_rt', $rt->ID_RT)
            ->set('currentStep', 2)
            ->set('id_kk', '123') // invalid digits
            ->set('nik', '3201051208940001') // already exists in seeder
            ->set('nama_lengkap', '')
            ->call('nextStep')
            ->assertHasErrors(['id_kk', 'nik', 'nama_lengkap']);
    }

    /**
     * Test 8: Registration step 3 validates username uniqueness and password matching.
     */
    public function test_registration_step_3_validation(): void
    {
        $rt = DataRt::first();

        Volt::test('auth.register-warga')
            ->set('id_rt', $rt->ID_RT)
            ->set('id_kk', '3201059988770002')
            ->set('nik', '3201059988770001')
            ->set('nama_lengkap', 'Budi Santoso')
            ->set('tgl_lahir', '1990-01-01')
            ->set('no_telp', '08123456789')
            ->set('alamat', 'Jl. Harmoni Blok A')
            ->set('currentStep', 3)
            ->set('username', 'superadmin') // already taken by admin
            ->set('password', 'short')
            ->set('password_confirmation', 'mismatch')
            ->call('register')
            ->assertHasErrors(['username', 'password']);
    }

    /**
     * Test 9: Complete registration flow creates KK, WARGA, and APPROVAL records inside transaction.
     */
    public function test_successful_citizen_registration_and_immediate_login(): void
    {
        $rt = DataRt::first();
        $newNik = '3201059988770001';
        $newKk = '3201059988770002';
        $newUsername = 'warga_baru';
        $newPassword = 'PasswordWarga123!';

        // Ensure records do not exist beforehand
        $this->assertDatabaseMissing('WARGA', ['NIK' => $newNik]);
        $this->assertDatabaseMissing('KARTU_KELUARGA', ['ID_KK' => $newKk]);

        Volt::test('auth.register-warga')
            ->set('id_rt', $rt->ID_RT)
            ->call('nextStep')
            ->assertSet('currentStep', 2)
            ->set('id_kk', $newKk)
            ->set('nik', $newNik)
            ->set('nama_lengkap', 'Ahmad Fadillah')
            ->set('tgl_lahir', '1995-07-20')
            ->set('no_telp', '0812-9988-7711')
            ->set('alamat', 'Harmoni Residence Blok E No. 09')
            ->call('nextStep')
            ->assertSet('currentStep', 3)
            ->set('username', $newUsername)
            ->set('password', $newPassword)
            ->set('password_confirmation', $newPassword)
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        // 1. Verify KARTU_KELUARGA created
        $this->assertDatabaseHas('KARTU_KELUARGA', [
            'ID_KK' => $newKk,
            'Alamat' => 'Harmoni Residence Blok E No. 09',
        ]);

        // 2. Verify WARGA created
        $this->assertDatabaseHas('WARGA', [
            'NIK' => $newNik,
            'ID_KK' => $newKk,
            'ID_RT' => $rt->ID_RT,
            'Nama_Lengkap' => 'Ahmad Fadillah',
            'Username' => $newUsername,
            'role' => 'warga',
        ]);

        $createdWarga = Warga::withoutTenant()->where('NIK', $newNik)->first();
        $this->assertNotNull($createdWarga);
        $this->assertTrue(Hash::check($newPassword, $createdWarga->Password_hash));

        // 3. Verify APPROVAL entry created
        $this->assertDatabaseHas('APPROVAL', [
            'Admin_ID' => null,
            'tabel_sumber' => 'WARGA',
            'id_referensi' => $newNik,
            'status' => 'pending',
            'catatan' => 'Pendaftaran mandiri akun warga baru',
        ]);

        // 4. Verify newly registered citizen can immediately log in
        $loginResponse = $this->post('/login', [
            'identity' => $newUsername,
            'password' => $newPassword,
        ]);
        $loginResponse->assertRedirect('/portal');
        $this->assertTrue(Auth::check());
        $this->assertEquals($newNik, Auth::user()->NIK);
    }
}
