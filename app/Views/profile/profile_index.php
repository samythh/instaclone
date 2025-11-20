<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profil | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

    <?= $this->include('partials/_sidebar') ?>

    <main class="main-container">
        <div class="profile-wrapper">

            <header class="profile__header">
                <div class="profile__avatar-group">
                    <img src="<?= base_url(empty($profileUser['profile_picture']) ? 'images/avatar.svg' : $profileUser['profile_picture']) ?>"
                        class="profile__avatar" />
                </div>

                <div class="profile__info">
                    <div class="profile__row-top">
                        <h2 class="profile__username"><?= esc($profileUser['username']) ?></h2>

                        <?php if ($isOwner): ?>
                            <div class="profile__actions">
                                <a href="<?= site_url('profile/edit') ?>" class="btn-secondary">Edit profil</a>
                                <a href="#" class="btn-secondary">Lihat arsip</a>
                                <i class="fa fa-cog" style="font-size: 24px; cursor:pointer; margin-left:5px;"></i>
                            </div>
                        <?php else: ?>
                            <div class="profile__actions">
                                <a href="<?= site_url('profile/toggleFollow/' . $profileUser['username']) ?>"
                                    class="btn-primary">
                                    <?= $isFollowing ? 'Mengikuti' : 'Ikuti' ?>
                                </a>
                                <a href="#" class="btn-secondary">Pesan</a>
                                <i class="fa fa-ellipsis-h" style="font-size: 24px; cursor:pointer; margin-left:5px;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <ul class="profile__stats">
                        <li><span class="stat-number"><?= esc($profileUser['posts_count']) ?></span> kiriman</li>
                        <li><a href="<?= site_url('explore/followers/' . $profileUser['username']) ?>"><span
                                    class="stat-number"><?= esc($profileUser['followers']) ?></span> pengikut</a></li>
                        <li><a href="<?= site_url('explore/followings/' . $profileUser['username']) ?>"><span
                                    class="stat-number"><?= esc($profileUser['followings']) ?></span> diikuti</a></li>
                    </ul>

                    <div class="profile__bio-group">
                        <span class="profile__fullname"><?= esc($profileUser['profile_name']) ?></span>
                        <div class="profile__bio-text"><?= esc($profileUser['bio']) ?></div>
                    </div>
                </div>
            </header>

            <div class="profile__highlights">
                <div class="highlight-item">
                    <div class="highlight-circle">
                        <i class="fa fa-plus" style="font-size: 30px; color: #c7c7c7;"></i>
                    </div>
                    <span style="font-size:12px; font-weight:600;">Baru</span>
                </div>
            </div>

            <div class="profile__tabs">
                <div class="profile__tab active">
                    <i class="fa fa-th"></i>
                    <span>POSTINGAN</span>
                </div>
                <div class="profile__tab">
                    <i class="fa fa-bookmark-o"></i>
                    <span>TERSIMPAN</span>
                </div>
                <div class="profile__tab">
                    <i class="fa fa-user-o"></i>
                    <span>DITANDAI</span>
                </div>
            </div>

            <div class="profile__pictures">
                <?php foreach ($posts as $post): ?>
                    <a href="<?= site_url('post/detail/' . $post['post_id']) ?>" class="profile-picture open-modal">
                        <img src="<?= base_url($post['photo']) ?>" class="profile-picture__picture" />
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

            <div style="text-align:center; margin: 30px 0; color:#999; font-size:12px;">
                © 2025 INSTACLONE BY MIKAIL
            </div>
        </div>
    </main>

    <div id="postModalOverlay" class="detail-overlay">
        <button class="detail-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
        <div id="postModalContent" style="width:100%; display:flex; justify-content:center;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script src="<?= base_url('js/like_ajax.js') ?>"></script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.open-modal', function (e) {
                e.preventDefault();

                var url = $(this).attr('href');

                // Tampilkan Overlay Loading
                $('#postModalOverlay').css('display', 'flex');
                $('#postModalContent').html('<div style="color:white; font-size:20px;">Memuat...</div>');

                // Ambil konten
                $.get(url, function (data) {
                    $('#postModalContent').html(data);
                }).fail(function () {
                    $('#postModalContent').html('<div style="color:white;">Gagal memuat postingan.</div>');
                });
            });
        });

        function closeModal() {
            $('#postModalOverlay').hide();
            $('#postModalContent').html('');
        }

        $(document).mouseup(function (e) {
            var container = $(".detail-card");
            if ($('#postModalOverlay').is(':visible')) {
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    closeModal();
                }
            }
        });

        function toggleOptions(postId) {
            var menu = $('#optionsMenu-' + postId);
            if (menu.css('display') === 'flex') {
                menu.hide();
            } else {
                menu.css('display', 'flex');
            }
        }
    </script>

</body>

</html>