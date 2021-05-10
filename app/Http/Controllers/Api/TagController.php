<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Exception;
use Sendportal\Base\Models\Subscriber;
use Sendportal\Base\Models\Tag;
use App\Http\Controllers\Controller;
use Sendportal\Base\Facades\Sendportal;
use App\Http\Resources\Tag as TagResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Sendportal\Base\Http\Resources\Subscriber as SubscriberResource;

class TagController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        return TagResource::collection(
            Tag::where('workspace_id', $workspaceId)->get()
        );
    }

    /**
     * @throws Exception
     */
    public function show(int $tag): TagResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        return new TagResource(
            Tag::where('workspace_id', $workspaceId)->where('id', $tag)->firstOrFail()
        );
    }

    public function subscribers(int $tag): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        $tag = Tag::where('workspace_id', $workspaceId)->where('id', $tag)->firstOrFail();

        return SubscriberResource::collection($tag->subscribers()->paginate(25));
    }
}
