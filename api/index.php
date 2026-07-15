<?php
// api/index.php - Vercel PHP Front Controller & Router

$projectRoot = dirname(__DIR__);

// Get the requested path
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($requestUri, PHP_URL_PATH);

// Normalize path (remove leading and trailing slashes)
$path = trim($path, '/');

// Default to index.php if path is empty
if ($path === '') {
    $path = 'index.php';
}

// Support clean URLs (e.g. '/about' -> 'about.php')
if (!str_ends_with($path, '.php') && !str_contains($path, '.')) {
    if (file_exists($projectRoot . '/' . $path . '.php')) {
        $path .= '.php';
    }
}

// Define the file path relative to project root
$fileToInclude = $projectRoot . '/' . $path;

// Block direct access to private folders via router
if (preg_match('/^(config|includes|db|scratch)/i', $path)) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
}

// Check if the file exists and is a PHP file
if (file_exists($fileToInclude) && is_file($fileToInclude)) {
    // Set SCRIPT_FILENAME to the included file so the script has proper context
    $_SERVER['SCRIPT_FILENAME'] = $fileToInclude;
    
    // Crucial: Change the working directory to the target file's directory
    // so that its relative require/include statements resolve correctly.
    chdir(dirname($fileToInclude));
    
    // Execute the PHP file
    require $fileToInclude;
} else {
    http_response_code(404);
    if (file_exists($projectRoot . '/404.php')) {
        chdir($projectRoot);
        require $projectRoot . '/404.php';
    } elseif (file_exists($projectRoot . '/404.html')) {
        readfile($projectRoot . '/404.html');
    } else {
        echo "404 Not Found";
    }
}
