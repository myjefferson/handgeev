<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'title',
        'subtitle',
        'description',
        'start_date',
        'end_date',
        'status',
        'technologies_used',
        'project_link',
        'git_repository_link'
    ];
}
