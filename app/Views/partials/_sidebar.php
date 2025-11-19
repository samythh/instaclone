<?php $currentUser = session()->get('username'); ?>

<nav class="navigation">
   <a href="<?= site_url('feed/' . $currentUser) ?>" class="navigation__logo-link">
      <img src="<?= base_url('images/navLogo.png') ?>" alt="Instaclone" class="navigation__logo" />
   </a>

   <ul class="navigation__list">
      <li class="navigation__list-item">
         <a href="<?= site_url('feed/' . $currentUser) ?>" class="navigation__link">
            <i class="fa fa-home"></i> <span class="navigation__text">Beranda</span>
         </a>
      </li>
      <li class="navigation__list-item">
         <a href="#" class="navigation__link">
            <i class="fa fa-search"></i> <span class="navigation__text">Cari</span>
         </a>
      </li>
      <li class="navigation__list-item">
         <a href="<?= site_url('explore') ?>" class="navigation__link">
            <i class="fa fa-compass"></i> <span class="navigation__text">Jelajahi</span>
         </a>
      </li>
      <li class="navigation__list-item">
         <a href="#" class="navigation__link">
            <i class="fa fa-film"></i> <span class="navigation__text">Reels</span>
         </a>
      </li>
      <li class="navigation__list-item">
         <a href="#" class="navigation__link">
            <i class="fa fa-paper-plane-o"></i> <span class="navigation__text">Pesan</span>
         </a>
      </li>

      <li class="navigation__list-item">
         <a href="#" class="navigation__link" id="btnNotifToggle">
            <i class="fa fa-heart-o"></i> <span class="navigation__text">Notifikasi</span>
         </a>
      </li>

      <li class="navigation__list-item">
         <a href="<?= site_url('post/create') ?>" class="navigation__link">
            <i class="fa fa-plus-square-o"></i> <span class="navigation__text">Buat</span>
         </a>
      </li>

      <li class="navigation__list-item">
         <a href="<?= site_url('profile/' . $currentUser) ?>" class="navigation__link navigation__link--profile">
            <img src="<?= base_url('images/avatar.svg') ?>" alt="Profile">
            <span class="navigation__text" style="font-weight:600;">Profil</span>
         </a>
      </li>
   </ul>

   <div class="navigation__more" id="moreMenuContainer">
      <div class="more-popup" id="morePopup">
         <a href="<?= site_url('logout') ?>" class="more-menu-item"><span>Keluar</span></a>
      </div>
      <div class="navigation__link" id="moreBtn" style="cursor: pointer;">
         <i class="fa fa-bars"></i> <span class="navigation__text">Lainnya</span>
      </div>
   </div>
</nav>

<div class="notification-drawer" id="notifDrawer">
   <div class="notif-header">Notifikasi</div>

   <div class="notif-scroll-area" id="notifContent">
   </div>
</div>

<div id="postModalOverlay" class="detail-overlay">
   <button class="detail-close" onclick="closeModal()"><i class="fa fa-times"></i></button>
   <div id="postModalContent" style="width:100%; display:flex; justify-content:center;"></div>
</div>

<script>

   function closeModal() {
      $('#postModalOverlay').hide();
      $('#postModalContent').html('');
   }

   document.addEventListener('DOMContentLoaded', function () {

      const moreBtn = document.getElementById('moreBtn');
      const morePopup = document.getElementById('morePopup');
      if (moreBtn && morePopup) {
         moreBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            morePopup.classList.toggle('show');
            if (morePopup.classList.contains('show')) {
               this.style.fontWeight = 'bold';
            } else {
               this.style.fontWeight = 'normal';
            }
         });
         document.addEventListener('click', function (e) {
            if (!morePopup.contains(e.target) && !moreBtn.contains(e.target)) {
               morePopup.classList.remove('show');
               moreBtn.style.fontWeight = 'normal';
            }
         });
      }

      const btnNotif = document.getElementById('btnNotifToggle');
      const drawer = document.getElementById('notifDrawer');
      const notifContent = document.getElementById('notifContent');

      if (btnNotif && drawer) {
         btnNotif.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            drawer.classList.toggle('show');

            if (drawer.classList.contains('show')) {
               this.querySelector('i').classList.remove('fa-heart-o');
               this.querySelector('i').classList.add('fa-heart');

               notifContent.innerHTML = '<div style="padding:20px; text-align:center; color:#999; margin-top: 50px;"><i class="fa fa-circle-o-notch fa-spin fa-2x"></i></div>';

               fetch('<?= site_url('notifications/load') ?>')
                  .then(response => response.text())
                  .then(html => { notifContent.innerHTML = html; })
                  .catch(err => console.error('Error loading notifs'));
            } else {
               this.querySelector('i').classList.remove('fa-heart');
               this.querySelector('i').classList.add('fa-heart-o');
            }
         });

         notifContent.addEventListener('click', function (e) {
            if (e.target.closest('a') || e.target.closest('.notif-content-modern')) {
               drawer.classList.remove('show');
               const icon = btnNotif.querySelector('i');
               if (icon) {
                  icon.classList.remove('fa-heart');
                  icon.classList.add('fa-heart-o');
               }
            }
         });

         document.addEventListener('click', function (e) {
            if (!drawer.contains(e.target) && !btnNotif.contains(e.target)) {
               drawer.classList.remove('show');
               const icon = btnNotif.querySelector('i');
               if (icon) {
                  icon.classList.remove('fa-heart');
                  icon.classList.add('fa-heart-o');
               }
            }
         });
      }
   });

   $(document).ready(function () {
      $(document).on('click', '.open-modal', function (e) {
         e.preventDefault();
         var url = $(this).attr('href');

         $('#notifDrawer').removeClass('show');
         $('#btnNotifToggle i').removeClass('fa-heart').addClass('fa-heart-o');

         $('#postModalOverlay').css('display', 'flex');
         $('#postModalContent').html('<div style="color:white; font-size:20px;">Memuat...</div>');

         $.get(url, function (data) {
            $('#postModalContent').html(data);
         }).fail(function () {
            $('#postModalContent').html('<div style="color:white;">Gagal memuat.</div>');
         });
      });

      $(document).mouseup(function (e) {
         var container = $(".detail-card");
         if ($('#postModalOverlay').is(':visible')) {
            if (!container.is(e.target) && container.has(e.target).length === 0) {
               closeModal();
            }
         }
      });
   });
</script>