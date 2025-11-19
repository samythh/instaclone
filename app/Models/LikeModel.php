<?php namespace App\Models;

use CodeIgniter\Model;

// Model ini menangani interaksi dengan tabel 'likes'
class LikeModel extends Model
{
    protected $table = 'likes';
    
    // Primary Key tidak didefinisikan secara eksplisit di sini karena tabel likes Anda 
    // menggunakan Primary Key komposit (username, post_id).
    // Kita akan menggunakan metode where() untuk operasi DELETE dan FIND.
    protected $returnType = 'array';
    
    // Kolom yang diizinkan untuk diisi saat pengguna melakukan like
    protected $allowedFields = [
        'post_id', 
        'likername'
    ];
}