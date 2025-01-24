<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'movement_id', 'value', 'dt_record'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['dt_record', 'start_at', 'conclusion_at'];
}