<?php

namespace Tests\Feature;

use App\Exceptions\DeletionRestrictedException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Students\Models\Student;
use Tests\TestCase;

class StudentDeletionRestrictionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['audit.enabled' => false]);

        Schema::dropIfExists('student_messages');
        Schema::dropIfExists('door_sessions');
        Schema::dropIfExists('student_exit_logs');
        Schema::dropIfExists('batch_student');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('academic_records');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('enrollment_contracts');
        Schema::dropIfExists('students');

        Schema::create('students', function (Blueprint $table): void {
            $table->id();
        });

        Schema::create('enrollment_contracts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('exam_results', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('academic_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('batch_student', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('student_exit_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('door_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });

        Schema::create('student_messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
        });
    }

    public function test_it_blocks_deleting_student_when_related_records_exist(): void
    {
        $studentId = DB::table('students')->insertGetId([]);
        $student = Student::query()->findOrFail($studentId);

        DB::table('enrollment_contracts')->insert([
            'student_id' => $studentId,
        ]);

        $this->expectException(DeletionRestrictedException::class);
        $this->expectExceptionMessage('عقود التسجيل');

        $student->delete();
    }

    public function test_it_allows_deleting_student_when_no_related_records_exist(): void
    {
        $studentId = DB::table('students')->insertGetId([]);
        $student = Student::query()->findOrFail($studentId);

        $deleted = $student->delete();

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('students', ['id' => $studentId]);
    }
}

