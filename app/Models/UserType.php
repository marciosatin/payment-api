<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{

    use HasFactory;
    
    const TYPE_COMUM = 'comum';
    const TYPE_LOJISTA = 'lojista';
    
    
    protected $table = 'users_types';
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_name',
    ];

}