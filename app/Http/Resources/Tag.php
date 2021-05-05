<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Tag extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subscribers_count' => $this->subscribers_count,
            'created_at' => $this->created_at->toDateTimeString(),
            'update_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
