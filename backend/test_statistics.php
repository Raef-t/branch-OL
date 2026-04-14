<?php

use Illuminate\Http\Request;
use Modules\Exams\Filters\ExamsStatisticsFilter;
use Modules\Exams\Services\ExamsStatisticsService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing ExamsStatisticsService with Year 2026...\n";

try {
    $service = app(ExamsStatisticsService::class);
    
    // Test with year 2026 and NO month
    $request = Request::create('/api/exams/statistics/top-performers', 'GET', [
        'year'  => 2026
    ]);
    
    $filter = ExamsStatisticsFilter::fromRequest($request);
    echo "Filter params: Month=" . ($filter->month ?? 'NULL') . ", Year=" . ($filter->year ?? 'NULL') . "\n";
    
    $results = $service->getMonthlyTopPerformers($filter);
    
    echo "Results found: " . count($results) . "\n";
    foreach ($results as $result) {
        printf("- %-20s: %d/%d (%.2f%%) at %s [Student ID: %d]\n", 
            $result['student_name'], 
            $result['total_obtained'], 
            $result['total_possible'], 
            $result['average_percentage'],
            $result['institute_branch_name'],
            $result['student_id']
        );
    }
    
    echo "\nSuccess!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
