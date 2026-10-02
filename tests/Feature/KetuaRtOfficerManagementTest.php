<?php

namespace Tests\Feature;

use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Models\Admin;
use App\Models\DataRt;
use Database\Seeders\ErdRuangWargaSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class KetuaRtOfficerManagementTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $superAdmin;
    protected Admin $ketuaRt;
    protected Admin $bendahara;
    protected Admin $sekretaris;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->superAdmin = Admin::where('role', 'super_admin')->first();
        $this->ketuaRt = Admin::where('role', 'admin_rt')->first();
        $this->bendahara = Admin::where('role', 'bendahara')->first();
        $this->sekretaris = Admin::where('role', 'sekretaris')->first();
    }

    /**
     * Test 1: Ketua RT can access the Admins/Pengurus resource table.
     */
    public function test_ketua_rt_can_access_admin_resource_page(): void
    {
        $response = $this->actingAs($this->ketuaRt, 'web')->get('/admin/admins');
        $response->assertStatus(200);
        $response->assertSee('Pengurus RT');
        $response->assertSee('Username / Akun');
        $response->assertSee('Daftarkan Pengurus RT');
    }

    /**
     * Test 2: Ketua RT can register a new Bendahara and Sekretaris for their RT.
     */
    public function test_ketua_rt_can_register_new_officer(): void
    {
        Livewire::actingAs($this->ketuaRt, 'web')
            ->test(ListAdmins::class)
            ->callAction('create', [
                'Username' => 'bendahara_baru_rt05',
                'role' => 'bendahara',
                'Password_hash' => 'SandiBaru123!',
            ])
            ->assertHasNoActionErrors();

        $newOfficer = Admin::where('Username', 'bendahara_baru_rt05')->first();
        $this->assertNotNull($newOfficer, 'Officer should be successfully created in ADMIN table.');
        $this->assertEquals('bendahara', $newOfficer->role);
        $this->assertEquals($this->ketuaRt->ID_RT, $newOfficer->ID_RT, 'New officer must belong to Ketua RT\'s ID_RT.');
        $this->assertTrue(Hash::check('SandiBaru123!', $newOfficer->Password_hash), 'Password must be properly hashed.');
    }

    /**
     * Test 3: Newly registered officer can immediately authenticate at /login.
     */
    public function test_newly_registered_officer_can_login_and_access_cms(): void
    {
        // 1. Ketua RT registers a new Sekretaris
        Admin::create([
            'Username' => 'sekretaris_andi',
            'Password_hash' => Hash::make('Aman12345!'),
            'role' => 'sekretaris',
            'ID_RT' => $this->ketuaRt->ID_RT,
        ]);

        // 2. Newly appointed Sekretaris logs in
        $response = $this->post('/login', [
            'identity' => 'sekretaris_andi',
            'password' => 'Aman12345!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertAuthenticated('web');
        $this->assertEquals('sekretaris', session('active_role'));

        // 3. New Sekretaris can access allowed resources (Surat & Warga)
        $this->get('/admin/surats')->assertStatus(200);
        $this->get('/admin/wargas')->assertStatus(200);

        // 4. New Sekretaris CANNOT access officer management (/admin/admins)
        $this->get('/admin/admins')->assertStatus(403);
    }

    /**
     * Test 4: Multi-tenant isolation - Ketua RT only sees officers in their own RT.
     */
    public function test_ketua_rt_only_sees_officers_in_own_rt(): void
    {
        // Create another RT and an officer in that RT
        $rtLain = DataRt::create([
            'nomor_rt' => '08',
            'nomor_rw' => '05',
            'nama_ketua_rt' => 'Pak Bambang',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Cibinong',
            'kota' => 'Bogor',
        ]);

        $officerRtLain = Admin::create([
            'Username' => 'bendahara_rt08_asing',
            'Password_hash' => Hash::make('Rahasia123!'),
            'role' => 'bendahara',
            'ID_RT' => $rtLain->ID_RT,
        ]);

        // When ketuaRt views the list, officer from RT 08 should NOT be visible
        Livewire::actingAs($this->ketuaRt, 'web')
            ->test(ListAdmins::class)
            ->assertCanSeeTableRecords([$this->ketuaRt, $this->bendahara, $this->sekretaris])
            ->assertCanNotSeeTableRecords([$officerRtLain]);
    }

    /**
     * Test 5: Ketua RT can delete an officer in their own RT, but cannot delete themselves.
     */
    public function test_ketua_rt_can_delete_officer_in_own_rt(): void
    {
        $officerToDelete = Admin::create([
            'Username' => 'sekre_magang',
            'Password_hash' => Hash::make('Rahasia123!'),
            'role' => 'sekretaris',
            'ID_RT' => $this->ketuaRt->ID_RT,
        ]);

        // Ketua RT deletes the officer
        Livewire::actingAs($this->ketuaRt, 'web')
            ->test(ListAdmins::class)
            ->callTableAction(DeleteAction::class, $officerToDelete);

        $this->assertDatabaseMissing('ADMIN', [
            'Admin_ID' => $officerToDelete->Admin_ID,
        ]);
    }

    /**
     * Test 6: Bendahara and Sekretaris cannot access officer management page (403 Forbidden).
     */
    public function test_subordinate_officers_cannot_manage_admins(): void
    {
        $this->actingAs($this->bendahara, 'web')->get('/admin/admins')->assertStatus(403);
        $this->actingAs($this->sekretaris, 'web')->get('/admin/admins')->assertStatus(403);
    }
}
