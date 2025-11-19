<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <title>Edit Postingan | Instaclone</title>
   <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
   <link rel="shortcut icon" href="<?= base_url('images/favicon.ico') ?>" type="image/x-icon">
   <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
   <meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body>

   <?= $this->include('partials/_sidebar') ?>

   <main class="main-container">

      <div class="create-post-wrapper">

         <button class="close-modal" onclick="window.history.back()">
            <i class="fa fa-times"></i>
         </button>

         <form action="<?= site_url('post/update/' . $post['post_id']) ?>" method="post" class="create-card"
            style="max-width: 900px;">

            <div class="create-header">
               <button type="button" class="header-btn danger" onclick="window.history.back()">Batal</button>

               <span class="header-title">Edit Info</span>

               <button type="submit" class="header-btn">Selesai</button>
            </div>

            <div class="create-body" style="display: flex; flex-direction: row; height: 500px;">

               <div class="details-image-container"
                  style="width: 60%; background-color: black; display: flex; align-items: center; justify-content: center;">
                  <img src="<?= base_url($post['photo']) ?>"
                     style="max-width: 100%; max-height: 100%; object-fit: contain;" />
               </div>

               <div class="details-form-container" style="width: 40%; border-left: 1px solid #dbdbdb; padding: 20px;">

                  <div class="user-info-mini" style="margin-bottom: 15px; display: flex; align-items: center;">
                     <img src="<?= base_url(empty($profilePicture) ? 'images/avatar.svg' : $profilePicture) ?>"
                        class="avatar-mini"
                        style="width: 28px; height: 28px; border-radius: 50%; margin-right: 10px;" />
                     <span class="username-mini" style="font-weight: 600;"><?= esc($currentUsername) ?></span>
                  </div>

                  <textarea name="discription" class="caption-input" placeholder="Tulis keterangan..."
                     style="width: 100%; border: none; resize: none; height: 200px; font-family: inherit; font-size: 15px; outline: none;"><?= esc($post['description']) ?></textarea>

                  <div style="border-top: 1px solid #efefef; margin-top: 10px; padding-top: 15px;">
                     <div
                        style="display:flex; justify-content:space-between; margin-bottom:15px; color:#262626; font-size: 14px;">
                        <span>Tambahkan lokasi</span>
                        <i class="fa fa-map-marker"></i>
                     </div>
                     <div style="display:flex; justify-content:space-between; color:#262626; font-size: 14px;">
                        <span>Teks alternatif</span>
                        <i class="fa fa-chevron-down"></i>
                     </div>
                  </div>

               </div>
            </div>

         </form>
      </div>
   </main>

   <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
   <script src="<?= base_url('js/app.js') ?>"></script>
</body>

</html>