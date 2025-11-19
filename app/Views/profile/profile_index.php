<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Profile | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body>
        <nav class="navigation">
            <a href="<?= site_url('feed/' . $currentUsername) ?>">
                <img src="<?= base_url('images/navLogo.png') ?>" alt="logo" title="logo" class="navigation__logo"/>
            </a>
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
        
        <main class="profile-container" style="margin-top: 100px;">
            <section class="profile">
                <header class="profile__header">
                    <div class="profile__avatar-container">
                        <img 
                            src="<?= base_url(empty($profileUser['profile_picture']) ? 'images/avatar.svg' : $profileUser['profile_picture']) ?>"
                            class="profile__avatar"
                         />
                    </div>
                    <div class="profile__info">
                        
                        <div class="profile__name">
                            <h1 class="profile__title"><?= esc($profileUser['username']) ?>
                                <img src="<?= base_url('images/verified.png') ?>" />
                            </h1>
                            <?php if ($isOwner): ?>
                                <a href="<?= site_url('profile/edit') ?>" class="profile__button u-fat-text">Edit profile</a>
                                <i class="fa fa-cog fa-2x" id="cog"></i>
                            <?php else: ?>
                                <a href="<?= site_url('profile/toggleFollow/' . $profileUser['username']) ?>" class="profile__button u-fat-text">
                                    <?= $isFollowing ? 'Following' : 'Follow' ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <ul class="profile__numbers">
                            <li class="profile__posts">
                                <span class="profile__number u-fat-text"><?= esc($profileUser['posts']) ?></span> posts
                            </li>
                            <li class="profile__followers">
                                <a href="<?= site_url('explore/followers/' . $profileUser['username']) ?>" class="profile__number u-fat-text">
                                    <?= esc($profileUser['followers']) ?> followers
                                </a>
                            </li>
                            <li class="profile__following">
                                <a href="<?= site_url('explore/followings/' . $profileUser['username']) ?>" class="profile__number u-fat-text">
                                    <?= esc($profileUser['followings']) ?> followings
                                </a>
                            </li>
                        </ul>
                        <div class="profile__bio">
                            <span class="profile__full-name u-fat-text"><?= esc($profileUser['profile_name']) ?></span>
                            <br>
                            <p class="profile__full-bio"><?= esc($profileUser['bio']) ?></p>
                        </div>
                    </div>
                </header>
                
                <div class="profile__pictures">
        
                <?php foreach ($posts as $post): ?>
                    <a href="<?= site_url('post/detail/' . $post['post_id']) ?>" class="profile-picture">
                        <img
                            height="300"
                            width="300"
                            src="<?= base_url($post['photo']) ?>"
                            class="profile-picture__picture"
                        />
                        <div class="profile-picture__overlay">
                            <span class="profile-picture__number">
                                <i class="fa fa-heart"></i> <?= esc($post['likes']) ?>
                            </span>
                            <span class="profile-picture__number">
                                <i class="fa fa-comment"></i> <?= esc($post['comments']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
                </div>
            </section>
        </main>
        
        <?= $this->include('partials/_footer') ?>
    </body>
</html>