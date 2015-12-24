<?php

/*
Plugin Name: WP Inbox
Plugin URI: https://github.com/nguyenvanduocit/wp-inbox
Description: An simple plugin make your WordPress become a email inbox client.
Version: 1.0
Author: nguyenvanduocit
Author URI: http://wordpresskite.com
License: A "Slug" license name e.g. GPL2
*/
add_action( 'rest_api_init', 'wpib_register_route' );
/**
 * Registers the oEmbed REST API route.
 *
 * @since 4.4.0
 */
function wpib_register_route() {
	register_rest_route( 'wpib/1.0/', '/inbox', array(
		array(
			'methods'  => 'POST',
			'callback' => 'wpib_on_email_recived',
			'args'     => array(
				'mandrill_events' => array(
					'required' => true
				),
			)
		),
	) );
}

function wpib_on_email_recived( $request ) {
	$eventData = stripcslashes($request['mandrill_events']);
	$events  = json_decode( $eventData );
	foreach($events as $event) {
		$message = $event->msg;
		$args    = array(
			'post_content' => $message->html,
			'post_title'   => $message->subject
		);
		$postId  = wp_insert_post( $args );
		update_post_meta( $postId, 'mandrill_from_email', $message->from_email );
		update_post_meta( $postId, 'mandrill_from_name', $message->from_name );
		update_post_meta( $postId, 'mandrill_to', $message->to );
		update_post_meta( $postId, 'mandrill_email', $message->email );
		update_post_meta( $postId, 'mandrill_sender', $message->sender );
		update_post_meta( $postId, 'mandrill_headers', $message->sender );
		update_post_meta( $postId, 'mandrill_images', $message->images );
		update_post_meta( $postId, 'mandrill_spam_report', $message->spam_report );
		update_post_meta( $postId, 'mandrill_attachments', $message->attachments );
		update_post_meta( $postId, 'mandrill_text', $message->text );
		update_post_meta( $postId, 'mandrill_html', $message->html );
	}
	return true;
}