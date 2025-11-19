<?php namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    // =======================================================
    // Method Before: Dijalankan sebelum Controller dieksekusi
    // =======================================================
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pengecekan status login
        if (!session()->get('isLoggedIn')) {
            // Jika user belum login (session tidak ada), arahkan ke halaman Login utama
            // Set pesan flashdata agar user tahu mengapa mereka dialihkan
            session()->setFlashdata('msg', 'Anda harus login untuk mengakses halaman ini.');
            return redirect()->to(site_url('/'));
        }
    }

    // =======================================================
    // Method After: Dijalankan setelah Controller selesai
    // =======================================================
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada yang dilakukan setelah eksekusi Controller untuk filter ini
    }
}