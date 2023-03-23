<?php
namespace App\Models;

class GradingSalary extends \Eloquent
{
    protected $table = 'grading_salary';

    protected $fillable = [
        'kode_grade',
        'salary_bulanan',
        'periode_umk',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $guarded = [''];

    protected $hidden = [''];

     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'kode_grade',
        'salary_bulanan',
        'periode_umk',
        'operator',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [];
    public $incrementing = false;
    // public $primaryKey = null;
    public $primaryKey = ['kode_grade'];


}
