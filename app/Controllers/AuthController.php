<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use League\OAuth2\Client\Provider\Facebook; // Pastikan library ini sudah terinstall via Composer

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // --------------------------------------------------------------------
    // HALAMAN LOGIN & PROSES LOGIN MANUAL
    // --------------------------------------------------------------------

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('feed/' . session()->get('username')));
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user) {
            // Verifikasi Password (jika user punya password)
            // User dari Facebook mungkin passwordnya NULL, jadi kita cek dulu
            if (!empty($user['password']) && password_verify($password, $user['password'])) {
                $this->setUserSession($user);
                return redirect()->to(site_url('feed/' . $user['username']));
            } else {
                $session->setFlashdata('msg', 'Password salah atau akun ini login via Facebook.');
                return redirect()->to(site_url('/'));
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to(site_url('/'));
        }
    }

    // --------------------------------------------------------------------
    // PROSES REGISTRASI MANUAL
    // --------------------------------------------------------------------

    public function register()
    {
        return view('auth/register');
    }

    public function store()
    {
        $session = session();
        $rules = [
            'username' => 'required|min_length[3]|max_length[25]|is_unique[users.username]',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'profilename' => 'required|max_length[25]',
        ];

        if (!$this->validate($rules)) {
            return view('auth/register', ['validation' => $this->validator]);
        }

        $data = [
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'profile_name' => $this->request->getVar('profilename'),
            'email' => $this->request->getVar('email'),
            'bio' => $this->request->getVar('bio') ?? '',
            'profile_picture' => $this->handleFileUpload(),
            'facebook_id' => null // Default null untuk register manual
        ];

        $this->userModel->insert($data);
        $session->setFlashdata('msg', 'Akun berhasil dibuat. Silakan masuk.');
        return redirect()->to(site_url('/'));
    }

    // --------------------------------------------------------------------
    // FITUR LOGIN FACEBOOK (BARU DITAMBAHKAN)
    // --------------------------------------------------------------------

    public function facebookLogin()
    {
        // 1. Konfigurasi Provider (Ganti dengan App ID & Secret Anda!)
        $provider = new Facebook([
            'clientId' => 'MASUKKAN_APP_ID_FACEBOOK_DISINI',
            'clientSecret' => 'MASUKKAN_APP_SECRET_FACEBOOK_DISINI',
            'redirectUri' => site_url('facebook/callback'),
            'graphApiVersion' => 'v17.0',
        ]);

        // 2. Dapatkan URL Login
        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email', 'public_profile']
        ]);

        // 3. Simpan state untuk keamanan CSRF
        session()->set('oauth2state', $provider->getState());

        return redirect()->to($authUrl);
    }

    public function facebookCallback()
    {
        $session = session();

        $provider = new Facebook([
            'clientId' => 'APP_ID_ANDA',      // Pastikan ini benar
            'clientSecret' => 'APP_SECRET_ANDA',  // Pastikan ini benar
            'redirectUri' => site_url('facebook/callback'),
            'graphApiVersion' => 'v17.0',
        ]);

        try {
            // Langsung tukar kode dengan token
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->request->getVar('code')
            ]);

            // Ambil data user
            $fbUser = $provider->getResourceOwner($token);
            $fbId = $fbUser->getId();
            $fbEmail = $fbUser->getEmail();
            $fbName = $fbUser->getName();

            // --- LOGIKA DATABASE (SAMA SEPERTI SEBELUMNYA) ---
            $user = $this->userModel->where('facebook_id', $fbId)->first();

            if ($user) {
                // User lama: Login
                $this->setUserSession($user);
                return redirect()->to(site_url('feed/' . $user['username']));
            } else {
                // Cek email manual
                $existingUser = $this->userModel->where('email', $fbEmail)->first();
                if ($existingUser) {
                    // Hubungkan akun
                    $this->userModel->update($existingUser['user_id'], ['facebook_id' => $fbId]);
                    $user = $this->userModel->find($existingUser['user_id']);
                } else {
                    // User baru
                    $cleanName = strtolower(str_replace(' ', '', $fbName));
                    $newUsername = substr($cleanName, 0, 15) . rand(100, 999);

                    $newData = [
                        'username' => $newUsername,
                        'email' => $fbEmail,
                        'profile_name' => $fbName,
                        'facebook_id' => $fbId,
                        'password' => null,
                        'profile_picture' => 'images/avatar.svg',
                        'bio' => 'Bergabung via Facebook'
                    ];
                    $this->userModel->insert($newData);
                    $user = $this->userModel->where('facebook_id', $fbId)->first();
                }
                $this->setUserSession($user);
                return redirect()->to(site_url('feed/' . $user['username']));
            }

        } catch (\Exception $e) {
            // Tampilkan error asli untuk debugging
            return redirect()->to(site_url('/'))->with('msg', 'Error Facebook: ' . $e->getMessage());
        }
    }

    // --------------------------------------------------------------------
    // FUNGSI UMUM (HELPER)
    // --------------------------------------------------------------------

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/'));
    }

    private function handleFileUpload()
    {
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/photos', $newName);
            return 'photos/' . $newName;
        }
        return 'images/avatar.svg';
    }

    // Fungsi kecil untuk set session agar tidak menulis ulang kode
    private function setUserSession($user)
    {
        $data = [
            'id' => $user['user_id'],
            'username' => $user['username'],
            'profile_name' => $user['profile_name'],
            'profile_picture' => $user['profile_picture'],
            'isLoggedIn' => TRUE
        ];
        session()->set($data);
    }
}