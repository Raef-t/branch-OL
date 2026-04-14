<?php

/**
 * Diagnostic Script for Scheduler Connectivity
 * Usage: Run via browser (http://.../test_scheduler.php) or CLI (php test_scheduler.php)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

header('Content-Type: text/plain; charset=utf-8');

echo "--- Scheduler Connectivity Test ---\n";

$mode = config('services.scheduler.mode');
$url = config('services.scheduler.url');

echo "Current Mode: " . ($mode ?: 'NOT SET') . "\n";
echo "Target URL: " . ($url ?: 'NOT SET') . "\n";
echo "-----------------------------------\n";

if ($mode !== 'docker') {
    echo "⚠️ Warning: SCHEDULER_MODE is not set to 'docker'.\n";
    echo "In non-docker mode, the system uses local shell commands.\n";
}

$healthUrl = rtrim($url, '/') . '/health';
echo "Testing connectivity to: {$healthUrl}\n";

try {
    $start = microtime(true);
    $response = Http::timeout(5)->get($healthUrl);
    $end = microtime(true);
    $duration = round($end - $start, 3);

    echo "Status Code: " . $response->status() . "\n";
    echo "Duration: {$duration}s\n";
    
    if ($response->successful()) {
        echo "✅ SUCCESS: Scheduler is reachable and healthy!\n";
        echo "Response: " . $response->body() . "\n";
    } else {
        echo "❌ FAILED: Scheduler returned an error status.\n";
        echo "Body: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: Could not reach scheduler.\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "\nPossible reasons:\n";
    echo "1. The 'scheduler' container is NOT running.\n";
    echo "2. The 'backend' container is NOT on the same Docker network as 'scheduler'.\n";
    echo "3. DNS for 'scheduler' hostname is not resolving inside the backend container.\n";
}

echo "-----------------------------------\n";
echo "Check laravel.log for detailed trace if needed.\n";
