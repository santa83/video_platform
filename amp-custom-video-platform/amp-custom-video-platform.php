<?php
/*
Plugin Name: Amp Custom Video Platform
Description: Extension made for AMP for WP to add a video after the title
Version: 1.0
Author:  Alessandro Santandrea
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


// Inserimento file js per la gestione del plugin
 
function my_enqueue_scripts() { 
    wp_enqueue_script( 'my-ajax-action', plugins_url('/js-acvp/my.ajax.action.js', __FILE__), array('jquery'), NULL, true);
//    wp_enqueue_style('custom-acvp-style', plugins_url('accelerated-mobile-pages/css-acvp/custom-acvp.css'));

}
add_action( 'admin_enqueue_scripts', 'my_enqueue_scripts');


// Inserimento settaggi nella dashboard dei plugin

function settings_link($links) {
    $mylinks = array(
         '<a href="' . admin_url( 'admin.php?page=amp_custom_video_platform' ) . '">Settings</a>',
         );
    return array_merge($links, $mylinks);
}

add_filter('plugin_action_links_' .plugin_basename( __FILE__ ),  'settings_link' );

// Inserimento pagina di impostazioni del plugin
function add_settings_page() {
    add_menu_page('Amp Custom Video Platform', 'ACVP', 'manage_options', 'amp_custom_video_platform', 'admin_index', 'dashicons-video-alt2
');
}

function admin_index() {
    require_once plugin_dir_path( __FILE__ ).'templates-acvp/admin.php';
}

add_action('admin_menu', 'add_settings_page');

// Aggiunta file js per Amp


function ampforwp_add_video_platform_head_js() { ?>  
      <script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>

    <?php
}

add_action('amp_post_template_head', 'ampforwp_add_video_platform_head_js');

// Inserimento css a seconda della versione attivata
function acvp_custom_insert_sticky_script() {
	$postId = get_the_ID();
	$embedVideo = get_post_meta($postId, 'embed_video',true);
    $css = "<style type='text/css'>
                    [id|=videoplayer-in-read-".$embedVideo."] {
                        padding:10px 0px;    
                    }
                    
                }</style>";    
  
     echo $css;
    $clientID = get_option('client_id_value');
    $fullUrl = site_url();
    $find = array( 'http://www.', 'https://www.' );
    $replace = '';
    $site = str_replace( $find, $replace, $fullUrl );
    
    $script = "<script type='text/javascript'>
   window._tgvtag = window._tgvtag || [];
   window._tgvtag.players = window._tgvtag.players || [];
   window._tgvtag.push({'clientId':'".$clientID."','domainApi':'https://services-01.tagvideo.eu/api/v/1','site':'".$site."'}); 
   (function () {
	 var s = document.createElement('script');
	 s.type = 'text/javascript';
	 s.async = true;
	 s.src = 'https://scripts-01.tagvideo.eu/js/loader.js?v=' + Math.random();
	 var sc = document.getElementsByTagName('script')[0];
	 sc.parentNode.insertBefore(s, sc);
   })();
</script>";    
    
    echo $script;
}
add_action( 'wp_head', 'acvp_custom_insert_sticky_script');

//Inserimento css versione amp
function ampforwp_add_video_platform_css() { ?>
	amp-iframe{
	    margin-bottom:15px;
	}
	.aligncenter{
	    font-size:11px;
	}
	<?php 
}

add_action('amp_post_template_css','ampforwp_add_video_platform_css');


// Inserimento video su Desktop // (e anche Mobile)
function add_video_desktop_and_mobile( $content ) {  
    $postId = get_the_ID();
    $isDesktop   = get_post_meta($postId, 'is_desktop', true);
    $embedVideo = get_post_meta($postId, 'embed_video',true);
    $inReadType = get_post_meta($postId, 'in_read_type', true );    

    if ($isDesktop !== 'true') {
        return $content;
    }
    
    switch ( $inReadType ) { 
        case 'in-read':
        $html = '<div id="videoplayer-in-read-'.$embedVideo.'" class="text-center"></div>
					<script>window._tgvtag.players.push( { \'placement\': \'videoplayer-in-read-'.$embedVideo.'\',\'id\': '.$embedVideo.', \'type\': \'in-read\'} ); </script>';
        break;
        case 'in-read-fixed-top':
        $html = '<div id="videoplayer-in-read-'.$embedVideo.'" class="text-center"></div>
					<script>window._tgvtag.players.push( { \'placement\': \'videoplayer-in-read-'.$embedVideo.'\',\'id\': '.$embedVideo.', \'type\': \'in-read-fixed-top\',\'deviceEnabled\':\'all\',\'deviceEffectEnable\':\'all\'} ); </script>';
        break;
        case 'in-read-float':
        $html = '<div id="videoplayer-in-read-'.$embedVideo.'" class="text-center"></div>
					<script>window._tgvtag.players.push( { \'placement\': \'videoplayer-in-read-'.$embedVideo.'\',\'id\': '.$embedVideo.', \'type\': \'in-read-float\',\'deviceEnabled\':\'all\',\'deviceEffectEnable\':\'all\'} ); </script>';
        break;
    }
    
    $html .= $content;
    return $html;
    
}

add_filter( 'the_content', 'add_video_desktop_and_mobile' ); 

//Inserimento video in versione amp
function amp_custom_video_below_the_title() { 
    
    $postId = get_the_ID();
    $isAmp   = get_post_meta($postId, 'is_amp', true);
    $embedVideo = get_post_meta($postId, 'embed_video',true);
    $clientID = get_option('client_id_value');
    
    
    
    if ($isAmp !== 'true') {
        return;
    }
?>
    <amp-iframe title="<?php echo ucfirst(get_bloginfo( 'name' )); ?> Video NewsLetter"
        allowfullscreen  
        frameborder="0" 
        height="360" 
        layout="responsive" 
        sandbox="allow-scripts allow-same-origin allow-popups" 
        src="https://services-01.tagvideo.eu/embed/<?php echo $clientID; ?>/<?php echo $embedVideo; ?>" 
        title="ViaggiNews.com Video NewsLetter" 
        width="640">
		<amp-img placeholder
    src="https://www.viagginews.com/wp-content/uploads/2018/10/placeholder_video.jpeg"
    layout="fill">
    </amp-img> 
    </amp-iframe>

    
	<?php 
}

add_action('ampforwp_below_the_title','amp_custom_video_below_the_title');


// Salvataggio dei meta data
function custom_save_video_platform_data($post_id) {
    // check if this isn't an auto save
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // security check
    ########################################################################
     if ( !wp_verify_nonce( $_POST['myplugin_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    // further checks if you like, 
    // for example particular user, role or maybe post type in case of custom post types

    // now store data in custom fields based on checkboxes selected
    $isAmp = $_POST['is_amp'];
    $isDesktop = $_POST['is_desktop'];
//    $isStickyMobile = $_POST['is_sticky_mobile'];
    $inReadType = $_POST['typeOption'];    
    $embedVideo = $_POST['embed_video'];
    
    update_post_meta( $post_id, 'is_desktop', $isDesktop );    
    update_post_meta( $post_id, 'is_amp', $isAmp );
//    update_post_meta( $post_id, 'is_sticky_mobile', $isStickyMobile );
    update_post_meta( $post_id, 'in_read_type', $inReadType );    
    update_post_meta( $post_id, 'embed_video', $embedVideo); 

}

add_action( 'save_post', 'custom_save_video_platform_data' );



// Stampa dei metabox
function custom_video_platform_box_content() {
    // nonce field for security check, you can have the same
    // nonce field for all your meta boxes of same plugin
    wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_nonce' );
    $postId = get_the_ID();
    $isAmp   = get_post_meta($postId, 'is_amp', true );
    $isDesktop   = get_post_meta($postId, 'is_desktop', true );
//    $isStickyMobile = get_post_meta($postId, 'is_sticky_mobile', true );
    $inReadType = get_post_meta($postId, 'in_read_type', true );    
    $embedVideo = get_post_meta($postId, 'embed_video', true );
    $wpNonce = wp_create_nonce('my_ajax_action');
    $checking = '';
    $checkingInRead = 'selected';
    $checkingInReadFixedTop = '';
    $checkingInReadFloat = '';
    
    if ( $isAmp === 'true' ) {
        $checkingAmp = 'checked';
    }
    else {
        $checkingAmp = '';
    }
    
    if ( $isDesktop === 'true' ) { 
        $checkingDesktop = 'checked';
    }
    else {
        $checkingDesktop = '';
    }
    
//    if ( $isStickyMobile === 'true' ) { 
//        $checkingStickyMobile = 'checked';
//    }
//    else {
//        $checkingStickyMobile = '';
//    }


    
    switch ( $inReadType ) { 
        case 'in-read-fixed-top':
        $checkingInReadFixedTop = 'selected';
        $checkingInRead = '';

        break;
        case 'in-read-float':
        $checkingInReadFloat = 'selected';
        $checkingInRead = '';                
        break;
    }

         
    
    echo '<p><input type="checkbox" name="is_amp" class="isAmpJs" value="'.$isAmp.'" '.$checkingAmp.' /> Abilita su Amp</p>';
    echo '<p><input type="checkbox" name="is_desktop" class="isDesktopJs" value="'.$isDesktop.'" '.$checkingDesktop.' /> Abilita su Mobile/Desktop</p>';
//    echo '<p><input type="checkbox" name="is_sticky_mobile" class="isStickyMobileJs" value="'.$isStickyMobile.'" '.$checkingStickyMobile.' /> Sticky Top on Scroll su Mobile</p>';
    echo '<p><select name="typeOption" id="typeOption" class="inReadTypeJs">
              <option value="in-read" '.$checkingInRead.'>In Read</option>
              <option value="in-read-fixed-top" '.$checkingInReadFixedTop.'>In Read Fixed Top</option>
              <option value="in-read-float" '.$checkingInReadFloat.'>In Read Float</option>
            </select></p>';
    echo '<p>ID Embed <input type="number" name="embed_video" class="embedVideoJs" value="'.$embedVideo.'" /></p>';
    echo '<input type="hidden" name="my_ajax_nonce" value="'.$wpNonce.'" />';
    echo '<button id="submit-my-form" data-post-id="'.$postId.'" type="submit">Salva le modifiche</button>';
    echo '<div class="acvp-success"></div>';
}

// Creazione dei meta boxes
function custom_video_platform_data() {
    add_meta_box(
        'my_meta_box_id',          // this is HTML id of the box on edit screen
        'Video Platform Data',    // title of the box
        'custom_video_platform_box_content',   // function to be called to display the checkboxes, see the function below
        'post',        // on which edit screen the box should appear
        'normal',      // part of page where the box should appear
        'default'      // priority of the box
    );
}
add_action( 'add_meta_boxes', 'custom_video_platform_data' );

// Chiamata Ajax che aggiorna i meta data
function my_ajax_custom_video_platform() {
    $postId = $_POST['postId'];
    $isAmp = $_POST['isAmp'];
    $isDesktop = $_POST['isDesktop'];
//    $isStickyMobile = $_POST['isStickyMobile'];
    $inReadType = $_POST['inReadType'];
    $embedVideo = $_POST['embedVideo'];
    
    update_post_meta( $postId, 'is_amp', $isAmp );
    update_post_meta( $postId, 'is_desktop', $isDesktop );
//    update_post_meta( $postId, 'is_sticky_mobile', $isStickyMobile );
    update_post_meta( $postId, 'in_read_type', $inReadType );    
    update_post_meta( $postId, 'embed_video', $embedVideo);
    exit;
}


add_action('wp_ajax_my_update_pm', 'my_ajax_custom_video_platform');
add_action('wp_ajax_nopriv_my_update_pm', 'my_ajax_custom_video_platform');