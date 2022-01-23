jQuery(document).ready(function($) {
    $('.codereadr-admin-settings-page__buttons .codereadr-button-secondary').on('click', function(){ 
        $('.codereadr-admin-settings-page__api-key-input').val("");
        $('.codereadr-admin-settings-page__api-key-input').removeAttr("disabled");    
    });
    $('.codereadr-add-new-response').on('click', function(){ 
        $(".codereadr-add-response-modal").addClass('show');
    });
    $('.codereadr-add-response-modal .codereadr-button-secondary').on('click', function(){
        $(".codereadr-add-response-modal").removeClass('show');
    });

    $('.codereadr-add-response-modal .codereadr-button-primary').on('click', function() {
        $.ajax({
            url: codeReadr.ajaxUrl,
            method: "POST",
            dataType : "json",
            data: {
                action: "codereadr_insert_or_update_response",
                name: $('.codereadr-response-name').val(),
                txt: $(".codereadr-response-text").val(),
                status: $(".codereadr-response-status").val(),
                id: $(".codereadr-response-id").val()
            },
            success: function(data) {
                $(".codereadr-add-response-modal").removeClass('show');
            }
        });
    })
})