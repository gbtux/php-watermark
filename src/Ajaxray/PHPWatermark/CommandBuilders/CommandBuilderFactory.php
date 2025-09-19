<?php declare(strict_types=1);

namespace Ajaxray\PHPWatermark\CommandBuilders;

final class CommandBuilderFactory
{
    private const PATTERN_MIME_IMAGE = '/^image\/\w{1,4}$/';
    private const PATTERN_MIME_PDF = '/^application\/(x\-)?pdf$/';

    private const PATTERN_MIME_OCTET = '/^application\/(x\-)?octet-stream$/';

    public static function getCommandBuilder(string $sourcePath): WatermarkCommandBuilderInterface
    {
        $mimeType = mime_content_type($sourcePath);

        if (preg_match(self::PATTERN_MIME_IMAGE, $mimeType)) {
            return new ImageCommandBuilder($sourcePath);
        }

        if (preg_match(self::PATTERN_MIME_PDF, $mimeType)) {
            return new PDFCommandBuilder($sourcePath);
        }

        if (preg_match(self::PATTERN_MIME_OCTET, $mimeType)) {
            return new PDFCommandBuilder($sourcePath);
        }

        throw new \InvalidArgumentException("The source file type $mimeType is not supported.");
    }
}
