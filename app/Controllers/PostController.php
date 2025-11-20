<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PostModel;
use App\Models\UserModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
use App\Models\NotificationModel;
use CodeIgniter\Database\RawSql;

class PostController extends Controller
{
   protected $postModel;
   protected $userModel;
   protected $likeModel;
   protected $commentModel;
   protected $notificationModel;
   protected $db;

   public function __construct()
   {
      $this->postModel = new PostModel();
      $this->userModel = new UserModel();
      $this->likeModel = new LikeModel();
      $this->commentModel = new CommentModel();
      $this->notificationModel = new NotificationModel();
      $this->db = \Config\Database::connect();

      helper(['form', 'url', 'date']);
   }

   public function create()
   {
      $currentId = session()->get('id');

      if (!$currentId) {
         return redirect()->to(site_url('/'));
      }

      $user = $this->userModel->find($currentId);

      $data = [
         'currentUsername' => $user['username'],
         'profilePicture' => $user['profile_picture'],
         'profileName' => $user['profile_name']
      ];

      return view('post/create_post', $data);
   }

   public function store()
   {
      $currentId = session()->get('id');
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
         'user_id' => $currentId,
         'photo' => 'photos/' . $imageName,
         'description' => $this->request->getPost('discription'),
         'likes' => 0,
         'comments' => 0
      ]);

      $this->userModel->set('posts', new RawSql('posts + 1'))
         ->where('id', $currentId)
         ->update();

      return redirect()->to(site_url('feed/' . $currentUsername));
   }

   public function toggleLike($postId)
   {
      $userId = session()->get('id');

      $post = $this->postModel->find($postId);

      $checkLike = $this->likeModel->where('post_id', $postId)
         ->where('user_id', $userId)
         ->first();
      $isLikedNow = false;

      if ($checkLike) {
         $this->likeModel->where('id', $checkLike['id'])->delete();
         $this->postModel->set('likes', new RawSql('likes - 1'))->where('post_id', $postId)->update();
         $isLikedNow = false;

         $this->notificationModel->where('from_user_id', $userId)
            ->where('post_id', $postId)
            ->where('type', 'like')
            ->delete();
      } else {
         $this->likeModel->insert(['post_id' => $postId, 'user_id' => $userId]);
         $this->postModel->set('likes', new RawSql('likes + 1'))->where('post_id', $postId)->update();
         $isLikedNow = true;

         if ($post['user_id'] != $userId) {
            $this->notificationModel->save([
               'to_user_id' => $post['user_id'],
               'from_user_id' => $userId,
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

   public function addComment($postId)
   {
      $userId = session()->get('id');
      $currentUsername = session()->get('username');
      $commentText = $this->request->getPost('comment');
      $returnTo = $this->request->getPost('return_to');

      $post = $this->postModel->find($postId);

      if (!empty($commentText)) {
         $this->commentModel->insert([
            'post_id' => $postId,
            'user_id' => $userId,
            'comment_text' => $commentText
         ]);
         $this->postModel->set('comments', new RawSql('comments + 1'))->where('post_id', $postId)->update();

         if ($post['user_id'] != $userId) {
            $this->notificationModel->save([
               'to_user_id' => $post['user_id'],
               'from_user_id' => $userId,
               'type' => 'comment',
               'post_id' => $postId,
               'message' => $commentText
            ]);
         }
      }

      if ($returnTo === 'detail') {
         return redirect()->to(site_url('post/detail/' . $postId));
      }
      return redirect()->to(site_url('feed/' . $currentUsername));
   }

   public function detail($postId)
   {
      $currentId = session()->get('id');
      $currentUsername = session()->get('username');

      $post = $this->postModel->find($postId);

      if (!$post) {
         throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
      }

      $poster = $this->userModel->find($post['user_id']);

      $comments = $this->commentModel->getCommentsByPost($postId);

      $isLiked = $this->likeModel->where('post_id', $postId)
         ->where('user_id', $currentId)
         ->countAllResults() > 0;

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

   public function delete($postId)
   {
      $currentId = session()->get('id');
      $currentUsername = session()->get('username');

      $post = $this->postModel->find($postId);

      if (!$post || $post['user_id'] != $currentId) {
         return redirect()->back();
      }

      $filePath = ROOTPATH . 'public/' . $post['photo'];
      if (file_exists($filePath)) {
         unlink($filePath);
      }

      $this->postModel->delete($postId);

      $this->userModel->set('posts', new RawSql('posts - 1'))
         ->where('id', $currentId)
         ->update();

      return redirect()->to(site_url('profile/' . $currentUsername));
   }

   public function edit($postId)
   {
      $currentId = session()->get('id');
      $currentUsername = session()->get('username');

      $post = $this->postModel->find($postId);

      if (!$post || $post['user_id'] != $currentId) {
         return redirect()->back();
      }

      $user = $this->userModel->find($currentId);

      $data = [
         'post' => $post,
         'currentUsername' => $currentUsername,
         'profilePicture' => $user['profile_picture']
      ];

      return view('post/edit_post', $data);
   }

   public function update($postId)
   {
      $currentId = session()->get('id');
      $currentUsername = session()->get('username');

      $post = $this->postModel->find($postId);

      if (!$post || $post['user_id'] != $currentId) {
         return redirect()->to(site_url('feed/' . $currentUsername));
      }

      $rules = [
         'discription' => 'required'
      ];

      if (!$this->validate($rules)) {
         return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
      }

      $this->postModel->update($postId, [
         'description' => $this->request->getPost('discription')
      ]);

      return redirect()->to(site_url('post/detail/' . $postId));
   }
}