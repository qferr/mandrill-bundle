<?php

namespace Qferrer\Symfony\MandrillBundle\Utils;

/**
 * Class JsonUtils
 */
class JsonUtils
{
    private function __construct()
    {
    }

    /**
     * Decodes a JSON data to array
     *
     * @param string $data
     *
     * @return array
     */
    public static function decode(string $data): array
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('The data must be a string');
        }

        if (false === $result = @json_decode($data, true)) {
            throw new \RuntimeException('Unable to decode the data');
        }

        return $result;
    }
}