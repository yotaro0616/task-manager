<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TaskCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
                'pending_count' => $this->collection->where('status', 'pending')->count(),
                'completed_count' => $this->collection->where('status', 'completed')->count(),
            ],
        ];
    }
}
