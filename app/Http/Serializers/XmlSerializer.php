<?php

namespace App\Http\Serializers;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\SerializerAbstract;

class XmlSerializer extends SerializerAbstract
{
    public function collection(?string $resourceKey, array $data): array
    {
        return [
            'items' => $data,
            'count' => count($data),
        ];
    }

    public function item(?string $resourceKey, array $data): array
    {
        return $data;
    }

    public function meta(array $meta): array
    {
        return $meta;
    }

    public function paginator($paginator): array
    {
        $paginatorData = $paginator->getPaginator();

        return [
            'current_page' => $paginatorData->currentPage(),
            'per_page' => $paginatorData->perPage(),
            'total' => $paginatorData->total(),
            'last_page' => $paginatorData->lastPage(),
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

    public static function toXml(array $data, string $rootElement = 'response'): string
    {
        $xml = new \SimpleXMLElement("<{$rootElement}/>");
        self::arrayToXml($data, $xml);

        return $xml->asXML();
    }

    private static function arrayToXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml->addChild($key);
                self::arrayToXml($value, $subnode);
            } else {
                $xml->addChild($key, htmlspecialchars((string) $value));
            }
        }
    }
}
