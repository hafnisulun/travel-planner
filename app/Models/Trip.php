<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $hidden = ['id', 'user_id', 'deleted_at'];

    public function getStartAtAttribute($value)
    {
        return Carbon::createFromTimeString($value);
    }

    public function getEndAtAttribute($value)
    {
        return Carbon::createFromTimeString($value);
    }
}
