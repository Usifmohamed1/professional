<?php

namespace Botble\Page\Services;

class ShortcodeParserService
{
    protected const SHORTCODE_NAME_PATTERN = '/^[a-zA-Z0-9_-]+$/';

    protected array $shortcodes = [];

    protected int $idCounter = 0;

    public function parse(string $content): array
    {
        $this->shortcodes = [];
        $this->idCounter = 0;

        $pattern = $this->getShortcodeRegex();

        if (empty($content)) {
            return [];
        }

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $match) {
            $shortcode = $this->parseShortcode($match);
            if ($shortcode) {
                $this->shortcodes[] = $shortcode;
            }
        }

        return $this->shortcodes;
    }

    protected function parseShortcode(array $match): ?array
    {
        $fullMatch = $match[0][0] ?? '';
        $position = $match[0][1] ?? 0;
        $name = $match[2][0] ?? '';
        $attributesString = $match[3][0] ?? '';
        $content = $match[5][0] ?? '';

        if (empty($name)) {
            return null;
        }

        $attributes = $this->parseAttributes($attributesString);

        $id = $attributes['data-vb-id'] ?? null;
        if (empty($id)) {
            $id = 'shortcode_' . time() . '_' . $this->idCounter++;
        }

        unset($attributes['data-vb-id']);

        return [
            'id' => $id,
            'name' => $name,
            'attributes' => $attributes,
            'content' => $content,
            'position' => $position,
            'raw' => $fullMatch,
            'isSelfClosing' => empty($match[5][0] ?? ''),
        ];
    }

    protected function parseAttributes(string $attributesString): array
    {
        $attributes = [];

        if (empty($attributesString)) {
            return $attributes;
        }

        $attributesString = html_entity_decode($attributesString, ENT_NOQUOTES, 'UTF-8');
        $length = strlen($attributesString);
        $position = 0;

        while ($position < $length) {
            while ($position < $length && ctype_space($attributesString[$position])) {
                $position++;
            }

            $keyStart = $position;

            while (
                $position < $length &&
                (
                    ctype_alnum($attributesString[$position]) ||
                    $attributesString[$position] === '_' ||
                    $attributesString[$position] === '-'
                )
            ) {
                $position++;
            }

            $key = substr($attributesString, $keyStart, $position - $keyStart);

            if ($key === '') {
                $position++;

                continue;
            }

            while ($position < $length && ctype_space($attributesString[$position])) {
                $position++;
            }

            if ($position >= $length || $attributesString[$position] !== '=') {
                continue;
            }

            $position++;

            while ($position < $length && ctype_space($attributesString[$position])) {
                $position++;
            }

            $value = '';

            if ($position < $length && in_array($attributesString[$position], ['"', "'"], true)) {
                $quote = $attributesString[$position++];

                while ($position < $length) {
                    $char = $attributesString[$position];

                    if ($char === '\\' && $position + 1 < $length) {
                        $value .= $char . $attributesString[$position + 1];
                        $position += 2;

                        continue;
                    }

                    if ($char === $quote) {
                        $position++;

                        break;
                    }

                    $value .= $char;
                    $position++;
                }
            } else {
                $valueStart = $position;

                while ($position < $length && ! ctype_space($attributesString[$position])) {
                    $position++;
                }

                $value = substr($attributesString, $valueStart, $position - $valueStart);
            }

            $attributes[$key] = $this->unescapeAttribute($value);
        }

        return $attributes;
    }

    protected function getShortcodeRegex(): string
    {
        return '/\[(\[?)([\w-]+)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*(?:\[(?!\/\2\])[^\[]*)*)(\[\/\2\]))?)(\]?)/';
    }

    public function serialize(array $shortcodes): string
    {
        $content = '';

        foreach ($shortcodes as $shortcode) {
            $name = $shortcode['name'] ?? '';
            $attributes = $shortcode['attributes'] ?? [];
            $innerContent = $shortcode['content'] ?? '';
            $id = $shortcode['id'] ?? '';

            if (empty($name) || ! $this->isValidShortcodeName($name)) {
                continue;
            }

            $shortcodeString = '[' . $name;

            foreach ($attributes as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                $escapedValue = $this->escapeAttribute($value);
                $shortcodeString .= ' ' . $key . '="' . $escapedValue . '"';
            }

            $shortcodeString .= ']';
            if (! empty($innerContent)) {
                $shortcodeString .= $innerContent;
            }
            $shortcodeString .= '[/' . $name . ']';

            $content .= $shortcodeString . "\n\n";
        }

        return trim($content);
    }

    protected function escapeAttribute(mixed $value): string
    {
        if (! is_string($value)) {
            $value = (string) $value;
        }

        $value = preg_replace('/<br[^>]*\/?>/i', '{{BR}}', $value);
        $value = str_replace('&nbsp;', '{{NBSP}}', $value);
        $value = str_replace(["\r\n", "\r", "\n"], '{{NEWLINE}}', $value);

        return str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
    }

    protected function unescapeAttribute(string $value): string
    {
        $value = str_replace(['\\"', '\\\\'], ['"', '\\'], $value);

        return str_replace(
            ['{{NEWLINE}}', '{{BR}}', '{{NBSP}}'],
            ["\n", '<br>', '&nbsp;'],
            $value
        );
    }

    public function getShortcodes(): array
    {
        return $this->shortcodes;
    }

    protected function isValidShortcodeName(string $name): bool
    {
        return (bool) preg_match(self::SHORTCODE_NAME_PATTERN, $name);
    }

    public function decodeForVisualBuilder(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        return html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    }

    public static function removeVisualBuilderIds(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        return preg_replace('/\s*data-vb-id="[^"]*"/', '', $content);
    }
}
