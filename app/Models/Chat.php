<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    // Define mass-assignable attributes
    protected $fillable = ['collection_id', 'prompt', 'response'];
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
