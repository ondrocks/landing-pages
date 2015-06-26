<?php/** * Adds Ajax for Clear Stats button * clear stats for all variations */add_action( 'wp_ajax_nopriv_lp_clear_stats_action', 'lp_clear_stats_action' );add_action( 'wp_ajax_lp_clear_stats_action', 'lp_clear_stats_action' );function lp_clear_stats_action() {	global $wpdb;	$newrules = "0";	$post_id = mysql_real_escape_string($_POST['page_id']);	$variations = get_post_meta($post_id, 'lp-ab-variations', true);	if ($variations){		$variations = explode(",", $variations);		foreach ($variations as $vid) {			add_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules );			add_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules );		}	} else {		add_post_meta( $post_id, 'lp-ab-variation-impressions-0' , $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-impressions-0', $newrules );	}	header('HTTP/1.1 200 OK');}/** * Adds Ajax for Clear Stats button * clear stats for single variations */add_action( 'wp_ajax_nopriv_lp_clear_stats_single', 'lp_clear_stats_single' );add_action( 'wp_ajax_lp_clear_stats_single', 'lp_clear_stats_single' );function lp_clear_stats_single() {	global $wpdb;	$newrules = "0";	$post_id = mysql_real_escape_string($_POST['page_id']);	$vid = $_POST['variation'];	add_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-impressions-'.$vid, $newrules );	add_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules, true ) or update_post_meta( $post_id, 'lp-ab-variation-conversions-'.$vid, $newrules );	header('HTTP/1.1 200 OK');}/** * Adds Ajax for Clear Stats button * clear stats for non lp post */add_action( 'wp_ajax_nopriv_lp_clear_stats_post', 'lp_clear_stats_post' );add_action( 'wp_ajax_lp_clear_stats_post', 'lp_clear_stats_post' );function lp_clear_stats_post() {	global $wpdb;	$newrules = "0";	$post_id = mysql_real_escape_string($_POST['post_id']);	$vid = $_POST['variation'];	update_post_meta( $post_id, '_inbound_impressions_count', '0' );	update_post_meta( $post_id, '_inbound_conversions_count', '0' );	header('HTTP/1.1 200 OK');}/* * Adds ajax to record landing page impressions * future plans to integrate with google analytics*/add_action('wp_ajax_lp_record_impression', 'lp_record_impression_callback');add_action('wp_ajax_nopriv_lp_record_impression', 'lp_record_impression_callback');function lp_record_impression_callback() {	global $wpdb; /*this is how you get access to the database */	global $user_ID;	$post_id = $_POST['post_id'];	$post_type = $_POST['post_type'];	$variation_id = $_POST['variation_id'];	if ( $disable_admin_tracking && current_user_can( 'manage_options' ) )	{		_e( "admin tracking disabled" , 'landing-pages' );		die();	}	die();}/** * Adds Ajax Template Selection * @return prints out landing page meta options */add_action( 'wp_ajax_nopriv_lp_get_template_meta', 'lp_get_template_meta' );add_action( 'wp_ajax_lp_get_template_meta', 'lp_get_template_meta' );function lp_get_template_meta() {	global $wpdb;	$current_template = $_POST['selected_template'];	$post_id = $_POST['post_id'];	$post = get_post($post_id);	/*echo $current_template; exit; */	$key['args']['key'] = $current_template;	lp_show_metabox($post,$key);	die();}?>