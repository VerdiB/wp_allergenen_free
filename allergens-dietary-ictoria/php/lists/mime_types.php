<?php
enum Mime_Types: string
{
    case png = 'image/png';
    case jpeg = 'image/jpeg';
    case jpg = 'image/jpg';
    case svg = 'image/svg+xml';
    case webp = 'image/webp';
    public static function get_mime_types(): array
    {
        return [
            self::png->value,
            self::jpeg->value,
            self::jpg->value,
            self::svg->value,
            self::webp->value,
        ];
    }
}
