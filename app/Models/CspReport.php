<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CspReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_uri',
        'referrer',
        'violated_directive',
        'blocked_uri',
        'source_file',
        'line_number',
        'column_number',
        'domain_id'
    ];

    // Define the relationship to the Domain model
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
