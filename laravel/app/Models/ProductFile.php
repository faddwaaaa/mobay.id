<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'file',
        'platform',
        'file_url',
    ];

    protected $casts = [
        'platform' => 'string',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Apakah file ini menggunakan URL eksternal (bukan upload langsung)?
     */
    public function isExternalUrl(): bool
    {
        return $this->platform !== 'upload';
    }

    /**
     * Ambil URL download yang sesuai.
     * - Upload  → storage URL
     * - Lainnya → file_url langsung
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->platform === 'upload') {
            return $this->file ? asset('storage/' . $this->file) : null;
        }

        return $this->file_url;
    }

    /**
     * Nama file yang ditampilkan ke user.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->platform === 'upload' && $this->file) {
            return basename($this->file);
        }

        $labels = [
            'dropbox' => 'Dropbox',
            'gdrive'  => 'Google Drive',
            'other'   => 'Link Download',
        ];

        return $labels[$this->platform] ?? 'File';
    }

    /**
     * Icon/label platform.
     */
    public function getPlatformLabelAttribute(): string
    {
        return match ($this->platform) {
            'dropbox' => 'Dropbox',
            'gdrive'  => 'Google Drive',
            'other'   => 'Other',
            default   => 'Upload',
        };
    }
}