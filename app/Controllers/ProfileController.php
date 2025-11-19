<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\NotificationModel; // TAMBAHAN BARU
use CodeIgniter\Controller;
use CodeIgniter\Database\RawSql;

class ProfileController extends Controller
{
    protected $userModel;
    protected $postModel;
    protected $notificationModel; // TAMBAHAN BARU
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->notificationModel = new NotificationModel(); // TAMBAHAN BARU
        $this->db = \Config\Database::connect();
        helper(['url', 'form']);
    }

    // =======================================================
    // 1. TAMPILAN PROFILE
    // =======================================================
    public function index($profileUsername)
    {
        $currentUsername = session()->get('username');

        $profileUser = $this->userModel->find($profileUsername);

        if (!$profileUser) {
            return redirect()->to(site_url("feed/{$currentUsername}"))->with('error', 'Profil tidak ditemukan.');
        }

        $isFollowing = $this->db->table('followings')
            ->where('username', $currentUsername)
            ->where('following', $profileUsername)
            ->countAllResults() > 0;

        $posts = $this->postModel->where('username', $profileUsername)
            ->orderBy('time_stamp', 'DESC')
            ->findAll();

        $data = [
            'profileUser' => $profileUser,
            'currentUsername' => $currentUsername,
            'isFollowing' => $isFollowing,
            'posts' => $posts,
            'isOwner' => ($currentUsername === $profileUsername)
        ];

        return view('profile/profile_index', $data);
    }

    // =======================================================
    // 2. FOLLOW / UNFOLLOW (DENGAN NOTIFIKASI)
    // =======================================================
    public function toggleFollow($targetUsername)
    {
        $follower = session()->get('username');
        $followingsTable = $this->db->table('followings');

        $isFollowing = $followingsTable->where('username', $follower)
            ->where('following', $targetUsername)
            ->countAllResults() > 0;

        if ($isFollowing) {
            // --- UNFOLLOW ---
            $followingsTable->where('username', $follower)->where('following', $targetUsername)->delete();

            $this->userModel->set('followers', new RawSql('followers - 1'))->where('username', $targetUsername)->update();
            $this->userModel->set('followings', new RawSql('followings - 1'))->where('username', $follower)->update();

            // Hapus notifikasi follow sebelumnya
            $this->notificationModel->where('from_username', $follower)
                ->where('to_username', $targetUsername)
                ->where('type', 'follow')
                ->delete();

            session()->setFlashdata('msg', "Berhasil unfollow {$targetUsername}");

        } else {
            // --- FOLLOW ---
            $followingsTable->insert(['username' => $follower, 'following' => $targetUsername]);

            $this->userModel->set('followers', new RawSql('followers + 1'))->where('username', $targetUsername)->update();
            $this->userModel->set('followings', new RawSql('followings + 1'))->where('username', $follower)->update();

            // KIRIM NOTIFIKASI FOLLOW
            if ($follower !== $targetUsername) {
                $this->notificationModel->save([
                    'to_username' => $targetUsername,
                    'from_username' => $follower,
                    'type' => 'follow',
                    'post_id' => null, // Follow tidak terkait post
                    'message' => ''
                ]);
            }

            session()->setFlashdata('msg', "Berhasil follow {$targetUsername}");
        }

        return redirect()->back();
    }

    // =======================================================
    // 3. EDIT PROFILE
    // =======================================================
    public function edit()
    {
        $currentUsername = session()->get('username');
        $user = $this->userModel->find($currentUsername);

        $data = ['user' => $user, 'currentUsername' => $currentUsername];
        return view('profile/edit_profile', $data);
    }

    // =======================================================
    // 4. UPDATE PROFILE
    // =======================================================
    public function updateProfile()
    {
        $currentUsername = session()->get('username');
        $session = session();
        $oldUser = $this->userModel->find($currentUsername);

        $rules = [
            'username' => 'required|min_length[3]|max_length[25]',
            'email' => 'required|valid_email',
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
            'email' => $this->request->getPost('email'),
            'bio' => $this->request->getPost('bio')
        ];

        // Handle Foto Profil Baru (Opsional)
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

        $this->userModel->update($currentUsername, $data);

        $session->set([
            'username' => $data['username'],
            'profile_name' => $data['profile_name']
        ]);

        $session->setFlashdata('msg', 'Profil berhasil diperbarui.');
        return redirect()->to(site_url("profile/edit"));
    }
}