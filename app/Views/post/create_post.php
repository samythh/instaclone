<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Buat Postingan | Instaclone</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    </head>
    <body style="margin-top:50px">

    <nav class="navigation">
        <a href="<?= site_url('feed/' . $currentUsername) ?>">
            <img 
                src="<?= base_url('images/navLogo.png') ?>"
                alt="logo"
                title="logo"
                class="navigation__logo"
            />
        </a>
        <div class="navigation__icons">
            <a href="<?= site_url('explore') ?>" class="navigation__link">
                <i class="fa fa-compass"></i>
            </a>
            <a href="#" class="navigation__link">
                <i class="fa fa-heart-o"></i>
            </a>
            <a href="<?= site_url('profile/' . $currentUsername) ?>" class="navigation__link">
                <i class="fa fa-user-o"></i>
            </a>
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                
                <li class="people__person">
                    <div class="photo__header">
                        <div class="people__avatar-container">
                        <img 
                            src="<?= base_url(empty($profilePicture) ? 'images/avatar.svg' : $profilePicture) ?>"
                            class="people__avatar"
                        />
                        </div>
                        <div class="people__info">
                            <span class="people__username"><?= esc($currentUsername) ?></span>
                            <span class="people__full-name"><?= esc($profileName) ?></span>
                        </div>
                    </div>
                </li>

                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="your-class">
                <form action="<?= site_url('post/store') ?>" method="post" enctype="multipart/form-data">
                    <textarea cols="40" name="discription" rows="10" style="position:relative; width:100%;" placeholder="Tulis deskripsi Anda di sini"></textarea><br>
                    <label>Pilih gambar untuk diunggah.</label><br><br>
                    <input type="file" name="fileToUpload" id="fileToUpload" required>
                    <input type="submit" value="Post" style="float:right; background-color: #3897f0; color:#fff" name="submit">
                </form>
                </div>

            </div>
            <div class="col-sm-2"></div>
        </div>
    </div>
    
    <?= $this->include('partials/_footer') ?>
    </body>
</html>