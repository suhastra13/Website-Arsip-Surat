<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSurat;

class KategoriSuratSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'Undangan',      'keterangan' => 'Surat undangan rapat, kegiatan, dll'],
            ['nama' => 'Surat Tugas',   'keterangan' => 'Penugasan dinas / perjalanan dinas'],
            ['nama' => 'Nota Dinas',    'keterangan' => 'Nota dinas internal'],
            ['nama' => 'Berita Acara',  'keterangan' => 'Berita acara kegiatan, pemeriksaan, dll'],
            ['nama' => 'Laporan',       'keterangan' => 'Laporan kegiatan / rutin'],
            ['nama' => 'Surat Edaran',  'keterangan' => 'Pengumuman resmi / edaran'],
            ['nama' => 'Lainnya',       'keterangan' => 'Kategori umum lainnya'],
        ];

        foreach ($data as $item) {
            KategoriSurat::firstOrCreate(
                ['nama' => $item['nama']],
                ['keterangan' => $item['keterangan']]
            );
        }
    }
}
