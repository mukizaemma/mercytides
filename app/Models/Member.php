<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    protected $fillable = [
        'names',
        'email',
        'phone',
        'address',
        'province',
        'district',
        'sector',
        'cell',
        'membership',
        'gender',
        'martual',
        'age',
        'dateJoined',
        'program_id',
        'status',
        'child_info',
        'challenge',
        'document',
    ];
}
