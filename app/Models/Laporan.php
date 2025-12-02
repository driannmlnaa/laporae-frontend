<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Laporan extends Model
{
    use HasFactory;

    public const KATEGORI_OPTIONS = [
        'Keamanan',
        'Aksesibilitas',
        'Fasilitas Rusak',
    ];

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'lokasi',
        'foto',
        'status',
        'pelapor_id',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        $foto = $this->attributes['foto'] ?? null;
        if (! $foto) {
            return null;
        }

        if (Str::startsWith($foto, ['http://', 'https://'])) {
            return $foto;
        }

        if (File::exists(public_path($foto))) {
            return asset($foto);
        }

        if (Storage::disk('public')->exists($foto)) {
            return Storage::disk('public')->url($foto);
        }

        return null;
    }
}
