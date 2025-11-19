<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    // Konstruktor untuk memuat Model
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Memuat helper 'url' dan 'form' yang sering digunakan
        helper(['form', 'url']);
    }

    // =======================================================
    // 1. TAMPILAN LOGIN (Menggantikan index.php/login.php HTML)
    // =======================================================
    public function index()
    {
        // Jika user sudah login, arahkan langsung ke halaman feed
        if(session()->get('isLoggedIn')){
            return redirect()->to(site_url('feed/' . session()->get('username')));
        }
        
        // Memuat tampilan login
        return view('auth/login');
    }

    // =======================================================
    // 2. PROSES LOGIN (Menggantikan login.php logic)
    // =======================================================
    public function login()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Cari user di database berdasarkan username
        $user = $this->userModel->find($username);

        // Jika user ditemukan
        if($user){
            // Verifikasi Password menggunakan password_verify() (kunci keamanan)
            if(password_verify($password, $user['password'])){
                // Password cocok, buat session
                $sessData = [
                    'username' => $user['username'],
                    'profile_name' => $user['profile_name'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($sessData);
                
                // Redirect ke halaman feed
                return redirect()->to(site_url('feed/' . $user['username']));
            } else {
                // Password salah
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to(site_url('/'));
            }
        } else {
            // Username tidak ditemukan
            $session->setFlashdata('msg', 'Username tidak ditemukan.');
            return redirect()->to(site_url('/'));
        }
    }

    // =======================================================
    // 3. TAMPILAN REGISTRASI (Menggantikan registration.php HTML)
    // =======================================================
    public function register()
    {
        // Memuat tampilan registrasi
        return view('auth/register');
    }

    // =======================================================
    // 4. PROSES REGISTRASI (Menggantikan create_account.php logic)
    // =======================================================
    public function store()
    {
        $session = session();

        // 1. Definisikan Aturan Validasi (Pembersihan dan Pengecekan)
        $rules = [
            'username'      => 'required|min_length[3]|max_length[25]|is_unique[users.username]',
            'email'         => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]|max_length[255]',
            'profilename'   => 'required|max_length[25]',
        ];

        // Jalankan Validasi
        if (!$this->validate($rules)) {
            // Jika validasi gagal, kembalikan ke form dengan pesan error
            return view('auth/register', ['validation' => $this->validator]);
        }
        
        // 2. Siapkan Data Pendaftaran
        $data = [
            'username'          => $this->request->getVar('username'),
            // HASHING PASSWORD: KRITIS UNTUK KEAMANAN
            'password'          => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT), 
            'profile_name'      => $this->request->getVar('profilename'),
            'email'             => $this->request->getVar('email'),
            'bio'               => $this->request->getVar('bio') ?? '',
            'profile_picture'   => $this->handleFileUpload() // Memanggil fungsi upload
        ];

        // 3. Simpan ke Database
        $this->userModel->insert($data);

        // Beri pesan sukses dan arahkan pengguna ke halaman login
        $session->setFlashdata('msg', 'Akun berhasil dibuat. Silakan masuk.');
        return redirect()->to(site_url('/'));
    }

    // =======================================================
    // 5. LOGOUT
    // =======================================================
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/'));
    }

    // =======================================================
    // FUNGSI HELPER: Menangani File Upload (Foto Profil)
    // =======================================================
    private function handleFileUpload()
    {
        $file = $this->request->getFile('image'); // 'image' adalah nama input file di form registration.php
        
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            // CI membuat nama file unik yang aman
            $newName = $file->getRandomName(); 
            // Pindahkan file ke folder public/photos
            $file->move(ROOTPATH . 'public/photos', $newName);
            
            // Mengembalikan path relatif yang akan disimpan di database
            return 'photos/' . $newName; 
        }
        // Jika tidak ada file diunggah atau gagal, kembalikan default avatar
        return 'images/avatar.svg'; 
    }
}