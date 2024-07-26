<?php

namespace App\Dtos;

use Illuminate\Http\Request;

class TaskDto
{
    public function __construct(
        public readonly string|null $author,
        public readonly string|null $status,
        public readonly string|null $perPage,
        public readonly string|null $priority,
        public readonly string|null $search,
        public readonly string|null $orderBy,
        public readonly string|null $title,
        public readonly string|null $description
    ) {}

    public static function fromRequest(Request $request): TaskDto
    {
        return new self(
            $request->author ?? null,
            $request->status ?? null,
            $request->per_page ?? null,
            $request->priority ?? null,
            $request->search ?? null,
            $request->order_by ?? null,
            $request->title ?? null,
            $request->description ?? null
        );
    }
}
