<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Feed | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head> 
    <body>
        <nav class="navigation">
            <a href="<?= site_url('feed/' . $currentUsername) ?>">
                <img 
                    src="<?= base_url('images/navLogo.png') ?>"
                    alt="logo"
                    title="logo"
                    class="navigation__logo"
                />
            </a>
            <form action="<?= site_url('explore/search') ?>" class="navigation__search-container" method="post">
                <div class="navigation__search-container">
                    <i class="fa fa-search"></i>
                    <input type="text" name="search_for" placeholder="Search">
                    <input type="submit" id="search" name="search" value="Search">
                </div>
            </form>
            <div class="navigation__icons">
                <a href="<?= site_url('explore') ?>" class="navigation__link">
                    <i class="fa fa-compass"></i>
                </a>
                <a href="<?= site_url('post/create') ?>" class="navigation__link">
                    <i class="fa fa-plus-square-o"></i> 
                </a>
                <a href="<?= site_url('profile/' . $currentUsername) ?>" class="navigation__link">
                    <i class="fa fa-user-o"></i>
                </a>
            </div>
        </nav>
        
        <main class="feed">
        <?php foreach ($feedData as $row): 
            $follower = $row['follower'];
            $following_dp = $row['following_dp'];
            $post_id = $row['post_id'];
            $likes = $row['likes'];
            $is_liked = $row['is_liked'];
            $comments_count = $row['comments'];
            $created_at = $row['time_stamp'];
        ?>
            <section class="photo">
                <header class="photo__header">
                    <div class="photo__header-column">
                        <img
                            class="photo__avatar"
                            src="<?= base_url(empty($following_dp) ? 'images/avatar.svg' : $following_dp) ?>"
                            style="width:30px;height:30px"
                        />
                    </div>
                    <div class="photo__header-column">
                        <a href="<?= site_url('profile/' . $currentUsername . '/' . $follower) ?>">
                            <?= esc($follower) ?>
                            <img height="13" width="13" src="<?= base_url('images/verified.png') ?>" />
                        </a>
                    </div>
                </header>
                <div class="photo__file-container">
                    <a href="<?= site_url('post/detail/' . $post_id) ?>"> 
                        <img class="photo__file" src="<?= base_url($row['photo']) ?>" >
                    </a>
                </div>
                <div class="photo__info">
                    <div class="photo__icons">
                        <span class="photo__icon">
                        <a href="<?= site_url('post/like/' . $post_id) ?>">
                            <?php 
                                if($is_liked == 1)
                                    echo "<i class=\"fa heart fa-lg heart-red fa-heart\"></i>";
                                else
                                    echo "<i class=\"fa fa-heart-o heart fa-lg\"></i>";
                             ?>
                        </a> 
                        
                        <a href="<?= site_url('post/detail/' . $post_id) ?>"> 
                            <i class="fa fa-comment-o fa-lg"></i>
                        </a>
                    </div>
                    <span class="photo__likes"><?= esc($likes) ?> likes</span>
                    
                    <ul class="photo__comments">
                        </ul>

                    <a href="<?= site_url('post/detail/' . $post_id) ?>"> 
                        <li class="photo__comment">
                            <span class="photo__comment-author">
                                <?= esc($comments_count > 2 ? $comments_count - 2 : 'No') ?> more comments...
                            </span>
                        </li>
                    </a>

                    <span class="photo__time-ago"><?= esc($created_at) ?> days ago</span>
                        <div class="photo__add-comment-container">
                        <form action="<?= site_url('post/comment/' . $post_id) ?>" method="POST">
                            <textarea name="comment" placeholder="Add a comment..." class="photo__add-comment"></textarea>
                            <input type="hidden" name="return_to" value="feed">
                            <input type="submit" class="fa fa-ellipsis-h" value="Kirim"></input>
                        </form>
                        </div>
                </div>
            </section>
        <?php endforeach; ?>
        </main>

        <?= $this->include('partials/_footer') ?>
    </body>
</html>