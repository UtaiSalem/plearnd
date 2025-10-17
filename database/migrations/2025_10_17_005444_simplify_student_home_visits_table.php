<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_home_visits', function (Blueprint $table) {
            // Drop redundant student information columns (now in students table)
            $table->dropColumn([
                'student_name',
                'class',
                'birth_date',
                'gender',
                'id_card',
                'phone',
                
                // Drop address columns (now in student_addresses table)
                'house_number',
                'village_number',
                'village_name',
                'province',
                'district',
                'subdistrict',
                'postal_code',
                
                // Drop family info (should be in separate guardian/family table)
                'father_name',
                'father_age',
                'father_occupation',
                'mother_name',
                'mother_age',
                'mother_occupation',
                'guardian_name',
                'guardian_relationship',
                'siblings_count',
                'sibling_position',
                
                // Drop home environment (can be in visit observations)
                'house_type',
                'ownership_status',
                'utilities',
                'study_space',
                'internet_access',
                
                // Drop behavior columns (should be in visit notes/observations)
                'learning_behavior',
                'home_responsibilities',
                'extracurricular',
                'academic_support',
                'challenges',
                'family_expectations',
                
                // Drop old photo storage (now using home_visit_images table)
                'photos',
                'parent_signature',
                'visitor_signature',
            ]);
            
            // Add new essential columns
            $table->text('observations')->nullable()->after('visit_time')->comment('สภาพแวดล้อมและการสังเกตการณ์');
            $table->text('notes')->nullable()->after('observations')->comment('บันทึกเพิ่มเติม');
            $table->enum('visit_status', ['completed', 'pending', 'rescheduled'])->default('completed')->after('notes');
            $table->unsignedBigInteger('created_by')->nullable()->after('next_visit')->comment('Teacher ID who created');
            
            // Modify existing columns
            $table->time('visit_time')->nullable()->change();
            $table->string('visitor_position')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_home_visits', function (Blueprint $table) {
            // Restore old columns
            $table->string('student_name')->after('id');
            $table->string('class', 10)->after('student_name');
            $table->date('birth_date')->after('class');
            $table->enum('gender', ['ชาย', 'หญิง'])->after('birth_date');
            $table->string('id_card', 17)->after('gender');
            $table->string('phone', 15)->after('id_card');
            
            $table->string('house_number', 20)->after('phone');
            $table->string('village_number', 10)->nullable()->after('house_number');
            $table->string('village_name', 100)->nullable()->after('village_number');
            $table->string('province', 100)->after('village_name');
            $table->string('district', 100)->after('province');
            $table->string('subdistrict', 100)->after('district');
            $table->string('postal_code', 5)->after('subdistrict');
            
            $table->string('father_name')->nullable()->after('postal_code');
            $table->integer('father_age')->nullable()->after('father_name');
            $table->string('father_occupation')->nullable()->after('father_age');
            $table->string('mother_name')->nullable()->after('father_occupation');
            $table->integer('mother_age')->nullable()->after('mother_name');
            $table->string('mother_occupation')->nullable()->after('mother_age');
            $table->string('guardian_name')->nullable()->after('mother_occupation');
            $table->string('guardian_relationship', 100)->nullable()->after('guardian_name');
            $table->integer('siblings_count')->nullable()->after('guardian_relationship');
            $table->integer('sibling_position')->nullable()->after('siblings_count');
            
            $table->string('house_type', 100)->nullable()->after('sibling_position');
            $table->string('ownership_status', 100)->nullable()->after('house_type');
            $table->json('utilities')->nullable()->after('ownership_status');
            $table->string('study_space', 100)->nullable()->after('utilities');
            $table->boolean('internet_access')->nullable()->after('study_space');
            
            $table->text('learning_behavior')->nullable()->after('internet_access');
            $table->text('home_responsibilities')->nullable()->after('learning_behavior');
            $table->text('extracurricular')->nullable()->after('home_responsibilities');
            $table->text('academic_support')->nullable()->after('extracurricular');
            $table->text('challenges')->nullable()->after('academic_support');
            $table->text('family_expectations')->nullable()->after('challenges');
            
            $table->json('photos')->nullable()->after('next_visit');
            $table->text('parent_signature')->nullable()->after('photos');
            $table->text('visitor_signature')->nullable()->after('parent_signature');
            
            // Drop new columns
            $table->dropColumn(['observations', 'notes', 'visit_status', 'created_by']);
        });
    }
};
