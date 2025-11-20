<?php
namespace App\Models;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'photo', 'description', 'likes', 'comments'];
}