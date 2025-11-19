<?php namespace App\Models;

use CodeIgniter\Model;

// Model ini menangani interaksi dengan tabel 'users'
class UserModel extends Model
{
    protected $table = 'users'; 

    protected $primaryKey = 'username';

    protected $returnType = 'array';

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

}