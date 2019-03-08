jQuery(document).ready(function($) {
    console.log('eccomi');
     $('.isAmpJs').change(function(){
         cb = $(this);
        cb.val(cb.prop('checked'));
    });
    
    $('.isDesktopJs').change(function(){
         cb = $(this);
        cb.val(cb.prop('checked'));
    });
    
    $('.isStickyMobileJs').change(function(){
         cb = $(this);
        cb.val(cb.prop('checked'));
    });
    
    
    $('body').on('click', '#submit-my-form', function(e) {                   
        e.preventDefault();
        
        var postId = $(this).attr('data-post-id');
        var isAmp = $(this).closest('#my_meta_box_id').find('.isAmpJs').val();
        var embedVideo = $(this).closest('#my_meta_box_id').find('.embedVideoJs').val();
        var isDesktop  = $(this).closest('#my_meta_box_id').find('.isDesktopJs').val();
        var inReadType = $(this).closest('#my_meta_box_id').find('.inReadTypeJs').val();

        var isStickyMobile  = $(this).closest('#my_meta_box_id').find('.isStickyMobileJs').val();
        
        var data = {
            action: 'my_update_pm',
            postId: postId,
            isAmp: isAmp,
            isDesktop: isDesktop,
            isStickyMobile: isStickyMobile,
            inReadType: inReadType,
            embedVideo: embedVideo
        };
        jQuery.post('admin-ajax.php', data, function(response) {
        }).done(function(data) {
            var html = "<p class='success-message'>Modifiche avvenute con successo!</p>";
            $('.acvp-success').append(html);
            setTimeout(function() {
              $(".success-message").fadeOut(1000);
            }, 3000);
        }).fail(function(xhr) {
            console.log(xhr.responseText);
        });
        
        
        
    });
});