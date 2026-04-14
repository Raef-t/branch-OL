<?php

namespace Tests\Feature;

use App\Exceptions\DeletionRestrictedException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\AcademicBranches\Models\AcademicBranch;
use Tests\TestCase;

class AcademicBranchDeletionRestrictionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['audit.enabled' => false]);

        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('batches');
        Schema::dropIfExists('academic_branches');

        Schema::create('academic_branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
        });

        Schema::create('subjects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('academic_branch_id')->nullable();
        });

        Schema::create('batches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('academic_branch_id')->nullable();
            $table->boolean('is_hidden')->default(false);
        });
    }

    public function test_it_blocks_deleting_branch_when_related_records_exist(): void
    {
        $branch = AcademicBranch::query()->create([
            'name' => 'Science',
        ]);

        DB::table('students')->insert([
            'branch_id' => $branch->id,
        ]);

        $this->expectException(DeletionRestrictedException::class);
        $this->expectExceptionMessage('الطلاب');

        $branch->delete();
    }

    public function test_it_allows_deleting_branch_when_no_related_records_exist(): void
    {
        $branch = AcademicBranch::query()->create([
            'name' => 'Literature',
        ]);

        $deleted = $branch->delete();

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('academic_branches', ['id' => $branch->id]);
    }
}

