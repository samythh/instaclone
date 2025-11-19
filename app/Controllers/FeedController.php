<?php namespace App\Controllers;

use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\RawSql; // Digunakan untuk query khusus

class FeedController extends Controller
{
    protected $postModel;
    protected $userModel;

    public function __construct()
    {
        // Memuat Model yang diperlukan
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
        helper(['url']);
    }

    // =======================================================
    // 1. TAMPILAN FEED UTAMA (Menggantikan feed.php logic)
    // =======================================================
    // Parameter $currentUsername didapat dari URL Segment (lihat Routes.php)
    public function index($currentUsername)
    {
        // Pastikan user sudah login (Opsional jika Filter sudah diterapkan)
        if (session()->get('username') !== $currentUsername || !session()->get('isLoggedIn')) {
             return redirect()->to(site_url('/'));
        }

        // --- Kunci Migrasi Query Kompleks dari feed.php ---
        // Query ini mereplikasi logika: "Ambil post terbaru dari SEMUA user yang diikuti oleh $currentUsername"
        
        $db = \Config\Database::connect();
        $feedData = $db->table('followings f')
            ->select('
                f.following AS follower,
                u.profile_picture AS following_dp,
                p.post_id AS post_id,
                p.photo AS photo, 
                p.likes,
                p.comments,
                DATEDIFF(NOW(), p.time_stamp) AS time_stamp,
                (
                    SELECT 1
                    FROM likes
                    WHERE likes.post_id = p.post_id AND likes.likername = f.username
                ) AS is_liked
            ')
            ->join('users u', 'u.username = f.following', 'inner')
            // Query untuk mendapatkan HANYA post terbaru dari setiap following
            ->join('posts p', new RawSql('p.post_id = (SELECT posts.post_id FROM posts WHERE posts.username = f.following ORDER BY posts.time_stamp DESC LIMIT 1)'), 'inner')
            ->where('f.username', $currentUsername)
            ->groupBy('f.following')
            ->get()
            ->getResultArray();

        // Data yang dikirim ke View
        $data = [
            'feedData' => $feedData,
            'currentUsername' => $currentUsername
        ];

        // Memuat View Feed
        return view('feed/feed_index', $data);
    }
    
    // ... Fungsi lain seperti Explore dan Post akan ditambahkan di Controller lain ...
}