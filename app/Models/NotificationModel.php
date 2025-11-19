<?php
namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
   protected $table = 'notifications';
   protected $primaryKey = 'id';
   protected $allowedFields = ['to_username', 'from_username', 'type', 'post_id', 'message', 'is_read', 'created_at'];

   // Fungsi Join Table untuk ambil foto profil pelaku & foto postingan
   public function getNotifications($username)
   {
      return $this->builder()
         ->select('notifications.*, u.profile_picture as actor_pic, p.photo as post_photo')
         ->join('users u', 'u.username = notifications.from_username')
         ->join('posts p', 'p.post_id = notifications.post_id', 'left') // Left join karena notif 'follow' tidak punya post_id
         ->where('notifications.to_username', $username)
         ->where('notifications.from_username !=', $username) // Hindari notif dari diri sendiri
         ->orderBy('notifications.created_at', 'DESC')
         ->get()
         ->getResultArray();
   }
}