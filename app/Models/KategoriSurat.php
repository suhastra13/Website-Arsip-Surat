<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSurat extends Model
{
    use HasFactory;

    protected $table = 'kategori_surat';

    protected $fillable = [
        'nama',
        'keterangan',
    ];

    public function surat()
    {
        return $this->hasMany(Surat::class, 'kategori_id');
    }
}
