<?php namespace App\Models;

use CodeIgniter\Model;

// Model ini menangani interaksi dengan tabel 'posts' (Postingan foto)
class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    protected $returnType = 'array';

    // Kolom yang diizinkan untuk diisi saat membuat postingan baru
    protected $allowedFields = [
        'username', 
        'photo', 
        'description', 
        'likes', 
        'comments'
    ];
}