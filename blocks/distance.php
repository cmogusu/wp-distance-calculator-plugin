<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package distance-calc
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function distance_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'distance/built/index.js';
	wp_register_script(
		'distance-block-editor',
		plugins_url( $index_js, __FILE__ ),
		//'http://localhost:9000/index.js',
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'distance/editor.css';
	wp_register_style(
		'distance-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'distance/style.css';
	wp_register_style(
		'distance-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'distance-calc/distance', array(
		'editor_script' => 'distance-block-editor',
		'editor_style'  => 'distance-block-editor',
		'style'         => 'distance-block',
	) );
}


function dc_enqueue_scripts(){
	$dc_settings = get_option( 'dc_settings' );

	wp_enqueue_script('dc-data',plugins_url('distance/data.js',__FILE__));
	wp_localize_script('dc-data','dcData',$dc_settings);
}


add_action( 'init', 'distance_block_init' );
add_action( 'admin_enqueue_scripts', 'dc_enqueue_scripts' );