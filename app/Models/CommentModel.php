<?php namespace App\Models;

use CodeIgniter\Model;

// Model ini menangani interaksi dengan tabel 'comments'
class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    protected $returnType = 'array';

    // Kolom yang diizinkan untuk diisi saat pengguna menambahkan komentar
    protected $allowedFields = [
        'commentername', 
        'post_id', 
        'comment_text'
    ];
}