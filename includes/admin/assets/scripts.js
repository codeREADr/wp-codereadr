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
    })
})