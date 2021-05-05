<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;
use Sendportal\Base\Models\Tag;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    public function setUp(): void
    {
        putenv("SENDPORTAL_REGISTER=false");
        putenv("SENDPORTAL_PASSWORD_RESET=false");

        parent::setUp();
    }

    /** @test */
    function must_return_all_tags_with_subscribers_count()
    {
        $this->withoutEvents();
        
        [$workspace, $user] = $this->createUserAndWorkspace();

        $this->loginUser($user);

        $tag = Tag::factory()->create(['workspace_id' => $workspace->id]);

        $response = $this->get(route('api.all-tags'));

        $this->assertEquals($tag->id, $response->decodeResponseJson()['data'][0]['id']);
    }
}
