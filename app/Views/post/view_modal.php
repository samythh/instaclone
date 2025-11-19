<div class="detail-card" style="position: relative;">

   <div id="optionsMenu-<?= $post['post_id'] ?>" class="options-overlay" style="display: none;">
      <div class="options-card">
         <?php if ($currentUsername === $poster['username']): ?>
            <a href="<?= site_url('post/delete/' . $post['post_id']) ?>" class="options-btn danger"
               onclick="return confirm('Hapus postingan ini?')">Hapus</a>
            <a href="<?= site_url('post/edit/' . $post['post_id']) ?>" class="options-btn">Edit</a>
         <?php endif; ?>

         <button class="options-btn" onclick="toggleOptions('<?= $post['post_id'] ?>')">Batal</button>
      </div>
   </div>

   <div class="detail-img-side">
      <img src="<?= base_url($post['photo']) ?>" class="detail-img-full">
   </div>

   <div class="detail-info-side">

      <header class="detail-header">
         <div style="display:flex; align-items:center;">
            <img
               src="<?= base_url(empty($poster['profile_picture']) ? 'images/avatar.svg' : $poster['profile_picture']) ?>"
               class="photo__avatar" style="width:32px; height:32px; margin-right:10px;">
            <div style="font-weight:600; font-size:14px;">
               <a href="<?= site_url('profile/' . $poster['username']) ?>" style="color:#262626; text-decoration:none;">
                  <?= esc($poster['username']) ?>
               </a>
            </div>
         </div>
         <i class="fa fa-ellipsis-h" style="margin-left:auto; cursor:pointer; padding:10px;"
            onclick="toggleOptions('<?= $post['post_id'] ?>')"></i>
      </header>

      <div class="detail-comments">
         <div class="comment-item">
            <img
               src="<?= base_url(empty($poster['profile_picture']) ? 'images/avatar.svg' : $poster['profile_picture']) ?>"
               class="comment-avatar">
            <div class="comment-content">
               <span class="comment-username"><?= esc($poster['username']) ?></span>
               <span class="comment-text"><?= esc($post['description']) ?></span>
               <div class="comment-meta"><?= esc($timeAgo) ?></div>
            </div>
         </div>

         <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
               <img src="<?= base_url('images/avatar.svg') ?>" class="comment-avatar">
               <div class="comment-content">
                  <span class="comment-username"><?= esc($comment['commentername']) ?></span>
                  <span class="comment-text"><?= esc($comment['comment_text']) ?></span>
               </div>
            </div>
         <?php endforeach; ?>
      </div>

      <div class="detail-footer">

         <div class="detail-actions" style="display: flex; align-items: center;">
            <a href="<?= site_url('post/like/' . $post['post_id']) ?>" class="btn-like"
               data-id="<?= $post['post_id'] ?>" style="text-decoration:none; color:inherit; margin-right: 16px;">
               <?php if ($isLiked): ?>
                  <i class="fa heart fa-heart heart-red" id="icon-modal-<?= $post['post_id'] ?>"></i>
               <?php else: ?>
                  <i class="fa fa-heart-o heart" id="icon-modal-<?= $post['post_id'] ?>"></i>
               <?php endif; ?>
            </a>

            <i class="fa fa-comment-o" style="margin-right: 16px; cursor: pointer;"
               onclick="document.getElementById('commentInput-<?= $post['post_id'] ?>').focus()"></i>

            <i class="fa fa-paper-plane-o" style="cursor: pointer;"></i>

            <i class="fa fa-bookmark-o" style="margin-left:auto; cursor: pointer;"></i>
         </div>

         <div class="detail-likes-date">
            <div style="font-weight:600; font-size:14px; margin-bottom:4px;">
               <span id="likes-count-modal-<?= $post['post_id'] ?>"><?= esc($post['likes']) ?></span> suka
            </div>
            <div style="font-size:10px; color:#8e8e8e; text-transform:uppercase;">
               <?= esc($timeAgo) ?>
            </div>
         </div>

         <form action="<?= site_url('post/comment/' . $post['post_id']) ?>" method="POST" class="detail-input-area">
            <i class="fa fa-smile-o" style="font-size:24px; margin-right:10px; color:#262626;"></i>

            <textarea id="commentInput-<?= $post['post_id'] ?>" name="comment" class="detail-input"
               placeholder="Tambahkan komentar..." required></textarea>

            <input type="hidden" name="return_to" value="feed">
            <button type="submit" class="detail-btn-post">Kirim</button>
         </form>
      </div>
   </div>
</div>

<script>
   // Toggle Menu Opsi
   function toggleOptions(id) {
      var menu = document.getElementById('optionsMenu-' + id);
      if (menu.style.display === 'flex') {
         menu.style.display = 'none';
      } else {
         menu.style.display = 'flex';
      }
   }
</script>