<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = __DIR__ . '/test_payment.log';

try {
    // Test 1: Try direct file writing
    $result1 = file_put_contents($logFile, "Test 1: Direct write at " . date('Y-m-d H:i:s') . "\n");
    echo "Test 1 Result: " . ($result1 !== false ? "Success" : "Failed") . "<br>";
    
    // Test 2: Try with fopen/fwrite
    $fp = fopen($logFile, 'a');
    if ($fp) {
        $result2 = fwrite($fp, "Test 2: fwrite at " . date('Y-m-d H:i:s') . "\n");
        fclose($fp);
        echo "Test 2 Result: " . ($result2 !== false ? "Success" : "Failed") . "<br>";
    } else {
        echo "Test 2: Could not open file<br>";
    }
    
    // Test 3: Check directory permissions
    echo "Directory writable: " . (is_writable(__DIR__) ? "Yes" : "No") . "<br>";
    if (file_exists($logFile)) {
        echo "Log file exists and " . (is_writable($logFile) ? "is" : "is not") . " writable<br>";
    } else {
        echo "Log file does not exist<br>";
    }
    
    // Test 4: Show directory path
    echo "Directory path: " . __DIR__ . "<br>";
    
    // Test 5: List directory contents
    echo "Directory contents:<br>";
    $files = scandir(__DIR__);
    foreach ($files as $file) {
        echo "$file - " . (is_writable(__DIR__ . '/' . $file) ? "Writable" : "Not writable") . "<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
