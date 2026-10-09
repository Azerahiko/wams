<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'status',
        'budget',
        'start_date',
        'due_date',
    ];

    protected $casts = [
        'status' => 'string',
        'budget' => 'int',
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the client that owns the project.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
