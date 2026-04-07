<?php

namespace App\Mcp\Servers;

use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Sentosa Quiz')]
#[Version('0.0.1')]
#[Instructions('A server that can read folder structure and file contents of the Laravel project.')]
class SentosaQuiz extends Server
{
    protected array $tools = [
    'get_folder_structure',
    'get_file_content',
    'write_file',
    'create_folder',
    'delete_file',
];

public function write_file(string $path, string $content): array
{
    $fullPath = base_path($path);

    // Block sensitive files
    if (basename($fullPath) === '.env') {
        return ['error' => 'Writing to this file is not allowed.'];
    }

    // Auto-create directory if it doesn't exist
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    file_put_contents($fullPath, $content);

    return [
        'success' => true,
        'path' => $path,
        'message' => "File '$path' written successfully.",
    ];
}

public function create_folder(string $path): array
{
    $fullPath = base_path($path);

    if (is_dir($fullPath)) {
        return ['error' => "Folder '$path' already exists."];
    }

    mkdir($fullPath, 0755, true);

    return [
        'success' => true,
        'path' => $path,
        'message' => "Folder '$path' created successfully.",
    ];
}

public function delete_file(string $path): array
{
    $fullPath = base_path($path);

    // Block sensitive files
    if (basename($fullPath) === '.env') {
        return ['error' => 'Deleting this file is not allowed.'];
    }

    if (!file_exists($fullPath)) {
        return ['error' => "File '$path' does not exist."];
    }

    unlink($fullPath);

    return [
        'success' => true,
        'path' => $path,
        'message' => "File '$path' deleted successfully.",
    ];
}
}