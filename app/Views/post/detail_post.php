<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Postingan | Instaclone</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

    <?= $this->include('partials/_sidebar') ?>

    <main class="main-container">

        <div
            style="width: 100%; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px;">

            <?= view('post/view_modal', [
                'post' => $post,
                'poster' => $poster,
                'comments' => $comments,
                'currentUsername' => $currentUsername,
                'isLiked' => $isLiked,
                'timeAgo' => $timeAgo
            ]) ?>

        </div>

    </main>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="<?= base_url('js/app.js') ?>"></script>
    <script src="<?= base_url('js/like_ajax.js') ?>"></script>

    <script>
        function toggleOptions(id) {
            var menu = document.getElementById('optionsMenu-' + id);
            if (menu.style.display === 'flex') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'flex';
            }
        }
    </script>
</body>

</html>