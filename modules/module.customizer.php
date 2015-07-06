<?php/** * Add to edit screen admin view */function lp_customizer_admin_bar() {    global $post;    global $wp_admin_bar;    if (is_admin() || !isset($post) || $post->post_type != 'landing-page') {        return;    }    $permalink = Landing_Pages_Variations::get_variation_permalink( $post->ID );    if ( isset($_GET['template-customize']) && $_GET['template-customize'] == 'on') {        $menu_title = __( 'Turn Off Editor' , 'landing-pages' );    } else {        $menu_title = __( 'Launch Visual Editor' , 'landing-pages' );        $permalink = add_query_arg( array( 'template-customize' => 'on' ) , $permalink );    }    $wp_admin_bar->add_menu(array('id' => 'launch-lp-front-end-customizer', 'title' => __($menu_title, 'landing-pages'), 'href' => $permalink));    $wp_admin_bar->add_menu(array('id' => 'lp-list-pages', 'title' => __("View Landing Page List", 'landing-pages'), 'href' => '/wp-admin/edit.php?post_type=landing-page'));}add_action('wp_before_admin_bar_render', 'lp_customizer_admin_bar');/* * Kill admin bar on visual editor preview window * */if (isset($_GET['cache_bust'])) {    show_admin_bar(false);}// Admin Side Print out variations toggles for preview iframesif (isset($_GET['iframe_window'])) {    add_action('admin_enqueue_scripts', 'lp_ab_previewer_enqueue');    function lp_ab_previewer_enqueue() {        wp_enqueue_style('lp_ab_testing_customizer_css', LANDINGPAGES_URLPATH . 'css/customizer-ab-testing.css');    }    show_admin_bar(false);    add_action('wp_head', 'lp_preview_iframe');    function lp_preview_iframe() {        $variation_id = (isset($_GET['lp-variation-id'])) ? $_GET['lp-variation-id'] : '0';        $landing_page_id = $_GET['post_id'];        $variations = get_post_meta($landing_page_id, 'lp-ab-variations', true);        $variations_array = explode(",", $variations);        $post_type_is = get_post_type($landing_page_id); ?>        <link rel="stylesheet" href="<?php echo LANDINGPAGES_URLPATH . 'css/customizer-ab-testing.css';?>"/>        <style type="text/css">            #variation-list {                position: absolute;                top: 0px;                left: 0px;                padding-left: 5px;            }            #variation-list h3 {                text-decoration: none;                border-bottom: none;            }            #variation-list div {                display: inline-block;            }            #current_variation_id, #current-post-id {                display: none !important;            }            <?php if ($post_type_is !== "landing-page") {            echo "#variation-list {display:none !important;}";            } ?>        </style>        <script type="text/javascript">            jQuery(document).ready(function ($) {                var current_page = jQuery("#current_variation_id").text();                // reload the iframe preview page (for option toggles)                jQuery('.variation-lp').on('click', function (event) {                    variation_is = jQuery(this).attr("id");                    var original_url = jQuery(parent.document).find("#TB_iframeContent").attr("src");                    var current_id = jQuery("#current-post-id").text();                    someURL = original_url;                    splitURL = someURL.split('?');                    someURL = splitURL[0];                    new_url = someURL + "?lp-variation-id=" + variation_is + "&iframe_window=on&post_id=" + current_id;                    jQuery(parent.document).find("#TB_iframeContent").attr("src", new_url);                });            });        </script>        <?php        if ($variations_array[0] === "") {            echo '<div id="variation-list" class="no-abtests"><h3>' . __('No A/B Tests running for this page', 'landing-pages') . '</h3>';        } else {            echo '<div id="variation-list"><h3>' . __('Variations', 'landing-pages') . ':</h3>';            echo '<div id="current_variation_id">' . $variation_id . '</div>';        }        foreach ($variations_array as $key => $val) {            $current_view = ($val == $variation_id) ? 'current-variation-view' : '';            echo "<div class='variation-lp " . $current_view . "' id=" . $val . ">";            echo Landing_Pages_Variations::vid_to_letter( $landing_page_id , $key);            // echo $val; number            echo "</div>";        }        echo "<span id='current-post-id'>$landing_page_id</span>";        echo '</div>';    }}// NEED ADMIN CHECK HERE// The loadtiny is specifically to load thing in the module.customizer-display.php iframe (not really working for whatever reason)if (isset($_GET['page']) && $_GET['page'] == 'lp-frontend-editor') {    add_action('init', 'lp_customizer_enqueue');    add_action('wp_enqueue_scripts', 'lp_customizer_enqueue');    function lp_customizer_enqueue($hook) {        wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));        wp_dequeue_script('jquery-cookie');        wp_enqueue_script('jquery-cookie', LANDINGPAGES_URLPATH . 'js/jquery.cookie.js');        wp_enqueue_style('wp-admin');        wp_admin_css('thickbox');        add_thickbox();        wp_enqueue_style('lp-admin-css', LANDINGPAGES_URLPATH . 'css/admin-style.css');        wp_enqueue_script('lp-post-edit-ui', LANDINGPAGES_URLPATH . 'js/admin/admin.post-edit.js');        wp_enqueue_script('lp-frontend-editor-js', LANDINGPAGES_URLPATH . 'js/customizer.save.js');        // Ajax Localize        wp_localize_script('lp-post-edit-ui', 'lp_post_edit_ui', array('ajaxurl' => admin_url('admin-ajax.php'), 'wp_landing_page_meta_nonce' => wp_create_nonce('wp-landing-page-meta-nonce')));        wp_enqueue_script('lp-js-isotope', LANDINGPAGES_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0');        wp_enqueue_style('lp-css-isotope', LANDINGPAGES_URLPATH . 'js/libraries/isotope/css/style.css');        //jpicker - color picker        wp_enqueue_script('jpicker', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');        wp_localize_script('jpicker', 'jpicker', array('thispath' => LANDINGPAGES_URLPATH . 'js/libraries/jpicker/images/'));        wp_enqueue_style('jpicker-css', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');        wp_enqueue_style('jpicker-css', LANDINGPAGES_URLPATH . 'js/libraries/jpicker/css/jPicker.css');        wp_enqueue_style('lp-customizer-frontend', LANDINGPAGES_URLPATH . 'css/customizer.frontend.css');        wp_dequeue_script('form-population');        wp_dequeue_script('inbound-analytics');        wp_enqueue_script('jquery-easing', LANDINGPAGES_URLPATH . 'js/jquery.easing.min.js');    }}/* DISPLAY HEADLINE HIDDEN INPUT FOR CUSTOMIZER */function lp_display_headline_input($id, $main_headline) {    //echo $id;    $id = Landing_Pages_Variations::prepare_input_id($id);    echo "<input type='text' name='{$id}' id='{$id}' value='{$main_headline}' size='30'>";}if (isset($_GET['notice']) && $_GET['notice'] == 'edit-note') {    echo "<div style='font-size:28px; text-align:center; position:absolute; left:33%; top:59px;'>" . __('Head into the landing page and click on frontend editor button!', 'landing-pages') . "</div>";}add_action('lp_launch_customizer_pre', 'lp_ab_testing_customizer_enqueue');function lp_ab_testing_customizer_enqueue($post) {    $permalink = get_permalink($post->ID);    $randomstring = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);    wp_enqueue_script('lp_ab_testing_customizer_js', LANDINGPAGES_URLPATH . 'js/customizer.ab-testing.js', array('jquery'));    wp_localize_script('lp_ab_testing_customizer_js', 'ab_customizer', array('lp_id' => $post->ID, 'permalink' => $permalink, 'randomstring' => $randomstring));    wp_enqueue_style('lp_ab_testing_customizer_css', LANDINGPAGES_URLPATH . 'css/customizer-ab-testing.css');}/*********** * Main Page Window * This is the page window behind the frames ***************/if (isset($_GET['template-customize']) && $_GET['template-customize'] == 'on') {    add_filter('wp_head', 'lp_launch_customizer');}// need filter to not load the actual page behind the frames. AKA kill the botton contentfunction lp_launch_customizer() {    global $post;    $permalink = get_permalink($post->ID);    $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);    $variation_id = Landing_Pages_Variations::get_current_variation( $post->ID );    $preview_link = add_query_args( array( 'lp-variation-id' => $variation_id , 'live-preview-area' => $randomString ) , $permalink );    $customizer_link = add_query_args( array( 'lp-variation-id' => $variation_id , 'post' => $post->ID , 'action' => 'edit' , 'frontend' => 'true' ) , admin_url('post.php') );    do_action('lp_launch_customizer_pre', $post);    ?>    <style type="text/css">        #wpadminbar {            z-index: 99999999999 !important;        }        #lp-live-preview #wpadminbar {            margin-top: 0px;        }        .lp-load-overlay {            position: absolute;            z-index: 9999999999 !important;            z-index: 999999;            background-color: #000;            opacity: 0;            background: -moz-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.4) 0, rgba(0, 0, 0, 0.9) 100%);            background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(0, 0, 0, 0.4)), color-stop(100%, rgba(0, 0, 0, 0.9)));            background: -webkit-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.4) 0, rgba(0, 0, 0, 0.9) 100%);            background: -o-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.4) 0, rgba(0, 0, 0, 0.9) 100%);            background: -ms-radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.4) 0, rgba(0, 0, 0, 0.9) 100%);            background: radial-gradient(center, ellipse cover, rgba(0, 0, 0, 0.4) 0, rgba(0, 0, 0, 0.9) 100%);            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#66000000', endColorstr='#e6000000', GradientType=1);            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";            filter: alpha(opacity=50);        }    </style>    <script type="text/javascript">        jQuery(document).ready(function ($) {            jQuery("#wp-admin-bar-edit a").text("Main Edit Screen");            setTimeout(function () {                jQuery(document).find("#lp-live-preview").contents().find("#wpadminbar").hide()                jQuery(document).find("#lp-live-preview").contents().find("html").css("margin-bottom", "-28px");            }, 2000);        });    </script>    <?php    echo '<div class="lp-load-overlay" style="top: 0;bottom: 0; left: 0;right: 0;position: fixed;opacity: .8; display:none;"></div><iframe id="lp_customizer_options" src="' . $customizer_link . '" style="width: 32%; height: 100%; position: fixed; left: 0px; z-index: 999999999; top: 26px;"></iframe>';    echo '<iframe id="lp-live-preview" src="' . $preview_link . '" style="width: 68%; height: 100%; position: fixed; right: 0px; top: 26px; z-index: 999999999; background-color: #eee;	//background-image: linear-gradient(45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194)), linear-gradient(-45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194));	//background-size:25px 25px; background-position: initial initial; background-repeat: initial initial;"></iframe>';    wp_footer();    exit;}