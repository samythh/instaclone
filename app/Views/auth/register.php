<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sign Up | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body>
        <nav class="navigation">
            <a href="<?= site_url('/') ?>">
                <img src="<?= base_url('images/navLogo.png') ?>" alt="logo" title="logo" class="navigation__logo"/>
            </a>
        </nav>
        <main class="edit-profile">
            <section class="profile-form">
                <header class="profile-form__header">
                <img 
                    src="<?= base_url('images/navLogo.png') ?>"
                    alt="logo"
                    title="logo"
                    class="navigation__logo"
                />
                </header>
                
                <?php if (isset($validation)): ?>
                    <div style="color: red; margin-bottom: 20px; text-align: center;">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('register/store') ?>" class="edit-profile__form" method="post" enctype="multipart/form-data">
                    <div class="edit-profile__form-row">
                        <label for="username" class="edit-profile__label">Username</label>
                        <input type="text" id="username" name="username" class="edit-profile__input" required value="<?= old('username') ?>"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="profilename" class="edit-profile__label">Profile Name</label>
                        <input type="text" name="profilename" class="edit-profile__input" required value="<?= old('profilename') ?>"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="password" class="edit-profile__label">Password</label>
                        <input type="password" id="password" name="password" class="edit-profile__input" required/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="bio" class="edit-profile__label">Bio</label>
                        <textarea id="bio" name ="bio" class="edit-profile__textarea"><?= old('bio') ?></textarea>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="email" class="edit-profile__label">Email</label>
                        <input type="email" class="edit-profile__input" id="email" name ="email" required value="<?= old('email') ?>"/>
                    </div>
                    <div class="edit-profile__form-row">
                        <label for="image" class="edit-profile__label">Profile Image</label>
                         <input type="file" name="image" id="image" />  
                     </div>
                    <div class="edit-profile__form-row">
                        <label class="edit-profile__label"></label>
                        <input type="submit" id="register" name="register" value="Register">
                    </div>
                </form>
            </section>
        </main>
        
        <?= $this->include('partials/_footer') ?>
        
    </body>
</html>