<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'message', 'house_id', 'is_processed'];
    
    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
