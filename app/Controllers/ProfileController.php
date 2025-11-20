<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\RawSql;

class ProfileController extends Controller
{
    protected $userModel;
    protected $postModel;
    protected $notificationModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
        helper(['url', 'form']);
    }

    public function index($profileUsername)
    {
        $currentId = session()->get('id');
        $currentUsername = session()->get('username');

        $profileUser = $this->userModel->where('username', $profileUsername)->first();

        if (!$profileUser) {
            return redirect()->to(site_url("feed/{$currentUsername}"))->with('error', 'Profil tidak ditemukan.');
        }

        $targetId = $profileUser['user_id'];

        $isFollowing = $this->db->table('followings')
            ->where('follower_id', $currentId)
            ->where('followed_id', $targetId)
            ->countAllResults() > 0;

        $posts = $this->postModel->where('user_id', $targetId)
            ->orderBy('time_stamp', 'DESC')
            ->findAll();

        $data = [
            'profileUser' => $profileUser,
            'currentUsername' => $currentUsername,
            'isFollowing' => $isFollowing,
            'posts' => $posts,
            'isOwner' => ($currentId == $targetId)
        ];

        return view('profile/profile_index', $data);
    }

    public function toggleFollow($targetUsername)
    {
        $currentId = session()->get('id');
        $followingsTable = $this->db->table('followings');

        $targetUser = $this->userModel->where('username', $targetUsername)->first();
        if (!$targetUser)
            return redirect()->back();

        $targetId = $targetUser['user_id'];

        $check = $followingsTable->where('follower_id', $currentId)
            ->where('followed_id', $targetId)
            ->get()->getRow();

        if ($check) {
            $followingsTable->where('following_id', $check->following_id)->delete();

            $this->userModel->set('followers', new RawSql('followers - 1'))->where('user_id', $targetId)->update();
            $this->userModel->set('followings', new RawSql('followings - 1'))->where('user_id', $currentId)->update();

            $this->notificationModel->where('from_user_id', $currentId)
                ->where('to_user_id', $targetId)
                ->where('type', 'follow')
                ->delete();

            session()->setFlashdata('msg', "Berhasil unfollow {$targetUsername}");

        } else {
            $followingsTable->insert(['follower_id' => $currentId, 'followed_id' => $targetId]);

            $this->userModel->set('followers', new RawSql('followers + 1'))->where('user_id', $targetId)->update();
            $this->userModel->set('followings', new RawSql('followings + 1'))->where('user_id', $currentId)->update();

            if ($currentId != $targetId) {
                $this->notificationModel->save([
                    'to_user_id' => $targetId,
                    'from_user_id' => $currentId,
                    'type' => 'follow',
                    'post_id' => null,
                    'message' => ''
                ]);
            }

            session()->setFlashdata('msg', "Berhasil follow {$targetUsername}");
        }

        return redirect()->back();
    }

    public function edit()
    {
        $currentId = session()->get('id');
        $currentUsername = session()->get('username');

        $user = $this->userModel->find($currentId);

        $data = [
            'user' => $user,
            'currentUsername' => $currentUsername
        ];

        return view('profile/edit_profile', $data);
    }

    public function updateProfile()
    {
        $currentId = session()->get('id');
        $session = session();
        $oldUser = $this->userModel->find($currentId);

        $rules = [
            'username' => 'required|min_length[3]|max_length[25]',
            'name' => 'required|max_length[25]',
            'password' => 'permit_empty|min_length[8]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', $this->validator->listErrors());
            return redirect()->back()->withInput();
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'profile_name' => $this->request->getPost('name'),
            'bio' => $this->request->getPost('bio')
        ];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/photos', $newName);
            $data['profile_picture'] = 'photos/' . $newName;
        }

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            $data['password'] = $oldUser['password'];
        }

        $this->userModel->update($currentId, $data);

        $session->set([
            'username' => $data['username'],
            'profile_name' => $data['profile_name']
        ]);

        if (isset($data['profile_picture'])) {
            $session->set('profile_picture', $data['profile_picture']);
        }

        $session->setFlashdata('msg', 'Profil berhasil diperbarui.');
        return redirect()->to(site_url("profile/edit"));
    }
}