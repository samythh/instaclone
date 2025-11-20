<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

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

    public function index($type = 'default', $target = null)
    {
        $currentId = session()->get('id');
        $currentUsername = session()->get('username');

        if (!$currentId) {
            return redirect()->to(site_url('/'));
        }

        $results = [];
        $title = "Explore People";

        if ($this->request->getPost('search_for')) {
            $type = 'search';
            $target = $this->request->getPost('search_for');
        }

        $targetId = null;
        if ($target && $type !== 'search') {
            $user = $this->userModel->where('username', $target)->first();
            if ($user) {
                $targetId = $user['id'];
            }
        }

        switch ($type) {
            case 'followers':
                if ($targetId) {
                    $results = $this->getFollowers($targetId, $currentId);
                    $title = "Followers of " . esc($target);
                }
                break;

            case 'followings':
                if ($targetId) {
                    $results = $this->getFollowings($targetId, $currentId);
                    $title = "Following by " . esc($target);
                }
                break;

            case 'search':
                $results = $this->searchUsers($target, $currentId);
                $title = "Search Results for '" . esc($target) . "'";
                break;

            case 'default':
            default:
                $results = $this->searchUsers('', $currentId);
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

    private function getFollowers($targetId, $currentId)
    {
        return $this->db->table('followings f')
            ->select('u.username AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE follower_id = ' . $this->db->escape($currentId) . ' AND followed_id = u.id) AS isFollowing')
            ->join('users u', 'u.id = f.follower_id', 'inner')
            ->where('f.followed_id', $targetId)
            ->where('u.id !=', $currentId)
            ->get()
            ->getResultArray();
    }

    private function getFollowings($targetId, $currentId)
    {
        return $this->db->table('followings f')
            ->select('u.username AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE follower_id = ' . $this->db->escape($currentId) . ' AND followed_id = u.id) AS isFollowing')
            ->join('users u', 'u.id = f.followed_id', 'inner')
            ->where('f.follower_id', $targetId)
            ->where('u.id !=', $currentId)
            ->get()
            ->getResultArray();
    }

    private function searchUsers($searchTerm, $currentId)
    {
        $builder = $this->db->table('users u')
            ->select('u.username AS usernamee, u.profile_name, u.profile_picture, 
                      (SELECT 1 FROM followings WHERE follower_id = ' . $this->db->escape($currentId) . ' AND followed_id = u.id) AS isFollowing');

        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('u.username', $searchTerm, 'both')
                ->orLike('u.profile_name', $searchTerm, 'both')
                ->groupEnd();
        }

        $builder->where('u.id !=', $currentId);

        $builder->limit(20);

        return $builder->get()->getResultArray();
    }

    public function search()
    {
        $searchTerm = $this->request->getPost('search_for');
        return $this->index('search', $searchTerm);
    }
}