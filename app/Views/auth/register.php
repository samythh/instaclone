<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body class="no-padding">
    <main class="register-container">
        <section class="login__column" style="flex-direction: column;">

            <div class="register__box">
                <img src="<?= base_url('images/navLogo.png') ?>" class="login__logo" />

                <h2 class="register__subtitle">Buat akun untuk melihat foto dan video dari teman Anda.</h2>

                <a class="login__fb-link" href="#"
                    style="background-color:#0095f6; color:white; padding:8px; border-radius:4px;">
                    <i class="fa fa-facebook-square"></i> Masuk dengan Facebook
                </a>

                <div class="login__divider">ATAU</div>

                <?php if (isset($validation)): ?>
                    <div style="color: red; margin-bottom: 15px; font-size:12px; text-align: center;">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('register/store') ?>" method="post" enctype="multipart/form-data"
                    class="login__form">
                    <input type="email" name="email" class="login__input" placeholder="Email" required
                        value="<?= old('email') ?>" />
                    <input type="text" name="profilename" class="login__input" placeholder="Nama Lengkap" required
                        value="<?= old('profilename') ?>" />
                    <input type="text" name="username" class="login__input" placeholder="Nama Pengguna" required
                        value="<?= old('username') ?>" />
                    <input type="password" name="password" class="login__input" placeholder="Kata Sandi" required />

                    <div style="margin:10px 0; text-align:left; font-size:12px; color:#8e8e8e;">
                        <label>Foto Profil (Opsional)</label>
                        <input type="file" name="image" style="margin-top:5px; font-size:12px;">
                    </div>

                    <p class="register__terms">
                        Orang yang menggunakan layanan kami mungkin telah mengunggah informasi kontak Anda ke Instagram.
                        <a href="#">Pelajari Selengkapnya</a>
                    </p>

                    <input type="submit" value="Daftar" class="login__btn" />
                </form>
            </div>

            <div class="login__signup-box">
                <span class="login__text">Punya akun? <a href="<?= site_url('login') ?>"
                        class="login__link">Masuk</a></span>
            </div>

            <div style="text-align: center;">
                <p class="login__app-text">Dapatkan aplikasi.</p>
                <div class="login__appstores">
                    <img src="<?= base_url('images/ios.png') ?>" class="login__appstore" />
                    <img src="<?= base_url('images/android.png') ?>" class="login__appstore" />
                </div>
            </div>
        </section>
    </main>

    <div style="text-align: center; color: #8e8e8e; font-size: 12px; margin-top: 20px; margin-bottom: 20px;">
        © 2025 INSTACLONE BY MIKAIL
    </div>
</body>

</html>