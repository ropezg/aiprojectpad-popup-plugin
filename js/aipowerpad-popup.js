// aipowerpad-popup.js
jQuery(document).ready(function($) {
    var delayTime = parseInt(popup_params.delayTime) * 1000; // Convert seconds to milliseconds
    var autoHideTime = parseInt(popup_params.autoHide) * 1000; // Get auto-hide time from settings
    var maxShow = parseInt(popup_params.maxShow); // Get maximum number of appearances from settings
    var shownCount = parseInt(localStorage.getItem('aipowerpad_popup_shown_count') || '0'); // Get current count of how many times the popup has been shown

    // Only show the popup if it hasn't exceeded the maximum number of appearances
    if (!maxShow || shownCount < maxShow) {
        setTimeout(function() {
            $('#aipowerpad-popup-overlay').fadeIn(); // Show the overlay
            $('#aipowerpad-popup-container').fadeIn(); // Show the popup
            localStorage.setItem('aipowerpad_popup_shown_count', shownCount + 1); // Increment the shown count

            // Set a timeout to auto-hide the popup if autoHideTime is greater than 0
            if (autoHideTime > 0) {
                setTimeout(function() {
                    $('#aipowerpad-popup-container, #aipowerpad-popup-overlay').fadeOut(); // Hide the popup and the overlay
                }, autoHideTime);
            }
        }, delayTime);
    }

    // Close popup and overlay when clicking the dismiss button or the overlay itself
    $('#aipowerpad-popup-container .aipowerpad-popup-dismiss, #aipowerpad-popup-overlay').on('click', function() {
        $('#aipowerpad-popup-container, #aipowerpad-popup-overlay').fadeOut(); // Hide the popup and the overlay
    });
});
