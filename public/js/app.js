$(document).ready(function () {
    var cog = $('#cog'),
        popUp = $('.popUp'),
        closePopUp = $('#closePopUp'),
        cancelPopUp = $('#cancelPopUp');

    cog.click(function () {
        popUp.fadeIn(500);
    })

    closePopUp.click(function () {
        popUp.fadeOut(500);
    })

    cancelPopUp.click(function () {
        popUp.slideUp(500)
    })
})