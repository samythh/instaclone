<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

// Controller ini menangani logika pencarian dan daftar follow/follower.
class ExploreController extends Controller
{
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
        helper(['url', 'form']);
    }

    // =======================================================
    // 1. TAMPILAN EXPLORE / SEARCH (Menggantikan explore.php)
    // =======================================================
    // $type: 'search', 'followers', atau 'followings'
    // $target: Username yang dicari atau username pemilik list (e.g., 'jane_doe')
    public function index($type = 'default', $target = null)
    {
        $currentUsername = session()->get('username');

        if (!$currentUsername) {
            return redirect()->to(site_url('/'));
        }

        $results = [];
        $title = "Explore People";
        
        // Cek jika ada input dari form pencarian (jika tidak dari URL segment)
        if ($this->request->getPost('search_for')) {
            $type = 'search';
            $target = $this->request->getPost('search_for');
        }

        // --- Kunci Migrasi Query dari explore.php ---
        switch ($type) {
            case 'followers':
                $results = $this->getFollowers($target, $currentUsername);
                $title = "Followers of " . esc($target);
                break;
            
            case 'followings':
                $results = $this->getFollowings($target, $currentUsername);
                $title = "Following by " . esc($target);
                break;

            case 'search':
                $results = $this->searchUsers($target, $currentUsername);
                $title = "Search Results for '" . esc($target) . "'";
                break;
            
            case 'default':
                // Untuk halaman explore default, tampilkan semua user kecuali diri sendiri
                $results = $this->searchUsers('', $currentUsername);
                $title = "People to Follow";
                break;
        }

        $data = [
            'currentUsername' => $currentUsername,
            'title' => $title,
            'results' => $results
        ];

        return view('explore/explore_index', $data);
    }
    
    // =======================================================
    // FUNGSI HELPER: Mengambil Daftar Followers
    // =======================================================
    private function getFollowers($target, $currentUsername)
    {
        // Query mereplikasi: SELECT followings.username AS usernamee, ... WHERE followings.following = '$target'
        return $this->db->table('followings f')
            ->select('f.username AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE username = ' . $this->db->escape($currentUsername) . ' AND following = f.username) AS isFollowing')
            ->join('users u', 'u.username = f.username', 'inner')
            ->where('f.following', $target)
            ->where('f.username !=', $currentUsername) // Jangan tampilkan diri sendiri
            ->get()
            ->getResultArray();
    }
    
    // =======================================================
    // FUNGSI HELPER: Mengambil Daftar Followings
    // =======================================================
    private function getFollowings($target, $currentUsername)
    {
        // Query mereplikasi: SELECT followings.following AS usernamee, ... WHERE followings.username = '$target'
        return $this->db->table('followings f')
            ->select('f.following AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE username = ' . $this->db->escape($currentUsername) . ' AND following = f.following) AS isFollowing')
            ->join('users u', 'u.username = f.following', 'inner')
            ->where('f.username', $target)
            ->where('f.following !=', $currentUsername) // Jangan tampilkan diri sendiri
            ->get()
            ->getResultArray();
    }
    
    // =======================================================
    // FUNGSI HELPER: Pencarian Umum
    // =======================================================
    private function searchUsers($searchTerm, $currentUsername)
    {
        // Query mereplikasi: WHERE users.username LIKE '%$searchTerm%' OR users.profile_name LIKE '%$searchTerm%'
        $builder = $this->db->table('users u')
            ->select('u.username AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE username = ' . $this->db->escape($currentUsername) . ' AND following = u.username) AS isFollowing');
            
        // Logika pencarian: cari di username atau profile_name
        if (!empty($searchTerm)) {
            $builder->like('u.username', $searchTerm, 'both')
                    ->orLike('u.profile_name', $searchTerm, 'both');
        }
        
        // Kecualikan user yang sedang login dari hasil
        $builder->where('u.username !=', $currentUsername);

        return $builder->get()->getResultArray();
    }

    // =======================================================
    // FUNGSI SEARCH DARI FORM NAVIGASI FEED
    // =======================================================
    // Fungsi ini dipanggil dari form di FeedController.php
    public function search()
    {
        $searchTerm = $this->request->getPost('search_for');
        return $this->index('search', $searchTerm);
    }
}