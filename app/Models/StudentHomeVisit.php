<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentHomeVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        // Student Information
        'student_id', // Foreign key to students table (normalized)
        'student_name',
        'class',
        'birth_date',
        'gender',
        'id_card',
        'phone',
        
        // Address Information
        'house_number',
        'village_number',
        'village_name',
        'province',
        'district',
        'subdistrict',
        'postal_code',
        
        // Family Information
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
        
        // Home Environment
        'house_type',
        'ownership_status',
        'utilities',
        'study_space',
        'internet_access',
        
        // Student Behavior and Learning
        'learning_behavior',
        'home_responsibilities',
        'extracurricular',
        'academic_support',
        'challenges',
        'family_expectations',
        
        // Visit Information
        'visit_date',
        'visit_time',
        'visitor_name',
        'visitor_position',
        
        // Recommendations
        'recommendations',
        'follow_up',
        'next_visit',
        
        // Photos and signatures
        'photos',
        'parent_signature',
        'visitor_signature',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'visit_date' => 'date',
        'next_visit' => 'date',
        'utilities' => 'array',
        'photos' => 'array',
        'internet_access' => 'boolean',
    ];

    protected $dates = [
        'birth_date',
        'visit_date',
        'next_visit',
        'created_at',
        'updated_at',
    ];

    // Scopes
    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }

    public function scopeByStudentName($query, $name)
    {
        return $query->where('student_name', 'like', '%' . $name . '%');
    }

    public function scopeRecentVisits($query, $days = 30)
    {
        return $query->where('visit_date', '>=', now()->subDays($days));
    }

    // Accessors
    public function getFormattedVisitDateAttribute()
    {
        return $this->visit_date ? $this->visit_date->format('d/m/Y') : null;
    }

    public function getPhotosUrlsAttribute()
    {
        if (!$this->photos) {
            return [];
        }
        
        return collect($this->photos)->map(function ($photo) {
            return asset('storage/' . $photo);
        })->toArray();
    }

    // Methods
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->house_number ? 'บ้านเลขที่ ' . $this->house_number : null,
            $this->village_number ? 'หมู่ที่ ' . $this->village_number : null,
            $this->village_name ? $this->village_name : null,
            $this->subdistrict ? 'ตำบล' . $this->subdistrict : null,
            $this->district ? 'อำเภอ' . $this->district : null,
            $this->province ? 'จังหวัด' . $this->province : null,
            $this->postal_code ? $this->postal_code : null,
        ]);
        
        return implode(' ', $addressParts);
    }

    /**
     * Relationship to Student (normalized database)
     * HYBRID APPROACH: HomeVisit can work both standalone and with relational data
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get student data - tries relationship first, falls back to standalone data
     */
    public function getStudentDataAttribute()
    {
        // If we have student_id and relationship exists, use normalized data
        if ($this->student_id && $this->student) {
            return [
                'id' => $this->student->id,
                'student_id' => $this->student->student_id,
                'name' => $this->student->first_name_th . ' ' . $this->student->last_name_th,
                'citizen_id' => $this->student->citizen_id,
                'class' => $this->student->academicInfo->current_class ?? $this->class,
                'birth_date' => $this->student->date_of_birth ?? $this->birth_date,
                'source' => 'normalized' // Data from students table
            ];
        }
        
        // Fall back to standalone data
        return [
            'id' => null,
            'student_id' => null,
            'name' => $this->student_name,
            'citizen_id' => $this->id_card,
            'class' => $this->class,
            'birth_date' => $this->birth_date,
            'source' => 'standalone' // Data from home_visits table
        ];
    }

    /**
     * Scope to find by student identifier (works with both approaches)
     */
    public function scopeByStudentIdentifier($query, $identifier)
    {
        return $query->where(function($q) use ($identifier) {
            $q->where('student_id', $identifier)
              ->orWhere('id_card', $identifier)
              ->orWhere('student_name', 'like', "%{$identifier}%");
        });
    }

    /**
     * Auto-link to Student record if possible (called when saving)
     */
    public function linkToStudent()
    {
        if (!$this->student_id && $this->id_card) {
            $student = Student::where('citizen_id', $this->id_card)->first();
            if ($student) {
                $this->student_id = $student->id;
                $this->save();
            }
        }
    }

    /**
     * Boot method to auto-link on save
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($homeVisit) {
            // Auto-link to student if possible
            if (!$homeVisit->student_id && $homeVisit->id_card) {
                $student = Student::where('citizen_id', $homeVisit->id_card)->first();
                if ($student) {
                    $homeVisit->student_id = $student->id;
                    
                    // Also sync standalone data from normalized database
                    $homeVisit->student_name = $student->first_name_th . ' ' . $student->last_name_th;
                    $homeVisit->class = $student->academicInfo->current_class ?? $homeVisit->class;
                    $homeVisit->birth_date = $homeVisit->birth_date ?? $student->date_of_birth;
                    $homeVisit->gender = $homeVisit->gender ?? ($student->gender === 'male' ? 'ชาย' : 'หญิง');
                }
            }
        });
    }

    /**
     * Bulk sync existing records with student database
     */
    public static function syncWithStudentDatabase()
    {
        $unlinkedVisits = self::whereNull('student_id')->get();
        $syncCount = 0;
        
        foreach ($unlinkedVisits as $visit) {
            if ($visit->id_card) {
                $student = Student::where('citizen_id', $visit->id_card)->first();
                if ($student) {
                    $visit->update([
                        'student_id' => $student->id,
                        'student_name' => $student->first_name_th . ' ' . $student->last_name_th,
                        'class' => $student->academicInfo->current_class ?? $visit->class,
                        'birth_date' => $visit->birth_date ?? $student->date_of_birth,
                        'gender' => $visit->gender ?? ($student->gender === 'male' ? 'ชาย' : 'หญิง'),
                    ]);
                    $syncCount++;
                }
            }
        }
        
        return $syncCount;
    }

    /**
     * Create home visit with smart data population
     */
    public static function createFromStudent(Student $student, array $visitData)
    {
        return self::create(array_merge([
            // Relational data
            'student_id' => $student->id,
            
            // Standalone backup data
            'student_name' => $student->first_name_th . ' ' . $student->last_name_th,
            'id_card' => $student->citizen_id,
            'class' => $student->academicInfo->current_class ?? 'N/A',
            'birth_date' => $student->date_of_birth,
            'gender' => $student->gender === 'male' ? 'ชาย' : 'หญิง',
            'phone' => $student->contacts->first()->phone_number ?? '',
            
            // Address from student data
            'house_number' => $student->addresses->first()->house_number ?? '',
            'village_number' => $student->addresses->first()->village_number ?? '',
            'subdistrict' => $student->addresses->first()->subdistrict ?? '',
            'district' => $student->addresses->first()->district ?? '',
            'province' => $student->addresses->first()->province ?? '',
            'postal_code' => $student->addresses->first()->postal_code ?? '',
        ], $visitData));
    }

    /**
     * Get the images for this home visit
     */
    public function images()
    {
        return $this->hasMany(HomeVisitImage::class, 'home_visit_id');
    }

    /**
     * Get the evidence images
     */
    public function evidenceImages()
    {
        return $this->images()->where('image_type', 'evidence');
    }

    /**
     * Get the activity images
     */
    public function activityImages()
    {
        return $this->images()->where('image_type', 'activity');
    }
}
