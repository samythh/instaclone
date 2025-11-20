<?php
namespace App\Models;
use CodeIgniter\Model;

class LikeModel extends Model
{
    protected $table = 'likes';
    protected $allowedFields = ['post_id', 'user_id'];
}