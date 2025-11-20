<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\NotificationModel;
use App\Models\UserModel;

class NotificationController extends Controller
{
   protected $notificationModel;
   protected $userModel;

   public function __construct()
   {
      $this->notificationModel = new NotificationModel();
      $this->userModel = new UserModel();
      helper(['url', 'date', 'text']);
   }

   public function index()
   {
      $currentId = session()->get('id');
      $currentUsername = session()->get('username');

      if (!$currentId) {
         return redirect()->to(site_url('/'));
      }

      $user = $this->userModel->find($currentId);

      $notifs = $this->notificationModel->getNotifications($currentId);

      $data = [
         'currentUsername' => $currentUsername,
         'profilePicture' => $user['profile_picture'],
         'profileName' => $user['profile_name'],
         'notifications' => $notifs
      ];

      return view('notification/index', $data);
   }

   public function loadNotifications()
   {
      $currentId = session()->get('id');

      if (!$currentId)
         return "";

      $notifs = $this->notificationModel->getNotifications($currentId);

      $data = [
         'notifications' => $notifs
      ];

      return view('notification/content', $data);
   }
}