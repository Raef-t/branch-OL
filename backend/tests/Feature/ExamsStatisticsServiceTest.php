<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Exams\Filters\ExamsStatisticsFilter;
use Modules\Exams\Services\ExamsStatisticsService;
use Tests\TestCase;

class ExamsStatisticsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('audit.enabled', false);

        $this->recreateSchema();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_filter_defaults_to_current_month_and_year_when_no_period_is_provided(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 10:00:00'));

        $filter = ExamsStatisticsFilter::fromRequest(
            Request::create('/api/exams/statistics/top-performers', 'GET')
        );

        $this->assertSame(3, $filter->month);
        $this->assertSame(2026, $filter->year);
        $this->assertNull($filter->examTypeId);
        $this->assertNull($filter->instituteBranchId);
    }

    public function test_service_uses_average_of_exam_percentages_and_keeps_students_at_or_above_ninety_percent(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 10:00:00'));

        $this->seedReferenceData();

        $this->insertStudent(1, 1, 'Ali', 'Top');
        $this->insertStudent(2, 1, 'Omar', 'Below');

        DB::table('batches')->insert([
            ['id' => 1, 'institute_branch_id' => 1, 'is_hidden' => false],
        ]);

        DB::table('batch_subjects')->insert([
            ['id' => 1, 'batch_id' => 1],
            ['id' => 2, 'batch_id' => 1],
            ['id' => 3, 'batch_id' => 1],
        ]);

        DB::table('exams')->insert([
            ['id' => 1, 'batch_subject_id' => 1, 'exam_type_id' => 1, 'exam_date' => '2026-03-05', 'total_marks' => 100],
            ['id' => 2, 'batch_subject_id' => 2, 'exam_type_id' => 1, 'exam_date' => '2026-03-10', 'total_marks' => 10],
            ['id' => 3, 'batch_subject_id' => 3, 'exam_type_id' => 1, 'exam_date' => '2026-02-10', 'total_marks' => 100],
        ]);

        DB::table('exam_results')->insert([
            ['exam_id' => 1, 'student_id' => 1, 'obtained_marks' => 100],
            ['exam_id' => 2, 'student_id' => 1, 'obtained_marks' => 8],
            ['exam_id' => 3, 'student_id' => 1, 'obtained_marks' => 100],
            ['exam_id' => 1, 'student_id' => 2, 'obtained_marks' => 89],
            ['exam_id' => 2, 'student_id' => 2, 'obtained_marks' => 9],
        ]);

        $filter = ExamsStatisticsFilter::fromRequest(
            Request::create('/api/exams/statistics/top-performers', 'GET')
        );

        $results = app(ExamsStatisticsService::class)->getMonthlyTopPerformers($filter)->values();

        $this->assertCount(1, $results);
        $this->assertSame(1, $results[0]['student_id']);
        $this->assertSame('Ali Top', $results[0]['student_name']);
        $this->assertSame('Branch One', $results[0]['institute_branch_name']);
        $this->assertSame(108.0, $results[0]['total_obtained']);
        $this->assertSame(110, $results[0]['total_possible']);
        $this->assertSame(90.0, $results[0]['average_percentage']);
    }

    public function test_service_applies_exam_type_and_branch_filters_when_provided(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-03-15 10:00:00'));

        $this->seedReferenceData();

        $this->insertStudent(1, 1, 'Ali', 'North');
        $this->insertStudent(2, 2, 'Sara', 'South');

        DB::table('batches')->insert([
            ['id' => 1, 'institute_branch_id' => 1, 'is_hidden' => false],
            ['id' => 2, 'institute_branch_id' => 2, 'is_hidden' => false],
        ]);

        DB::table('batch_subjects')->insert([
            ['id' => 1, 'batch_id' => 1],
            ['id' => 2, 'batch_id' => 2],
            ['id' => 3, 'batch_id' => 1],
        ]);

        DB::table('exams')->insert([
            ['id' => 1, 'batch_subject_id' => 1, 'exam_type_id' => 1, 'exam_date' => '2026-03-05', 'total_marks' => 100],
            ['id' => 2, 'batch_subject_id' => 2, 'exam_type_id' => 1, 'exam_date' => '2026-03-06', 'total_marks' => 100],
            ['id' => 3, 'batch_subject_id' => 3, 'exam_type_id' => 2, 'exam_date' => '2026-03-07', 'total_marks' => 100],
        ]);

        DB::table('exam_results')->insert([
            ['exam_id' => 1, 'student_id' => 1, 'obtained_marks' => 95],
            ['exam_id' => 2, 'student_id' => 2, 'obtained_marks' => 96],
            ['exam_id' => 3, 'student_id' => 1, 'obtained_marks' => 99],
        ]);

        $filter = ExamsStatisticsFilter::fromRequest(
            Request::create('/api/exams/statistics/top-performers', 'GET', [
                'month' => 3,
                'year' => 2026,
                'exam_type_id' => 1,
                'institute_branch_id' => 1,
            ])
        );

        $results = app(ExamsStatisticsService::class)->getMonthlyTopPerformers($filter)->values();

        $this->assertCount(1, $results);
        $this->assertSame(1, $results[0]['student_id']);
        $this->assertSame(95.0, $results[0]['average_percentage']);
    }

    private function recreateSchema(): void
    {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('batch_subjects');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('students');
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('institute_branches');

        Schema::create('institute_branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('exam_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('institute_branch_id')->nullable();
            $table->text('first_name')->nullable();
            $table->string('first_name_hash')->nullable();
            $table->text('last_name')->nullable();
            $table->string('last_name_hash')->nullable();
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('institute_branch_id')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        Schema::create('batch_subjects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('batch_subject_id');
            $table->unsignedBigInteger('exam_type_id')->nullable();
            $table->date('exam_date');
            $table->unsignedInteger('total_marks');
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('obtained_marks', 5, 2);
            $table->timestamps();
        });
    }

    private function seedReferenceData(): void
    {
        DB::table('institute_branches')->insert([
            ['id' => 1, 'name' => 'Branch One'],
            ['id' => 2, 'name' => 'Branch Two'],
        ]);

        DB::table('exam_types')->insert([
            ['id' => 1, 'name' => 'Monthly'],
            ['id' => 2, 'name' => 'Final'],
        ]);
    }

    private function insertStudent(int $id, int $branchId, string $firstName, string $lastName): void
    {
        DB::table('students')->insert([
            'id' => $id,
            'institute_branch_id' => $branchId,
            'first_name' => Crypt::encryptString($firstName),
            'first_name_hash' => sha1($firstName),
            'last_name' => Crypt::encryptString($lastName),
            'last_name_hash' => sha1($lastName),
        ]);
    }
}
