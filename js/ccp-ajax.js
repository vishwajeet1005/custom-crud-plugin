jQuery(document).ready(function($) {

    // Validate the form
    $('#ccp-form').validate({
        rules: {
            full_name: {
                required: true,
                minlength: 2
            },
            email: {
                required: true,
                email: true
            },
            message: {
                required: true,
                minlength: 5
            }
        },
        messages: {
            full_name: {
                required: "Please enter your full name",
                minlength: "Your name must consist of at least 2 characters"
            },
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            message: {
                required: "Please enter your message",
                minlength: "Your message must be at least 5 characters long"
            }
        },
        submitHandler: function(form) {
            var formData = $(form).serialize();

            $.ajax({
                type: 'POST',
                url: ccp_ajax_object.ajax_url,
                data: {
                    action: 'ccp_handle_form',
                    full_name: $('input[name="full_name"]').val(),
                    email: $('input[name="email"]').val(),
                    message: $('textarea[name="message"]').val(),
                },
                success: function(response) {
                    if (response.success) {
                        $('#ccp-message').html('<p>' + response.data + '</p>');
                        $('#ccp-form')[0].reset();
                    } else {
                        $('#ccp-message').html('<p>There was an error during form submission.</p>');
                    }
                }
            });
        }
    });

});