<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Login | Instaclone</title>
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
        <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    </head>
    <body class="no-padding">
        <main class="login">
            <div class="login__column">
                <img 
                    src="<?= base_url('images/phone.png') ?>"
                    alt="Phone Image"
                    title="Phone Image"
                    class="login__phone-image"
                />                
            </div>
            <section class="login__column">
                <div class="login__section login__sign-in">
                    <img 
                        src="<?= base_url('images/logo.png') ?>"
                        alt="Logo"
                        title="Logo"
                        class="login__logo"
                    />

                    <?php if(session()->getFlashdata('msg')):?>
                        <div style="color: green; margin-top: 15px;"><?= session()->getFlashdata('msg') ?></div>
                    <?php endif;?>
                    
                    <form action="<?= site_url('login/process') ?>" method ="POST" class="login__form"> 
                        <div class="login__input-container">
                            <input 
                                type="text"
                                name="username"
                                placeholder="Username"
                                required
                                class="login__input"
                            />
                        </div>
                        <div class="login__input-container">
                            <input 
                                type="password"
                                name="password"
                                placeholder="Password"
                                required
                                class="login__input" 
                            />
                            <a href="#" class="login__form-link">Forgot?</a>
                        </div>
                        <div class="login__input-container">
                            <input
                                type="submit"
                                value="Log in"
                                class="login__input login__input--btn"
                            />
                        </div>
                    </form>
                    <span class="login__divider">or</span>
                    <a class="login__fb-link" href="#">
                        <i class="fa fa-facebook-square fa-lg" aria-hidden="true"></i> Log in with Facebook
                    </a>
                </div>
                <div class="login__section login__sign-up">
                    <span class="login__text">
                        Don't have an account? 
                        <a href="<?= site_url('register') ?>" class="login__link">
                            Sign up
                        </a>
                    </span>
                </div>
                <div class="login__section login__section--transparent login__app">
                    <span class="login__text">
                        Get the app.
                    </span>
                    <div class="login__appstores">
                        <img 
                            src="<?= base_url('images/ios.png') ?>"
                            alt="iOS"
                            title="Get the app!"
                            class="login__appstore" 
                        />
                        <img 
                            src="<?= base_url('images/android.png') ?>"
                            alt="Android"
                            title="Get the app!"
                            class="login__appstore" 
                        />
                    </div>
                </div>
            </section>
        </main>
        
        <?= $this->include('partials/_footer') ?> 
        
    </body>
</html>