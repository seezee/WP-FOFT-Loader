<?php

	$current_section = '';

if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
	if ( ! wp_verify_nonce( $_POST['_wpnonce'] ) ) {
		wp_die( 'Go away!' );
	} else {
		$current_section = sanitize_text_field( wp_unslash( $_POST['tab'] ) );
	}
} else {
	if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'] ) ) {
			wp_die( 'Go away!' );
		} else {
			$current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}
	}
}
