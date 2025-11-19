<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feed | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

    <?= $this->include('partials/_sidebar') ?>

    <main class="feed">
        <?php foreach ($feedData as $row):
            $follower = $row['follower'];
            $following_dp = $row['following_dp'];
            $post_id = $row['post_id'];
            $likes = $row['likes'];
            $is_liked = $row['is_liked'];
            $comments_count = $row['comments'];
            $created_at = $row['time_stamp'];
            ?>
            <section class="photo">
                <header class="photo__header">
                    <div style="display:flex; align-items:center;">
                        <a href="<?= site_url('profile/' . $follower) ?>" style="display:flex; align-items:center;">
                            <img class="photo__avatar"
                                src="<?= base_url(empty($following_dp) ? 'images/avatar.svg' : $following_dp) ?>" />
                        </a>
                        <div style="display:flex; flex-direction:column;">
                            <a href="<?= site_url('profile/' . $follower) ?>" class="photo__username">
                                <?= esc($follower) ?>
                            </a>
                        </div>
                    </div>
                    <i class="fa fa-ellipsis-h"></i>
                </header>

                <div class="photo__file-container">
                    <a href="<?= site_url('post/detail/' . $post_id) ?>" class="open-modal">
                        <img class="photo__file" src="<?= base_url($row['photo']) ?>">
                    </a>
                </div>

                <div class="photo__info">
                    <div class="photo__icons">
                        <span class="photo__icon">
                            <a href="<?= site_url('post/like/' . $post_id) ?>" class="btn-like" data-id="<?= $post_id ?>">
                                <?php if ($is_liked == 1): ?>
                                    <i class="fa heart fa-lg heart-red fa-heart" id="icon-<?= $post_id ?>"></i>
                                <?php else: ?>
                                    <i class="fa fa-heart-o heart fa-lg" id="icon-<?= $post_id ?>"></i>
                                <?php endif; ?>
                            </a>
                        </span>
                        <span class="photo__icon">
                            <a href="<?= site_url('post/detail/' . $post_id) ?>" class="open-modal">
                                <i class="fa fa-comment-o fa-lg"></i>
                            </a>
                        </span>
                        <span class="photo__icon"><i class="fa fa-paper-plane-o fa-lg"></i></span>
                        <span class="photo__icon" style="margin-left: auto;"><i class="fa fa-bookmark-o fa-lg"></i></span>
                    </div>

                    <span class="photo__likes">
                        <span id="likes-count-<?= $post_id ?>"><?= esc($likes) ?></span> suka
                    </span>

                    <a href="<?= site_url('post/detail/' . $post_id) ?>" class="open-modal" style="text-decoration:none;">
                        <div style="color:#999; font-size:14px; margin-top:5px; margin-bottom:5px;">
                            Lihat semua <?= esc($comments_count) ?> komentar
                        </div>
                    </a>

                    <span class="photo__time-ago"><?= esc($created_at) ?> HARI YANG LALU</span>

                    <div class="photo__add-comment-container">
                        <form action="<?= site_url('post/comment/' . $post_id) ?>" method="POST"
                            style="display:flex; align-items:center;">
                            <textarea name="comment" placeholder="Tambahkan komentar..."
                                class="photo__add-comment"></textarea>
                            <input type="hidden" name="return_to" value="feed">
                            <button type="submit"
                                style="background:none; border:none; color:#0095f6; font-weight:600; cursor:pointer;">Kirim</button>
                        </form>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </main>

    <div id="postModalOverlay" class="detail-overlay">
        <button class="detail-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
        <div id="postModalContent" style="width:100%; display:flex; justify-content:center;"></div>
    </div>

    <?= $this->include('partials/_footer') ?>

    <script>
        $(document).ready(function () {
            $('.open-modal').click(function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $('#postModalOverlay').css('display', 'flex');
                $('#postModalContent').html('<div style="color:white; font-size:20px;">Memuat...</div>');
                $.get(url, function (data) { $('#postModalContent').html(data); });
            });
        });
        function closeModal() { $('#postModalOverlay').hide(); $('#postModalContent').html(''); }
        $(document).mouseup(function (e) {
            var container = $(".detail-card");
            if (!container.is(e.target) && container.has(e.target).length === 0) closeModal();
        });
    </script>
</body>

</html>