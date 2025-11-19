<?php
namespace App\Controllers;
// File: app/Controllers/FeedController.php

use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class FeedController extends Controller
{
    protected $postModel;
    protected $userModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
        helper(['url', 'date']);
    }

    // --- TAMBAHKAN FUNGSI INI ---
    public function handleEmptyFeed()
    {
        $username = session()->get('username');

        // Jika user login, arahkan ke feed miliknya yang benar
        if ($username) {
            return redirect()->to(site_url('feed/' . $username));
        }

        // Jika tidak login, kembalikan ke halaman login
        return redirect()->to(site_url('/'));
    }
    // -----------------------------

    public function index($currentUsername)
    {
        // ... (kode index yang sudah ada biarkan saja) ...
        // Cek sesi login
        if (session()->get('username') !== $currentUsername || !session()->get('isLoggedIn')) {
            return redirect()->to(site_url('/'));
        }

        $db = \Config\Database::connect();

        // ... (Query builder Anda yang sudah benar) ...
        $builder = $db->table('posts p');
        $builder->select('
            p.username AS follower,
            u.profile_picture AS following_dp,
            p.post_id AS post_id,
            p.photo AS photo, 
            p.likes,
            p.comments,
            DATEDIFF(NOW(), p.time_stamp) AS time_stamp,
            (
                SELECT 1
                FROM likes
                WHERE likes.post_id = p.post_id AND likes.likername = ' . $db->escape($currentUsername) . '
            ) AS is_liked
        ');
        $builder->join('users u', 'u.username = p.username', 'inner');
        $builder->groupStart();
        $builder->where('p.username', $currentUsername);
        $builder->orWhere("p.username IN (SELECT following FROM followings WHERE username = " . $db->escape($currentUsername) . ")");
        $builder->groupEnd();
        $builder->orderBy('p.time_stamp', 'DESC');

        $feedData = $builder->get()->getResultArray();

        $data = [
            'feedData' => $feedData,
            'currentUsername' => $currentUsername
        ];

        return view('feed/feed_index', $data);
    }
}