<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    use HasFactory;

    protected $table = 'volunteers';

    protected $fillable = [
        'names',
        'slug',
        'email',
        'address',
        'country',
        'aboutYou',
        'career',
        'howToServe',
        'program_id',
        'status',
    ];
}
