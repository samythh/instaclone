<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
   protected $table = 'notifications';
   protected $primaryKey = 'notification_id';
   protected $allowedFields = ['to_user_id', 'from_user_id', 'type', 'post_id', 'message', 'is_read'];

   public function getNotifications($userId)
   {
      // Panggil service database untuk fitur escape string
      $db = \Config\Database::connect();

      return $this->builder()
         ->select('
                notifications.*, 
                u.username as from_username, 
                u.profile_picture as actor_pic, 
                p.photo as post_photo,
                (
                    SELECT COUNT(*) 
                    FROM followings 
                    WHERE follower_id = ' . $db->escape($userId) . ' 
                    AND followed_id = u.user_id
                ) as is_following
            ')
         ->join('users u', 'u.user_id = notifications.from_user_id')
         ->join('posts p', 'p.post_id = notifications.post_id', 'left')
         ->where('notifications.to_user_id', $userId)
         ->where('notifications.from_user_id !=', $userId)
         ->orderBy('notifications.created_at', 'DESC')
         ->get()->getResultArray();
   }
}