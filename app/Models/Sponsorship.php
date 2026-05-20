<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsorship extends Model
{
    use HasFactory, SoftDeletes;
    protected $table ='sponsorships';

    protected $fillable = [
        'names',
        'age',
        'sex',
        'status',
        'phone',
        'contact',
        'address',
        'testimany',
        'monthly_need',
        'image',
    ];

    public function Child(){
        return $this->hasMany(Donate::class);
    }
}
