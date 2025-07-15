<?php

namespace App\Http\Serializers;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

class CsvSerializer extends SerializerAbstract
{
    public function collection(?string $resourceKey, array $data): array
    {
        return $data;
    }

    public function item(?string $resourceKey, array $data): array
    {
        return [$data];
    }

    public function meta(array $meta): array
    {
        return [];
    }

    public function paginator($paginator): array
    {
        return [];
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

    public static function toCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        // Handle single item
        if (!isset($data[0])) {
            $data = [$data];
        }

        $output = fopen('php://temp', 'r+');

        // Write headers
        $headers = array_keys($data[0]);
        fputcsv($output, $headers);

        // Write data rows
        foreach ($data as $row) {
            fputcsv($output, array_values($row));
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
