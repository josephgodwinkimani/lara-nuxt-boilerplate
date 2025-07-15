<?php

namespace App\Http\Serializers;

use League\Fractal\Serializer\ArraySerializer;

class DefaultSerializer extends ArraySerializer
{
    public function collection(?string $resourceKey, array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'count' => count($data),
                'format' => 'json',
            ],
        ];
    }

    public function item(?string $resourceKey, array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'format' => 'json',
            ],
        ];
    }
}
