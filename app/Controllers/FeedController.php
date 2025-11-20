<?php
namespace App\Controllers;

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

    public function handleEmptyFeed()
    {
        $username = session()->get('username');
        if ($username) {
            return redirect()->to(site_url('feed/' . $username));
        }
        return redirect()->to(site_url('/'));
    }

    public function index($urlUsername)
    {
        $currentId = session()->get('id');
        $currentUsername = session()->get('username');

        if (!$currentId) {
            return redirect()->to(site_url('/'));
        }

        $db = \Config\Database::connect();
        $builder = $db->table('posts p');

        $builder->select('
            u.username AS follower,
            u.profile_picture AS following_dp,
            p.post_id AS post_id,
            p.photo AS photo, 
            p.likes,
            p.comments,
            DATEDIFF(NOW(), p.time_stamp) AS time_stamp,
            (
                SELECT 1
                FROM likes
                WHERE likes.post_id = p.post_id AND likes.user_id = ' . $db->escape($currentId) . '
            ) AS is_liked
        ');

        $builder->join('users u', 'u.id = p.user_id', 'inner');

        $builder->groupStart();
        $builder->where('p.user_id', $currentId);
        $builder->orWhere("p.user_id IN (SELECT followed_id FROM followings WHERE follower_id = " . $db->escape($currentId) . ")");
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