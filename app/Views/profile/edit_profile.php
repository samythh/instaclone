<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>
    <?= $this->include('partials/_sidebar') ?>

    <main class="main-container">
        <div class="edit-profile-wrapper">
            <h1 class="edit-title">Edit profil</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div style="color: red; margin-bottom: 20px; background: #ffe6e6; padding: 10px; border-radius: 8px;">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('msg')): ?>
                <div style="color: green; margin-bottom: 20px; background: #e6ffe6; padding: 10px; border-radius: 8px;">
                    <?= session()->getFlashdata('msg') ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                <div class="photo-change-card">
                    <div class="photo-change-info">
                        <img src="<?= base_url(empty($user['profile_picture']) ? 'images/avatar.svg' : $user['profile_picture']) ?>"
                            class="photo-change-avatar" />
                        <div>
                            <span class="photo-change-username"><?= esc($user['username']) ?></span>
                            <span class="photo-change-text"><?= esc($user['profile_name']) ?></span>
                        </div>
                    </div>
                    <label for="imageUpload" class="btn-change-photo">Ubah foto</label>
                    <input type="file" id="imageUpload" name="image" style="display: none;"
                        onchange="previewImage(this)">
                </div>

                <div class="edit-form-group">
                    <label class="edit-label">Nama</label>
                    <input type="text" name="name" value="<?= esc($user['profile_name']) ?>" class="edit-input"
                        placeholder="Nama lengkap Anda" />
                </div>
                <div class="edit-form-group">
                    <label class="edit-label">Username</label>
                    <input type="text" name="username" value="<?= esc($user['username']) ?>" class="edit-input"
                        placeholder="Username" />
                </div>
                <div class="edit-form-group">
                    <label class="edit-label">Bio</label>
                    <textarea name="bio" class="edit-textarea" maxlength="150"
                        oninput="updateCount(this)"><?= esc($user['bio']) ?></textarea>
                    <div class="bio-counter"><span id="charCount">0</span> / 150</div>
                </div>
                <div class="btn-submit-container">
                    <input type="submit" name="submit" value="Kirim" class="btn-submit-primary">
                </div>
            </form>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script>
        function updateCount(field) { document.getElementById('charCount').innerText = field.value.length; }
        document.addEventListener("DOMContentLoaded", function () { var bio = document.querySelector('textarea[name="bio"]'); if (bio) updateCount(bio); });
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) { document.querySelector('.photo-change-avatar').src = e.target.result; }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>