<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_name_and_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('avatar.png', 50, 'image/png');

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'profile_photo' => $file,
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_replacing_avatar_removes_old_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'profile_photo_path' => UploadedFile::fake()
                ->create('old.png', 40, 'image/png')
                ->store('profile-photos', 'public'),
        ]);

        $oldPath = $user->profile_photo_path;

        $response = $this->actingAs($user)->put('/profile', [
            'name' => $user->name,
            'profile_photo' => UploadedFile::fake()->create('new.png', 45, 'image/png'),
        ]);

        $response->assertRedirect();

        $user->refresh();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }
}
