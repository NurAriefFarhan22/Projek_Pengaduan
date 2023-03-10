<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Response extends Model
{
    use HasFactory;
    protected $fillable = [
        'report_id',
        'status',
        'pesan',
    ];
    // belongsTo : disambungkan dengan table nama (PK nya ada dimana)
    // table yang berperan sebagai FK
    // nama fungsi == nama model PK
    public function report()
    {
        return $this->belongsTo
        (Report::class);
    }
}
