#!/usr/bin/env php
<?php
/**
 * Blade-safe obfuscation for deploy.
 *
 * YakPro-po cannot safely obfuscate Blade (mixed HTML/PHP). This script:
 * - strips Blade and HTML comments
 * - collapses insignificant whitespace
 * - leaves Blade directives, {{ }}, {!! !!}, and @php blocks intact for runtime
 *
 * Usage:
 *   php bin/obfuscate-blade.php <source-views-dir> <target-views-dir>
 */

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php bin/obfuscate-blade.php <source-dir> <target-dir>\n");
    exit(1);
}

$sourceDir = rtrim($argv[1], '/');
$targetDir = rtrim($argv[2], '/');

if (!is_dir($sourceDir)) {
    fwrite(STDERR, "Source directory not found: {$sourceDir}\n");
    exit(1);
}

function ensureDir(string $dir): void
{
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException("Unable to create directory: {$dir}");
    }
}

/**
 * Protect regions that must not be minified, run a transform, then restore.
 *
 * @param array<int, string> $patterns
 */
function protectAndTransform(string $content, array $patterns, callable $transform): string
{
    $vault = [];
    $i = 0;

    foreach ($patterns as $pattern) {
        $content = preg_replace_callback($pattern, function (array $m) use (&$vault, &$i): string {
            $key = "___BLADE_KEEP_{$i}___";
            $vault[$key] = $m[0];
            $i++;

            return $key;
        }, $content) ?? $content;
    }

    $content = $transform($content);

    // Restore in reverse so nested placeholders resolve correctly if ever nested.
    foreach (array_reverse($vault, true) as $key => $original) {
        $content = str_replace($key, $original, $content);
    }

    return $content;
}

function obfuscateBlade(string $content): string
{
    // Strip Blade comments first.
    $content = preg_replace('/\{\{--.*?--\}\}/s', '', $content) ?? $content;
    // Strip HTML comments (keep IE conditional comments if any: <!--[if ...]>).
    $content = preg_replace('/<!--(?!\[if\b).*?-->/s', '', $content) ?? $content;

    $protectedPatterns = [
        // @php ... @endphp blocks
        '/@php\b.*?@endphp/s',
        // Escaped / raw Blade echoes
        '/\{\{.*?\}\}/s',
        '/\{!!.*?!!\}/s',
        // Blade directives with parentheses (keep args intact)
        '/@[a-zA-Z_]+\s*\([^;]*?\)/s',
    ];

    return protectAndTransform($content, $protectedPatterns, function (string $plain): string {
        // Collapse runs of whitespace to a single space outside protected regions.
        $plain = preg_replace('/[ \t]+/', ' ', $plain) ?? $plain;
        $plain = preg_replace('/\s*\n\s*/', "\n", $plain) ?? $plain;
        // Remove blank lines.
        $plain = preg_replace("/\n{2,}/", "\n", $plain) ?? $plain;

        return trim($plain) . "\n";
    });
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS)
);

$count = 0;
foreach ($iterator as $file) {
    /** @var SplFileInfo $file */
    if (!$file->isFile()) {
        continue;
    }

    $relative = substr($file->getPathname(), strlen($sourceDir) + 1);
    $dest = $targetDir . '/' . $relative;
    ensureDir(dirname($dest));

    if (str_ends_with($file->getFilename(), '.blade.php')) {
        $raw = file_get_contents($file->getPathname());
        if ($raw === false) {
            throw new RuntimeException("Unable to read: {$file->getPathname()}");
        }
        $out = obfuscateBlade($raw);
        if (file_put_contents($dest, $out) === false) {
            throw new RuntimeException("Unable to write: {$dest}");
        }
        $count++;
    } else {
        if (!copy($file->getPathname(), $dest)) {
            throw new RuntimeException("Unable to copy: {$file->getPathname()}");
        }
    }
}

fwrite(STDOUT, "Obfuscated {$count} Blade view(s) into {$targetDir}\n");
