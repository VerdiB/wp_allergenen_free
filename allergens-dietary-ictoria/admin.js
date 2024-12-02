jQuery(document).ready(function($) {
    // Handle click event on auto-update toggle links
    $('.my-plugin-toggle-auto-update').on('click', function(e) {
        e.preventDefault();
        var plugin = $(this).data('plugin'); 
        var action = $(this).data('action'); 

        // Perform Ajax request to toggle auto-update
        $.ajax({
            url: myPluginAjax.ajax_url, 
            type: 'POST', 
            data: {
                action: 'my_plugin_toggle_auto_update',
                security: myPluginAjax.nonce, 
                plugin: plugin,
                toggle_action: action
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Something went wrong. Please try again.'); 
                }
            }
        });
    });
});
