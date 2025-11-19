<?php
namespace App\Controllers;

// File: app/Controllers/PostController.php

use CodeIgniter\Controller;
use App\Models\PostModel;
use App\Models\UserModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\NotificationModel; // TAMBAHAN BARU
use CodeIgniter\Database\RawSql;

class PostController extends Controller
{
   protected $postModel;
   protected $userModel;
   protected $likeModel;
   protected $commentModel;
   protected $notificationModel; // TAMBAHAN BARU
   protected $db;

   public function __construct()
   {
      $this->postModel = new PostModel();
      $this->userModel = new UserModel();
      $this->likeModel = new LikeModel();
      $this->commentModel = new CommentModel();
      $this->notificationModel = new NotificationModel(); // TAMBAHAN BARU
      $this->db = \Config\Database::connect();

      helper(['form', 'url', 'date']);
   }

   // =======================================================
   // 1. HALAMAN BUAT POSTINGAN
   // =======================================================
   public function create()
   {
      $currentUsername = session()->get('username');

      if (!$currentUsername) {
         return redirect()->to(site_url('/'));
      }

      $user = $this->userModel->find($currentUsername);

      $data = [
         'currentUsername' => $currentUsername,
         'profilePicture' => $user['profile_picture'],
         'profileName' => $user['profile_name']
      ];

      return view('post/create_post', $data);
   }

   // =======================================================
   // 2. PROSES SIMPAN POSTINGAN
   // =======================================================
   public function store()
   {
      $currentUsername = session()->get('username');

      $rules = [
         'fileToUpload' => 'uploaded[fileToUpload]|max_size[fileToUpload,5120]|is_image[fileToUpload]'
      ];

      if (!$this->validate($rules)) {
         return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
      }

      $file = $this->request->getFile('fileToUpload');
      $imageName = '';

      if ($file->isValid() && !$file->hasMoved()) {
         $imageName = $file->getRandomName();
         $file->move(ROOTPATH . 'public/photos', $imageName);
      }

      $this->postModel->insert([
         'username' => $currentUsername,
         'photo' => 'photos/' . $imageName,
         'description' => $this->request->getPost('discription'),
         'likes' => 0,
         'comments' => 0
      ]);

      $this->userModel->set('posts', new RawSql('posts + 1'))
         ->where('username', $currentUsername)
         ->update();

      return redirect()->to(site_url('feed/' . $currentUsername));
   }

   // =======================================================
   // 3. LIKE / UNLIKE (DENGAN NOTIFIKASI)
   // =======================================================
   public function toggleLike($postId)
   {
      $liker = session()->get('username');

      // Ambil data post dulu untuk tahu pemiliknya
      $post = $this->postModel->find($postId);

      $checkLike = $this->likeModel->where('post_id', $postId)
         ->where('likername', $liker)
         ->first();
      $isLikedNow = false;

      if ($checkLike) {
         // --- UNLIKE ---
         $this->likeModel->where('post_id', $postId)->where('likername', $liker)->delete();
         $this->postModel->set('likes', new RawSql('likes - 1'))->where('post_id', $postId)->update();
         $isLikedNow = false;

         // Hapus Notifikasi jika di-unlike (Supaya bersih)
         $this->notificationModel->where('from_username', $liker)
            ->where('post_id', $postId)
            ->where('type', 'like')
            ->delete();

      } else {
         // --- LIKE ---
         $this->likeModel->insert(['post_id' => $postId, 'likername' => $liker]);
         $this->postModel->set('likes', new RawSql('likes + 1'))->where('post_id', $postId)->update();
         $isLikedNow = true;

         // KIRIM NOTIFIKASI (Hanya jika yang like bukan diri sendiri)
         if ($post['username'] !== $liker) {
            $this->notificationModel->save([
               'to_username' => $post['username'], // Pemilik post
               'from_username' => $liker,            // Pelaku like
               'type' => 'like',
               'post_id' => $postId,
               'message' => ''
            ]);
         }
      }

      if ($this->request->isAJAX()) {
         $updatedPost = $this->postModel->find($postId);
         return $this->response->setJSON([
            'success' => true,
            'likes' => $updatedPost['likes'],
            'liked' => $isLikedNow
         ]);
      }

      return redirect()->back();
   }

   // =======================================================
   // 4. KOMENTAR (DENGAN NOTIFIKASI)
   // =======================================================
   public function addComment($postId)
   {
      $commenter = session()->get('username');
      $commentText = $this->request->getPost('comment');
      $returnTo = $this->request->getPost('return_to');

      // Ambil data post untuk notifikasi
      $post = $this->postModel->find($postId);

      if (!empty($commentText)) {
         $this->commentModel->insert([
            'post_id' => $postId,
            'commentername' => $commenter,
            'comment_text' => $commentText
         ]);
         $this->postModel->set('comments', new RawSql('comments + 1'))->where('post_id', $postId)->update();

         // KIRIM NOTIFIKASI KOMENTAR
         if ($post['username'] !== $commenter) {
            $this->notificationModel->save([
               'to_username' => $post['username'],
               'from_username' => $commenter,
               'type' => 'comment',
               'post_id' => $postId,
               'message' => $commentText // Simpan isi komen
            ]);
         }
      }

      if ($returnTo === 'detail') {
         return redirect()->to(site_url('post/detail/' . $postId));
      }
      return redirect()->to(site_url('feed/' . $commenter));
   }

   // =======================================================
   // 5. DETAIL POST
   // =======================================================
   public function detail($postId)
   {
      $currentUsername = session()->get('username');
      $post = $this->postModel->find($postId);

      if (!$post) {
         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
      }

      $poster = $this->userModel->find($post['username']);
      $comments = $this->commentModel->where('post_id', $postId)->findAll();
      $isLiked = $this->likeModel->where('post_id', $postId)->where('likername', $currentUsername)->countAllResults() > 0;

      $postTime = strtotime($post['time_stamp'] ?? 'now');
      $timeAgo = floor((time() - $postTime) / 86400) . " HARI YANG LALU";

      $data = [
         'post' => $post,
         'poster' => $poster,
         'comments' => $comments,
         'currentUsername' => $currentUsername,
         'isLiked' => $isLiked,
         'timeAgo' => $timeAgo
      ];

      if ($this->request->isAJAX()) {
         return view('post/view_modal', $data);
      }

      return view('post/detail_post', $data);
   }

   // =======================================================
   // 6. HAPUS POSTINGAN
   // =======================================================
   public function delete($postId)
   {
      $currentUsername = session()->get('username');
      $post = $this->postModel->find($postId);

      if (!$post || $post['username'] !== $currentUsername) {
         return redirect()->back();
      }

      $filePath = ROOTPATH . 'public/' . $post['photo'];
      if (file_exists($filePath)) {
         unlink($filePath);
      }

      $this->likeModel->where('post_id', $postId)->delete();
      $this->commentModel->where('post_id', $postId)->delete();
      // Hapus juga notifikasi terkait post ini
      $this->notificationModel->where('post_id', $postId)->delete();

      $this->postModel->delete($postId);

      $this->userModel->set('posts', new RawSql('posts - 1'))->where('username', $currentUsername)->update();

      return redirect()->to(site_url('profile/' . $currentUsername));
   }

   // =======================================================
   // 7. HALAMAN EDIT POSTINGAN
   // =======================================================
   public function edit($postId)
   {
      $currentUsername = session()->get('username');
      $post = $this->postModel->find($postId);

      if (!$post || $post['username'] !== $currentUsername) {
         return redirect()->back();
      }

      $user = $this->userModel->find($currentUsername);

      $data = [
         'post' => $post,
         'currentUsername' => $currentUsername,
         'profilePicture' => $user['profile_picture']
      ];

      return view('post/edit_post', $data);
   }

   // =======================================================
   // 8. UPDATE POSTINGAN
   // =======================================================
   public function update($postId)
   {
      $currentUsername = session()->get('username');
      $post = $this->postModel->find($postId);

      if (!$post || $post['username'] !== $currentUsername) {
         return redirect()->to(site_url('feed/' . $currentUsername));
      }

      $rules = ['discription' => 'required'];

      if (!$this->validate($rules)) {
         return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
      }

      $this->postModel->update($postId, [
         'description' => $this->request->getPost('discription')
      ]);

      return redirect()->to(site_url('post/detail/' . $postId));
   }
}