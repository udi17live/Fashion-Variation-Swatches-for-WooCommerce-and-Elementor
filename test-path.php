<?php
/**
 * Test script to debug plugin directory path issues
 */

echo "<h1>Plugin Directory Path Test</h1>";

// Test different ways to get the plugin directory
echo "<h2>Path Resolution Tests</h2>";

// Method 1: Using __FILE__
echo "<p><strong>__FILE__:</strong> " . __FILE__ . "</p>";

// Method 2: Using dirname(__FILE__)
echo "<p><strong>dirname(__FILE__):</strong> " . dirname(__FILE__) . "</p>";

// Method 3: Using dirname(__FILE__) with trailing slash
echo "<p><strong>dirname(__FILE__) with trailing slash:</strong> " . dirname(__FILE__) . "/" . "</p>";

// Method 4: Using realpath
echo "<p><strong>realpath(dirname(__FILE__)):</strong> " . realpath(dirname(__FILE__)) . "</p>";

// Method 5: Using realpath with trailing slash
echo "<p><strong>realpath(dirname(__FILE__)) with trailing slash:</strong> " . realpath(dirname(__FILE__)) . "/" . "</p>";

// Test file existence with different paths
echo "<h2>File Existence Tests</h2>";

$test_file = 'includes/class-fashion-variation-swatches-core.php';

$paths_to_test = [
    'dirname(__FILE__)' => dirname(__FILE__) . '/' . $test_file,
    'dirname(__FILE__) with trailing slash' => dirname(__FILE__) . '/' . $test_file,
    'realpath(dirname(__FILE__))' => realpath(dirname(__FILE__)) . '/' . $test_file,
    'realpath(dirname(__FILE__)) with trailing slash' => realpath(dirname(__FILE__)) . '/' . $test_file,
];

foreach ($paths_to_test as $method => $path) {
    $exists = file_exists($path);
    $readable = is_readable($path);
    echo "<p><strong>$method:</strong> $path</p>";
    echo "<p>Exists: " . ($exists ? '✅ Yes' : '❌ No') . "</p>";
    echo "<p>Readable: " . ($readable ? '✅ Yes' : '❌ No') . "</p>";
    echo "<hr>";
}

// Test all required files
echo "<h2>All Required Files Test</h2>";

$required_files = [
    'includes/class-fashion-variation-swatches-core.php',
    'includes/class-fashion-variation-swatches-admin.php',
    'includes/class-fashion-variation-swatches-frontend.php',
    'includes/class-fashion-variation-swatches-attributes.php',
    'includes/class-fashion-variation-swatches-shop-filters.php',
];

$base_path = dirname(__FILE__) . '/';

foreach ($required_files as $file) {
    $full_path = $base_path . $file;
    $exists = file_exists($full_path);
    $readable = is_readable($full_path);
    
    echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px 0;'>";
    echo "<p><strong>File:</strong> $file</p>";
    echo "<p><strong>Full Path:</strong> $full_path</p>";
    echo "<p><strong>Exists:</strong> " . ($exists ? '✅ Yes' : '❌ No') . "</p>";
    echo "<p><strong>Readable:</strong> " . ($readable ? '✅ Yes' : '❌ No') . "</p>";
    echo "</div>";
}

// Test directory listing
echo "<h2>Directory Listing Test</h2>";

$includes_dir = $base_path . 'includes/';
echo "<p><strong>Includes Directory:</strong> $includes_dir</p>";
echo "<p><strong>Directory Exists:</strong> " . (is_dir($includes_dir) ? '✅ Yes' : '❌ No') . "</p>";

if (is_dir($includes_dir)) {
    $files = scandir($includes_dir);
    echo "<p><strong>Files in includes directory:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<h2>System Information</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Current Working Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>"; 