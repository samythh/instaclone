<?php
namespace App\Models;
use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    protected $allowedFields = ['user_id', 'post_id', 'comment_text'];

    public function getCommentsByPost($postId)
    {
        return $this->select('comments.*, users.username as commentername, users.profile_picture')
            ->join('users', 'users.user_id = comments.user_id')
            ->where('post_id', $postId)
            ->findAll();
    }
}