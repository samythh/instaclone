<?php if (empty($notifications)): ?>
   <div
      style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:300px; color:#737373;">
      <i class="fa fa-heart-o" style="font-size:48px; margin-bottom:15px;"></i>
      <span>Aktivitas di postingan Anda</span>
      <span>akan muncul di sini.</span>
   </div>
<?php else: ?>

   <div class="notif-group-title">Terbaru</div>

   <?php foreach ($notifications as $notif): ?>
      <div class="notif-item-modern">

         <a href="<?= site_url('profile/' . $notif['from_username']) ?>">
            <img src="<?= base_url(empty($notif['actor_pic']) ? 'images/avatar.svg' : $notif['actor_pic']) ?>"
               class="notif-avatar-modern">
         </a>

         <div class="notif-content-modern"
            onclick="window.location.href='<?= site_url('profile/' . $notif['from_username']) ?>'">
            <span class="notif-user-bold"><?= esc($notif['from_username']) ?></span>
            <span class="notif-text-light">
               <?php if ($notif['type'] == 'like'): ?>
                  menyukai postingan Anda.
               <?php elseif ($notif['type'] == 'comment'): ?>
                  mengomentari: <?= esc(substr($notif['message'], 0, 20)) ?>
               <?php elseif ($notif['type'] == 'follow'): ?>
                  mulai mengikuti Anda.
               <?php endif; ?>
            </span>
            <span class="notif-time-ago">
               <?php
               $time = strtotime($notif['created_at']);
               $diff = abs(time() - $time);

               if ($diff < 60)
                  echo $diff . 'd';
               elseif ($diff < 3600)
                  echo floor($diff / 60) . 'm';
               elseif ($diff < 86400)
                  echo floor($diff / 3600) . 'j';
               else
                  echo floor($diff / 86400) . 'h';
               ?>
            </span>
         </div>

         <div class="notif-action-modern">
            <?php if ($notif['type'] == 'follow'): ?>

               <?php if (isset($notif['is_following']) && $notif['is_following'] > 0): ?>
                  <button class="btn-following-modern ajax-follow"
                     data-url="<?= site_url('profile/toggleFollow/' . $notif['from_username']) ?>">
                     Mengikuti
                  </button>
               <?php else: ?>
                  <button class="btn-follow-modern ajax-follow"
                     data-url="<?= site_url('profile/toggleFollow/' . $notif['from_username']) ?>">
                     Ikuti
                  </button>
               <?php endif; ?>

            <?php elseif (!empty($notif['post_photo'])): ?>

               <a href="<?= site_url('post/detail/' . $notif['post_id']) ?>">
                  <img src="<?= base_url($notif['post_photo']) ?>" class="notif-post-thumb">
               </a>

            <?php endif; ?>
         </div>

      </div>
   <?php endforeach; ?>

<?php endif; ?>