<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration performs three operations:
     * 1. Updates NULL academic_year_id values to active academic year
     * 2. Merges duplicate Grade records for same student/subject/semester/year
     * 3. Ensures all grades have correct academic_year_id
     */
    public function up(): void
    {
        // Get active academic year
        $activeYear = DB::table('academic_years')->where('is_active', 1)->first();
        
        if (!$activeYear) {
            echo "Warning: No active academic year found. Skipping migration.\n";
            return;
        }

        echo "Using active academic year: {$activeYear->name} (ID: {$activeYear->id})\n";

        // Step 1: Update NULL academic_year_id values
        $nullCount = DB::table('grades')->whereNull('academic_year_id')->count();
        if ($nullCount > 0) {
            DB::table('grades')
                ->whereNull('academic_year_id')
                ->update(['academic_year_id' => $activeYear->id]);
            echo "Step 1: Updated {$nullCount} grades with NULL academic_year_id\n";
        } else {
            echo "Step 1: No NULL academic_year_id values found\n";
        }

        // Step 2: Merge duplicate Grade records
        $duplicates = DB::table('grades')
            ->select('student_id', 'subject_id', 'semester', 'academic_year_id', DB::raw('MIN(id) as keep_id'), DB::raw('GROUP_CONCAT(id) as all_ids'))
            ->groupBy('student_id', 'subject_id', 'semester', 'academic_year_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
            
        $mergedCount = 0;
        foreach ($duplicates as $dup) {
            $ids = explode(',', $dup->all_ids);
            $keepId = $dup->keep_id;
            $deleteIds = array_filter($ids, fn($id) => $id != $keepId);
            
            if (!empty($deleteIds)) {
                // Update grade_tasks to point to the kept Grade
                DB::table('grade_tasks')
                    ->whereIn('grades_id', $deleteIds)
                    ->update(['grades_id' => $keepId]);
                    
                // Delete duplicate Grade records
                DB::table('grades')->whereIn('id', $deleteIds)->delete();
                
                $mergedCount += count($deleteIds);
                echo "Step 2: Merged Grade IDs [" . implode(', ', $deleteIds) . "] into Grade ID {$keepId}\n";
            }
        }
        
        if ($mergedCount > 0) {
            echo "Step 2: Merged {$mergedCount} duplicate grade records\n";
        } else {
            echo "Step 2: No duplicate grades found\n";
        }

        // Step 3: Ensure all grades have correct academic_year_id
        $updated = DB::table('grades')
            ->where('academic_year_id', '!=', $activeYear->id)
            ->orWhereNull('academic_year_id')
            ->update(['academic_year_id' => $activeYear->id]);
            
        if ($updated > 0) {
            echo "Step 3: Updated {$updated} grades to active academic year\n";
        } else {
            echo "Step 3: All grades already have correct academic_year_id\n";
        }

        echo "Migration completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
        echo "Warning: This migration cannot be reversed automatically.\n";
    }
};
