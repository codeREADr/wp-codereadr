jQuery(document).ready(function($) {
    $('.codereadr-admin-settings-page__buttons .codereadr-button-secondary').on('click', function(){ 
        $('.codereadr-admin-settings-page__api-key-input').val("");
        $('.codereadr-admin-settings-page__api-key-input').removeAttr("disabled");    
    });
    $('.codereadr-add-new-service').on('click', function(){ 
        $(".codereadr-add-service-modal").addClass('show');
    });
    $('.codereadr-add-service-modal .codereadr-button-secondary').on('click', function(){
        $(".codereadr-add-service-modal").removeClass('show');
    });

    $('.codereadr-add-service-modal .codereadr-button-primary').on('click', function() {
        $.ajax({
            url: codeReadr.ajaxUrl,
            method: "POST",
            dataType : "json",
            data: {
                action: "codereadr_insert_or_update_service",
                name: $('.codereadr-service-name').val(),
                txt: $(".codereadr-service-text").val(),
                status: $(".codereadr-service-status").val(),
                id: $(".codereadr-service-id").val()
            },
            success: function(data) {
                $(".codereadr-add-service-modal").removeClass('show');
            }
        });
    })
})