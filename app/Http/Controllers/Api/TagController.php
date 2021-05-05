<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Exception;
use Sendportal\Base\Models\Tag;
use App\Http\Controllers\Controller;
use Sendportal\Base\Facades\Sendportal;
use App\Http\Resources\Tag as TagResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
