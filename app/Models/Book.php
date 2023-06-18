<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property string|null $gambar
 */
class Book extends Model
{
    use HasFactory;
     protected $fillable = ['judul','penulis','penerbit','tahun_terbit', 'harga', 'sinopsis', 'gambar'];
     public function getUrlAttribute()
    {
        if ($this->gambar) {
            return asset('images/' . $this->gambar);
        }

        // Jika tidak ada gambar, kembalikan URL gambar default
        return asset('images/default.jpg');
    }
}
