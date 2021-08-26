<?php

namespace Triangulum\Yii\ModuleContainer\System\Json;

use JsonException;

final class Json
{
    /**
     * @return mixed
     * @throws JsonException
     */
    public static function decode(string $json, bool $assoc = true, int $depth = 512)
    {
        return json_decode(
            $json,
            $assoc,
            $depth,
            JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param     $data
     * @param int $depth
     * @return false|string
     * @throws JsonException
     */
    public static function encode($data, int $depth = 512)
    {
        return json_encode(
            $data,
            JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
            $depth
        );
    }

    public static function dbEmptyValue(): string
    {
        return json_encode([], JSON_FORCE_OBJECT);
    }

    public static function decodeSoft(string $json): array
    {
        if (empty($json) || $json === self::dbEmptyValue() || $json === '[]') {
            return [];
        }

        return Json::decode($json);
    }
}
