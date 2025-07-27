<?php declare(strict_types=1);

namespace Ajaxray\PHPWatermark\CommandBuilders;

final class PDFCommandBuilder extends AbstractCommandBuilder implements WatermarkCommandBuilderInterface
{
    /** @inheritDoc */
    public function getImageMarkCommand(string $markerImage, string $output, array $options): string
    {
        list($source, $destination) = $this->prepareContext($output, $options);
        $marker = escapeshellarg($markerImage);

        $opacity = $this->getMarkerOpacity();
        $anchor = $this->getAnchor();
        $offset = $this->getImageOffset();
        $quality = $this->getQuality();
        $density = $this->getDensity();

        return sprintf(
            "convert %s %s  miff:- | convert -density %s %s null: - -%s -%s -quality %s -compose multiply -layers composite %s",
            $marker,
            $opacity,
            $density,
            $source,
            $anchor,
            $offset,
            $quality,
            $destination
        );
    }

    /** @inheritDoc */
    public function getTextMarkCommand(string $text, string $output, array $options): string
    {
        list($source, $destination) = $this->prepareContext($output, $options);
        $text = escapeshellarg($text);

        $anchor = $this->getAnchor();
        $rotate = $this->getRotate();
        $font = $this->getFont();
        $quality = $this->getQuality();
        $density = $this->getDensity();

        list($light, $dark) = $this->getDuelTextColor();
        list($offsetLight, $offsetDark) = $this->getDuelTextOffset();

        return sprintf(
            "convert %s -%s -quality %s -density %s %s -%s -annotate %s%s %s -%s -annotate %s%s %s  %s",
            $source,
            $anchor,
            $quality,
            $density,
            $font,
            $light,
            $rotate,
            $offsetLight,
            $text,
            $dark,
            $rotate,
            $offsetDark,
            $text,
            $destination
        );
    }

    private function getMarkerOpacity(): string
    {
        $opacity = $this->getOpacity() * 100;

        return "-alpha set -channel A -evaluate set {$opacity}%";
    }

    protected function getDuelTextOffset(): array
    {
        $offset = $this->getOffset();

        return [
            "+{$offset[0]}+{$offset[1]}",
            '+'.($offset[0] + 1) .'+'. ($offset[1] + 1),
        ];
    }

    protected function getRotate(): string
    {
        return empty($this->options['rotate']) ? '' : "{$this->options['rotate']}x{$this->options['rotate']}";
    }

    protected function getDuelTextColor(): array
    {
        return [
            "fill \"rgba(255,255,255,{$this->getOpacity()})\"",
            "fill \"rgba(0,0,0,{$this->getOpacity()})\"",
        ];
    }
}
