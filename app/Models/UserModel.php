<?php namespace App\Models;

use CodeIgniter\Model;

// Model ini menangani interaksi dengan tabel 'users'
class UserModel extends Model
{
    // Nama tabel di database
    protected $table = 'users'; 

    // Primary Key (Kunci Utama) dari tabel
    protected $primaryKey = 'username';

    // Tipe data yang dikembalikan dari query
    protected $returnType = 'array';

    // Kolom yang diizinkan untuk diisi (digunakan untuk operasi insert/update yang aman)
    protected $allowedFields = [
        'username', 
        'password', 
        'profile_name', 
        'profile_picture', 
        'email', 
        'bio',
        'followers',
        'followings',
        'posts'
    ];

    // Kolom-kolom ini akan digunakan untuk fitur otentikasi (login/registrasi)
    // Jika Anda ingin menggunakan fitur hashing bawaan CI, Anda bisa melakukannya di sini.
}