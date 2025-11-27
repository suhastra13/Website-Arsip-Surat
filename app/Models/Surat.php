<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KategoriSurat;
use App\Models\User;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surat';

    protected $fillable = [
        'tipe',
        'kategori_id',
        'kode_arsip',
        'no_surat',
        'tanggal_surat',
        'tanggal_terima',
        'tanggal_keluar',
        'asal_surat',
        'tujuan_surat',
        'perihal',
        'ringkasan',
        'penandatangan',
        'tingkat_penting',
        'file_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_surat'   => 'date',
        'tanggal_terima'  => 'date',
        'tanggal_keluar'  => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
