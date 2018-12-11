<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\TestDox;

class Color
{
    private const WHITESPACE_MAP = [
        ' '  => '·',
        "\t" => '⇥',
        "\n" => '↵',
    ];

    /**
     * @var array
     */
    private static $ansiCodes = [
        'reset'             => '0',
        'bold'              => '1',
        'dim'               => '2',
        'dim-reset'         => '22',
        'underlined'        => '4',
        'fg-default'        => '39',
        'fg-black'          => '30',
        'fg-red'            => '31',
        'fg-green'          => '32',
        'fg-yellow'         => '33',
        'fg-blue'           => '34',
        'fg-magenta'        => '35',
        'fg-cyan'           => '36',
        'fg-white'          => '37',
        'bg-default'        => '49',
        'bg-black'          => '40',
        'bg-red'            => '41',
        'bg-green'          => '42',
        'bg-yellow'         => '43',
        'bg-blue'           => '44',
        'bg-magenta'        => '45',
        'bg-cyan'           => '46',
        'bg-white'          => '47',
    ];

    public static function colorize(string $color, string $buffer): string
    {
        if (\trim($buffer) === '') {
            return $buffer;
        }

        $codes   = \array_map('\trim', \explode(',', $color));

        $styles = [];

        foreach ($codes as $code) {
            if (isset(self::$ansiCodes[$code])) {
                $styles[] = self::$ansiCodes[$code] ?? '';
            }
        }

        if (empty($styles)) {
            return $buffer;
        }

        return \sprintf("\x1b[%sm", \implode(';', $styles)) . $buffer . "\x1b[0m";
    }

    public static function colorizePath(string $path, ?string $prevPath = null): string
    {
        if ($prevPath === null) {
            $prevPath = '';
        }

        $path     = \explode(\DIRECTORY_SEPARATOR, $path);
        $prevPath = \explode(\DIRECTORY_SEPARATOR, $prevPath);

        for ($i = 0; $i < \min(\count($path), \count($prevPath)); $i++) {
            if ($path[$i] == $prevPath[$i]) {
                $path[$i] = self::dim($path[$i]);
            }
        }

        return \implode(self::dim(\DIRECTORY_SEPARATOR), $path);
    }

    public static function dim(string $buffer): string
    {
        if (\trim($buffer) === '') {
            return $buffer;
        }

        return "\x1b[2m$buffer\x1b[22m";
    }

    public static function visualizeWhitespace(string $buffer): string
    {
        return \preg_replace_callback('/\s+/', function ($matches) {
            return self::dim(\strtr($matches[0], self::WHITESPACE_MAP));
        }, $buffer);
    }
}