<?php
namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model
{
   protected $table = 'notifications';
   protected $primaryKey = 'id';
   protected $allowedFields = ['to_user_id', 'from_user_id', 'type', 'post_id', 'message', 'is_read'];

   public function getNotifications($userId)
   {
      return $this->builder()
         ->select('notifications.*, u.username as from_username, u.profile_picture as actor_pic, p.photo as post_photo')
         ->join('users u', 'u.id = notifications.from_user_id')
         ->join('posts p', 'p.post_id = notifications.post_id', 'left')
         ->where('notifications.to_user_id', $userId)
         ->where('notifications.from_user_id !=', $userId)
         ->orderBy('notifications.created_at', 'DESC')
         ->get()->getResultArray();
   }
}