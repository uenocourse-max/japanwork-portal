<?php

namespace Tests\Feature;

use App\Filament\Resources\LpkTskResource\Pages\CreateLpkTsk;
use App\Filament\Resources\LpkTskResource\Pages\EditLpkTsk;
use App\Models\LpkTsk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LpkTskResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_lpk_tsk_with_recruiter_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(CreateLpkTsk::class)
            ->fillForm([
                'name' => 'LPK Sejahtera',
                'location' => 'Jakarta',
                'email' => 'sejahtera@example.com',
                'password' => 'password',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('lpk_tsks', [
            'email' => 'sejahtera@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'sejahtera@example.com',
            'role' => 'recruiter',
        ]);
    }

    public function test_edit_lpk_tsk_syncs_email_and_name_to_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $recruiter = User::factory()->create([
            'email' => 'lama@example.com',
            'role' => 'recruiter',
        ]);
        $lpk = LpkTsk::create([
            'user_id' => $recruiter->id,
            'name' => 'LPK Lama',
            'email' => 'lama@example.com',
            'password' => 'password',
            'location' => 'Bandung',
        ]);

        Livewire::actingAs($admin)
            ->test(EditLpkTsk::class, [
                'record' => $lpk->getRouteKey(),
            ])
            ->fillForm([
                'name' => 'LPK Baru',
                'location' => 'Surabaya',
                'email' => 'baru@example.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('lpk_tsks', [
            'id' => $lpk->id,
            'name' => 'LPK Baru',
            'email' => 'baru@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $recruiter->id,
            'name' => 'LPK Baru',
            'email' => 'baru@example.com',
            'role' => 'recruiter',
        ]);
    }
}
