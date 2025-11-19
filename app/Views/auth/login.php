<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body class="no-padding">

    <main class="login-container">
        <div class="login__column">
            <img src="<?= base_url('images/phone.png') ?>" alt="Phone Image" class="login__phone-image" />
        </div>

        <section class="login__column" style="flex-direction: column;">

            <div class="login__box">
                <img src="<?= base_url('images/logo.png') ?>" alt="Logo" class="login__logo" />

                <?php if (session()->getFlashdata('msg')): ?>
                    <div style="color: #ed4956; margin-bottom: 15px; font-size: 14px; text-align: center;">
                        <?= session()->getFlashdata('msg') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= site_url('login/process') ?>" method="POST" class="login__form">
                    <input type="text" name="username" placeholder="Nama Pengguna" required class="login__input" />
                    <input type="password" name="password" placeholder="Kata Sandi" required class="login__input" />

                    <input type="submit" value="Log in" class="login__btn" />
                </form>

                <div class="login__divider">ATAU</div>

                <a class="login__fb-link" href="#">
                    <i class="fa fa-facebook-square fa-lg"></i> Masuk dengan Facebook
                </a>

                <a href="#" class="login__forgot">Lupa kata sandi?</a>
            </div>

            <div class="login__signup-box">
                <span class="login__text">
                    Tidak punya akun?
                    <a href="<?= site_url('register') ?>" class="login__link">
                        Buat akun
                    </a>
                </span>
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

    <div style="text-align: center; color: #8e8e8e; font-size: 12px; margin-top: 50px; margin-bottom: 20px;">
        © 2025 INSTACLONE BY MIKAIL
    </div>

</body>

</html>