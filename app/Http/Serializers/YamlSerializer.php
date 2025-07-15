<?php

namespace App\Http\Serializers;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

class YamlSerializer extends SerializerAbstract
{
    public function collection(?string $resourceKey, array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'count' => count($data),
                'format' => 'yaml',
            ],
        ];
    }

    public function item(?string $resourceKey, array $data): array
    {
        return [
            'data' => $data,
            'meta' => [
                'format' => 'yaml',
            ],
        ];
    }

    public function meta(array $meta): array
    {
        return ['meta' => $meta];
    }

    public function paginator($paginator): array
    {
        $paginatorData = $paginator->getPaginator();

        return [
            'pagination' => [
                'current_page' => $paginatorData->currentPage(),
                'per_page' => $paginatorData->perPage(),
                'total' => $paginatorData->total(),
                'last_page' => $paginatorData->lastPage(),
            ],
        ];
    }

    public function null(): ?array
    {
        return null;
    }

    public function includedData(ResourceInterface $resource, array $data): array
    {
        return $data;
    }

    public function cursor($cursor): array
    {
        return is_array($cursor) ? $cursor : [];
    }
}
