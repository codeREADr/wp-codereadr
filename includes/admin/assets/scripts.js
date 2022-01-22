jQuery(document).ready(function($) {
    $('.codereadr-admin-settings-page__buttons .codereadr-button-secondary').on('click', function(){ 
        $('.codereadr-admin-settings-page__api-key-input').val("");
        $('.codereadr-admin-settings-page__api-key-input').removeAttr("disabled");    
    
    })
})