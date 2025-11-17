<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'admin']);
    }

    public function test_supervisor_sees_only_owned_or_assigned_projects(): void
    {
        $supervisor = User::factory()->create();
        $supervisor->assignRole('supervisor');

        $otherSupervisor = User::factory()->create();
        $otherSupervisor->assignRole('supervisor');

        $ownedProject = Project::factory()->create(['created_by' => $supervisor->id]);
        $assignedProject = Project::factory()->create(['created_by' => $otherSupervisor->id]);
        $assignedProject->users()->attach($supervisor->id);
        $otherProject = Project::factory()->create(['created_by' => $otherSupervisor->id]);

        $response = $this->actingAs($supervisor)->get('/projects');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('projects', function ($projects) use ($ownedProject, $assignedProject) {
                $ids = collect($projects)->pluck('id');
                return $ids->contains($ownedProject->id)
                    && $ids->contains($assignedProject->id)
                    && $ids->count() === 2;
            })
        );
    }

    public function test_admin_sees_all_projects(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $projects = Project::factory()->count(3)->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->get('/projects');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->where('projects', fn ($list) => count($list) === 3)
            ->where('projects', function ($list) use ($projects) {
                $ids = collect($list)->pluck('id')->sort()->values()->all();
                $expected = $projects->pluck('id')->sort()->values()->all();
                return $ids === $expected;
            })
        );
    }
}
