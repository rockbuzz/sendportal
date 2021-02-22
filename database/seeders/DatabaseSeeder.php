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
            'workspace_id' => $workspace->id,
            'api_token' => 'oRVdaoQvlInKeeuZNnrwbOIIIOkGIPPQ'
        ]);
    
        $tags = Tag::factory()->count(10)->create()->each(function ($tag) {
            Subscriber::factory()->count(rand(30, 60))
                ->create()
                ->each(function ($subscriber) use ($tag) {
                $subscriber->tags()->attach($tag);
            });
        });

        Campaign::factory()->count(10)->create([
            'from_name' => config('mail.from.name'),
            'from_email' => config('mail.from.address')
        ])->each(function ($campaign) use ($tags) {
            $campaign->tags()->attach($tags->random(rand(1,3)));
        });
    }
}
