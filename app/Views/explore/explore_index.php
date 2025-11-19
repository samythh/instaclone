<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= esc($title) ?> | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <link href="<?= base_url('css/explore.css') ?>" rel="stylesheet">
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
        
        <main class="explore" style="margin-top: 100px;">
            <h2 style="text-align: center; margin-bottom: 20px;"><?= esc($title) ?></h2>
            <section class="people">
                <ul class="people__list">

                <?php if (empty($results)): ?>
                    <li style="text-align: center; padding: 20px;">Tidak ada hasil ditemukan.</li>
                <?php endif; ?>

                <?php foreach($results as $row): 
                    $usernamee = $row['usernamee'];
                    $profile_name = $row['profile_name'];
                    $ifFollowing = $row['isFollowing'];
                ?>
                    <li class="people__person">
                        <a href="<?= site_url('profile/' . $currentUsername . '/' . $usernamee) ?>">
                            <div class="photo__header">
                                <div class="people__avatar-container">
                                    <img 
                                        src="<?= base_url(empty($row['profile_picture']) ? 'images/avatar.svg' : $row['profile_picture']) ?>"
                                        class="people__avatar"
                                    />
                                </div>
                                <div class="people__info">
                                    <span class="people__username"><?= esc($usernamee) ?>
                                    <img src="<?= base_url('images/verified.png') ?>" />
                                    </span>
                                    <span class="people__full-name"><?= esc($profile_name) ?></span>
                                </div>
                            </div>
                        </a>
                        <div class="people__column">
                            <?php if ($currentUsername !== $usernamee): ?>
                                <a href="<?= site_url('profile/toggleFollow/' . $usernamee) ?>" >
                                    <?= $ifFollowing ? 'Following' : 'Follow' ?>
                                </a>
                            <?php endif; ?>       
                        </div>
                    </li>
                <?php endforeach; ?>

                </ul>
            </section>
        </main>
        
        <?= $this->include('partials/_footer') ?>
    </body>
</html>