
$(document).ready(function () {

   $(document).on('click', '.btn-like', function (e) {

      e.preventDefault();

      var button = $(this);
      var postId = button.data('id');
      var url = button.attr('href');

      var icon = button.find('i');

      var countFeed = $('#likes-count-' + postId);     
      var countModal = $('#likes-count-modal-' + postId); 

      var otherIcons = $('.btn-like[data-id="' + postId + '"]').not(button).find('i');

      $.ajax({
         url: url,
         type: 'GET',
         dataType: 'json',
         success: function (response) {
            if (response.success) {
               var newText = response.likes; 
               countFeed.text(newText);
               countModal.text(newText);

               if (response.liked) {
                  icon.removeClass('fa-heart-o').addClass('fa-heart heart-red');
                  otherIcons.removeClass('fa-heart-o').addClass('fa-heart heart-red'); 
               } else {
                  icon.removeClass('fa-heart heart-red').addClass('fa-heart-o');
                  otherIcons.removeClass('fa-heart heart-red').addClass('fa-heart-o'); 
               }
            }
         },
         error: function (xhr, status, error) {
            console.error("Gagal memproses like: " + error);
         }
      });
   });
});