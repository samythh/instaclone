<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

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
            if (password_verify($password, $user['password'])) {
                $sessData = [
                    'id' => $user['user_id'],
                    'username' => $user['username'],
                    'profile_name' => $user['profile_name'],
                    'profile_picture' => $user['profile_picture'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($sessData);

                return redirect()->to(site_url('feed/' . $user['username']));
            } else {
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to(site_url('/'));
            }
        } else {
            $session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to(site_url('/'));
        }
    }

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
            'profile_picture' => $this->handleFileUpload()
        ];

        $this->userModel->insert($data);
        $session->setFlashdata('msg', 'Akun berhasil dibuat. Silakan masuk.');
        return redirect()->to(site_url('/'));
    }

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
}