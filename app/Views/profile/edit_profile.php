<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Edit Profile | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <link href="<?= base_url('css/edit-profile.css') ?>" rel="stylesheet">
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
        
        <main class="edit-profile" style="margin-top: 100px;">
            <section class="profile-form">
                <header class="profile-form__header">
                    <div class="profile-form__avatar-container">
                        <img 
                            src="<?= base_url(empty($user['profile_picture']) ? 'images/avatar.svg' : $user['profile_picture']) ?>"
                            class="profile-form__avatar"
                        />
                    </div>
                    <h4 class="profile-form__title"><?= esc($currentUsername) ?></h4>
                </header>

                <?php if(session()->getFlashdata('error')): ?>
                    <div style="color: red; text-align: center; margin-bottom: 20px;"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('msg')): ?>
                    <div style="color: green; text-align: center; margin-bottom: 20px;"><?= session()->getFlashdata('msg') ?></div>
                <?php endif; ?>

                <form action="<?= site_url('profile/update') ?>" method="post" class="edit-profile__form">
                    <div class="edit-profile__form-row">
                        <label for="name" class="edit-profile__label">Name</label>
                        <input name="name" type="text" value="<?= esc($user['profile_name']) ?>" class="edit-profile__input"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="username" class="edit-profile__label">Username</label>
                        <input type="text" name="username" value="<?= esc($user['username']) ?>" class="edit-profile__input"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="password" class="edit-profile__label">Password</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin diubah" class="edit-profile__input"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="bio" class="edit-profile__label">Bio</label>
                        <textarea name="bio" class="edit-profile__textarea"><?= esc($user['bio']) ?></textarea>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="email" class="edit-profile__label">Email</label>
                        <input type="email" class="edit-profile__input" name="email" value="<?= esc($user['email']) ?>"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label class="edit-profile__label"></label>
                        <input type="submit" name="submit" value="Submit">
                    </div>
                </form>
            </section>
        </main>
        
        <?= $this->include('partials/_footer') ?>
    </body>
</html>