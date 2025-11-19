<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>Notifikasi | Instaclone</title>
   <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
   <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
   <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
   <meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

   <?= $this->include('partials/_sidebar') ?>

   <main class="main-container">
      <div class="notif-wrapper">
         <h2 class="notif-title">Notifikasi</h2>

         <div class="notif-list">
            <?php if (empty($notifications)): ?>
               <div style="text-align:center; padding:40px; color:#8e8e8e;">
                  <i class="fa fa-heart-o" style="font-size:40px; margin-bottom:10px;"></i>
                  <p>Belum ada notifikasi.</p>
                  <small>Aktivitas like, komentar, dan follow akan muncul di sini.</small>
               </div>
            <?php else: ?>

               <?php foreach ($notifications as $notif): ?>
                  <div class="notif-item">

                     <a href="<?= site_url('profile/' . $notif['from_username']) ?>">
                        <img src="<?= base_url(empty($notif['actor_pic']) ? 'images/avatar.svg' : $notif['actor_pic']) ?>"
                           class="notif-avatar">
                     </a>

                     <div class="notif-text">
                        <a href="<?= site_url('profile/' . $notif['from_username']) ?>" class="notif-username">
                           <?= esc($notif['from_username']) ?>
                        </a>

                        <?php if ($notif['type'] == 'like'): ?>
                           menyukai postingan Anda.
                        <?php elseif ($notif['type'] == 'comment'): ?>
                           mengomentari:
                           "<?= esc(substr($notif['message'], 0, 20)) . (strlen($notif['message']) > 20 ? '...' : '') ?>"
                        <?php elseif ($notif['type'] == 'follow'): ?>
                           mulai mengikuti Anda.
                        <?php endif; ?>

                        <span class="notif-time">
                           <?php
                           $time = strtotime($notif['created_at']);
                           $diff = time() - $time;
                           if ($diff < 3600)
                              echo floor($diff / 60) . 'm';
                           else if ($diff < 86400)
                              echo floor($diff / 3600) . 'j';
                           else
                              echo floor($diff / 86400) . 'h';
                           ?>
                        </span>
                     </div>

                     <div class="notif-action">
                        <?php if ($notif['type'] == 'follow'): ?>
                           <a href="<?= site_url('profile/toggleFollow/' . $notif['from_username']) ?>" class="btn-primary"
                              style="padding: 5px 16px; font-size: 12px;">Ikuti</a>
                        <?php elseif (!empty($notif['post_photo'])): ?>
                           <a href="<?= site_url('post/detail/' . $notif['post_id']) ?>">
                              <img src="<?= base_url($notif['post_photo']) ?>" class="notif-post-img">
                           </a>
                        <?php endif; ?>
                     </div>

                  </div>
               <?php endforeach; ?>

            <?php endif; ?>
         </div>
      </div>
   </main>

   <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
   <script src="<?= base_url('js/app.js') ?>"></script>
</body>

</html>