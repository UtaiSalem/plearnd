<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCard extends Model
{
    protected $fillable = [
        'order_no',
        'student_fullname_th',
        'student_class',
        'student_section',
        'id_card_no',
        'student_id',
        'level_room',
        'prefix_name',
        'student_name_th',
        'last_name_th',
        'student_name_en',
		'date_of_birth',
        'date_of_birth_str',
        'issue_date',
        'expiry_date',
        'status',
        'student_image',
    ];
}
