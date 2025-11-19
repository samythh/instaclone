<?php
namespace App\Controllers;

// File: app/Controllers/AuthController.php

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
    // 1. TAMPILAN LOGIN
    // =======================================================
    public function index()
    {
        // Jika user sudah login, arahkan langsung ke halaman feed
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('feed/' . session()->get('username')));
        }

        // Memuat tampilan login
        return view('auth/login');
    }

    // =======================================================
    // 2. PROSES LOGIN (DIPERBARUI DENGAN LOGIKA MIGRASI)
    // =======================================================
    public function login()
    {
        $session = session();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password'); // Password yang diketik user

        // 1. Cari user di database berdasarkan username
        $user = $this->userModel->find($username);

        // Jika user ditemukan
        if ($user) {
            $dbPassword = $user['password']; // Password dari database
            $isAuthenticated = false;
            $needsRehash = false;

            // --- SKENARIO A: Login Normal (User Baru / Sudah Update) ---
            // Cek apakah password di DB cocok dengan hash
            if (password_verify($password, $dbPassword)) {
                $isAuthenticated = true;

                // Cek apakah algoritma hash perlu diperbarui (untuk keamanan masa depan)
                if (password_needs_rehash($dbPassword, PASSWORD_DEFAULT)) {
                    $needsRehash = true;
                }
            }
            // --- SKENARIO B: Login Migrasi (User Lama / Data Manual) ---
            // Cek apakah password di DB cocok dengan teks biasa
            // HANYA GUNAKAN INI JIKA DATA LAMA ANDA BELUM DI-HASH
            else if ($password === $dbPassword) {
                $isAuthenticated = true;
                $needsRehash = true; // Wajib di-hash agar login berikutnya aman
            }

            // 2. Jika Autentikasi Berhasil
            if ($isAuthenticated) {

                // SELF-HEALING: Update password ke format Hash yang aman jika diperlukan
                if ($needsRehash) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    // Update hanya kolom password untuk user ini
                    $this->userModel->update($user['username'], ['password' => $newHash]);
                }

                // Buat Session Data
                $sessData = [
                    'username' => $user['username'],
                    'profile_name' => $user['profile_name'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($sessData);

                // Redirect ke halaman feed user
                return redirect()->to(site_url('feed/' . $user['username']));

            } else {
                // Password salah (tidak cocok hash maupun plain text)
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
    // 3. TAMPILAN REGISTRASI
    // =======================================================
    public function register()
    {
        return view('auth/register');
    }

    // =======================================================
    // 4. PROSES REGISTRASI (DENGAN HASHING)
    // =======================================================
    public function store()
    {
        $session = session();

        // Validasi Input
        $rules = [
            'username' => 'required|min_length[3]|max_length[25]|is_unique[users.username]',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[255]',
            'profilename' => 'required|max_length[25]',
        ];

        if (!$this->validate($rules)) {
            return view('auth/register', ['validation' => $this->validator]);
        }

        // Siapkan Data
        $data = [
            'username' => $this->request->getVar('username'),
            // PENTING: Password selalu di-hash saat pendaftaran baru
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'profile_name' => $this->request->getVar('profilename'),
            'email' => $this->request->getVar('email'),
            'bio' => $this->request->getVar('bio') ?? '',
            'profile_picture' => $this->handleFileUpload()
        ];

        // Simpan
        $this->userModel->insert($data);

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
    // FUNGSI HELPER: Upload Gambar
    // =======================================================
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