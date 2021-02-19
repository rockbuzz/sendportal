<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Sendportal\Base\Facades\Sendportal;
use App\Models\{Workspace, ApiToken, User};
use Sendportal\Base\Models\{Subscriber, Tag, Campaign};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        $user = User::factory()->create([
            'name' => 'Tiago',
            'email' => 'tiago@rockbuzz.com.br',
            'password' => bcrypt(12345678)
        ]);
        $workspace = Workspace::factory()->create([
            'name' => 'Rockbuzz',
            'owner_id' => $user->id
        ]);
        $user->update(['current_workspace_id' => $workspace->id]);
        auth()->login($user);
        ApiToken::factory()->create([
            'workspace_id' => Sendportal::currentWorkspaceId()
        ]);
    
        Tag::factory()->count(10)->create([
            'workspace_id' => Sendportal::currentWorkspaceId()
        ])->each(function ($tag) {
            Subscriber::factory()->count(rand(30, 60))->create([
                'workspace_id' => Sendportal::currentWorkspaceId()
            ])->each(function ($subscriber) use ($tag) {
                $subscriber->tags()->attach($tag);
            });
        });

        Campaign::factory()->count(10)->create();
    }
}
