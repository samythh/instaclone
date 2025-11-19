<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PostModel; 
use CodeIgniter\Controller;
use CodeIgniter\Database\RawSql; 

class ProfileController extends Controller
{
    protected $userModel;
    protected $postModel;
    protected $db; // Untuk mengakses Query Builder/Raw SQL

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->postModel = new PostModel();
        $this->db = \Config\Database::connect(); // Menginisialisasi database connection
        helper(['url', 'form']);
    }

    // =======================================================
    // 1. TAMPILAN PROFILE (Menggantikan profile.php)
    // =======================================================
    // $profileUsername: user yang profilnya sedang dilihat (e.g., 'jane_doe')
    public function index($profileUsername)
    {
        $currentUsername = session()->get('username');
        
        // 1. Ambil data user yang dilihat
        $profileUser = $this->userModel->find($profileUsername);

        if (!$profileUser) {
            return redirect()->to(site_url("feed/{$currentUsername}"))->with('error', 'Profil tidak ditemukan.');
        }

        // 2. Cek status follow (Logika profile.php - is_follower)
        $isFollowing = $this->db->table('followings')
                                ->where('username', $currentUsername)
                                ->where('following', $profileUsername)
                                ->countAllResults() > 0; // Menggunakan countAllResults untuk efisiensi

        // 3. Ambil semua postingan user
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
    // 2. FOLLOW / UNFOLLOW (Menggantikan follow.php dan unfollow.php)
    // =======================================================
    // $targetUsername: user yang akan di-follow/unfollow
    public function toggleFollow($targetUsername)
    {
        $follower = session()->get('username'); // User yang melakukan aksi
        $followingsTable = $this->db->table('followings');
        
        // Cek status follow saat ini
        $isFollowing = $followingsTable->where('username', $follower)
                                       ->where('following', $targetUsername)
                                       ->countAllResults() > 0;

        if ($isFollowing) {
            // UNFOLLOW (Logika unfollow.php - DELETE)
            $followingsTable->where('username', $follower)
                            ->where('following', $targetUsername)
                            ->delete();

            // Decrement follower counter di user target
            $this->userModel->set('followers', new RawSql('followers - 1'))
                            ->where('username', $targetUsername)
                            ->update();

            // Decrement following counter di user yang melakukan aksi
            $this->userModel->set('followings', new RawSql('followings - 1'))
                            ->where('username', $follower)
                            ->update();
            
            session()->setFlashdata('msg', "Berhasil unfollow {$targetUsername}");

        } else {
            // FOLLOW (Logika follow.php - INSERT)
            $followingsTable->insert(['username' => $follower, 'following' => $targetUsername]);

            // Increment follower counter di user target
            $this->userModel->set('followers', new RawSql('followers + 1'))
                            ->where('username', $targetUsername)
                            ->update();
            
            // Increment following counter di user yang melakukan aksi
            $this->userModel->set('followings', new RawSql('followings + 1'))
                            ->where('username', $follower)
                            ->update();

            session()->setFlashdata('msg', "Berhasil follow {$targetUsername}");
        }

        // Redirect kembali ke halaman profil yang sedang dilihat
        return redirect()->back(); 
    }

    // =======================================================
    // 3. TAMPILAN EDIT PROFILE (Menggantikan edit-profile.php)
    // =======================================================
    public function edit()
    {
        $currentUsername = session()->get('username');
        
        $user = $this->userModel->find($currentUsername);
        
        $data = [
            'user' => $user,
            'currentUsername' => $currentUsername
        ];

        return view('profile/edit_profile', $data);
    }

    // =======================================================
    // 4. SUBMIT EDIT PROFILE (Implementasi Hashing Aman)
    // =======================================================
    public function updateProfile()
    {
        $currentUsername = session()->get('username');
        $session = session();

        // 1. Ambil data user lama untuk mendapatkan hash password yang tersimpan
        $oldUser = $this->userModel->find($currentUsername);
        
        // 2. Definisikan Aturan Validasi (Password diizinkan kosong)
        $rules = [
            'username' => 'required|min_length[3]|max_length[25]', 
            'email'    => 'required|valid_email',
            'name'     => 'required|max_length[25]',
            'password' => 'permit_empty|min_length[8]|max_length[255]', // Password diizinkan kosong
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', $this->validator->listErrors());
            return redirect()->back()->withInput();
        }

        // 3. Siapkan Data Update
        $data = [
            'username'     => $this->request->getPost('username'),
            'profile_name' => $this->request->getPost('name'),
            'email'        => $this->request->getPost('email'),
            'bio'          => $this->request->getPost('bio')
        ];
        
        $newPassword = $this->request->getPost('password');

        // 4. LOGIKA KRITIS: Amankan Password
        if (!empty($newPassword)) {
            // Jika user mengisi field password, hash password baru tersebut
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            // Jika field password kosong, gunakan password hash lama
            $data['password'] = $oldUser['password'];
        }
        
        // 5. Lakukan Update
        // Note: CodeIgniter menangani perubahan Primary Key (username) jika Anda menggunakan update($oldId, $newData)
        $this->userModel->update($currentUsername, $data); 

        // Update session jika username atau profile name diubah
        $session->set([
            'username' => $data['username'],
            'profile_name' => $data['profile_name']
        ]);

        $session->setFlashdata('msg', 'Profil berhasil diperbarui.');
        return redirect()->to(site_url("profile/edit"));
    }
}