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
      $currentUsername = session()->get('username');

      // Cek Login
      if (!$currentUsername) {
         return redirect()->to(site_url('/'));
      }

      // Ambil data user untuk sidebar
      $user = $this->userModel->find($currentUsername);

      // Ambil data notifikasi dari Model
      $notifs = $this->notificationModel->getNotifications($currentUsername);

      $data = [
         'currentUsername' => $currentUsername,
         'profilePicture' => $user['profile_picture'], // Untuk sidebar
         'profileName' => $user['profile_name'],    // Untuk sidebar
         'notifications' => $notifs
      ];

      return view('notification/index', $data);
   }

   // =======================================================
   // FUNGSI AJAX: AMBIL KONTEN NOTIFIKASI SAJA
   // =======================================================
   public function loadNotifications()
   {
      $currentUsername = session()->get('username');

      if (!$currentUsername)
         return ""; // Kosong jika tidak login

      $notifs = $this->notificationModel->getNotifications($currentUsername);

      $data = [
         'notifications' => $notifs
      ];

      // Kita return view parsial baru
      return view('notification/content', $data);
   }

   
}