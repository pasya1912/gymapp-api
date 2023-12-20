<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;


class GeneralResource extends JsonResource
{
    public static function formatResponse(array $data): array
    {
        return [
            'status' => $data['status'] ?? null,
            'message' => $data['message'] ?? null,
            'data' => $data['data'] ?? null,
        ];
    }

    public function toArray($request)
    {
        return static::formatResponse($this->resource);
    }
}

