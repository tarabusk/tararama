<?php 
/*
Plugin Name: tararama
Plugin URI:  
Description: Shortcode to display a slider: [tararama ids="1,2,3" auto="true" pause="3000" orderby=""post__in"]. A button will be added to the toolbar to help you build the shortcode.
Version: 1.0
Author: Gaëlle Vaudaine
Author URI: http://tarabusk.net
License: GPLv2
*/


class tararama_shortcode {
	static $add_tararama_script;

	static function init() {
		add_shortcode('tararama', array(__CLASS__, 'tararama_gallery_shortcode'));
        add_action('media_buttons', array(__CLASS__, 'tararama_add_media_button') );
		
		add_action( 'wp_enqueue_scripts',  array(__CLASS__, 'tararama_styles') );
		add_action('wp_enqueue_media',  array(__CLASS__, 'tararama_include_media_button_js_file'));
		
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}
	
	static function tararama_styles()
	{
		wp_enqueue_style( 'tarama-style',  plugin_dir_url( __FILE__ ) . '/style.css' );
	}


	static function tararama_gallery_shortcode($atts) {
		self::$add_tararama_script = true;

			$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		$atts = shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'post__in',
			'id'         => $post ? $post->ID : 0,	
			'auto'		 => true,	
			'pause'      => 4000,
			'include'    => '',
			'exclude'    => ''		
		), $attr, 'tararama' );

		$id = intval( $atts['id'] );

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		$columns = intval( $atts['columns'] );
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "tararama-{$instance}";
		$i = 0;
		$output .= "<ul  id='$selector' class='bxslider'>";
		foreach ( $attachments as $id => $attachment ) {
			$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
			$image_output = wp_get_attachment_url( $id );		
			$output .= "<li><img src='".$image_output."'  alt='" . wptexturize($attachment->post_excerpt) . "' title='" . wptexturize($attachment->post_excerpt) . "' /></li>";			
		}	
		$output .= "</ul >"; //bxslider	
		$output .= "  
		'<script>
		(function ($, root, undefined) {	
			$(function () {		
				'use strict';
				
				$('#{$selector}').bxSlider({
					mode: 'fade',
					captions: true,
					adaptiveHeight: true,
					auto:{$atts['auto']},
					pause:{$atts['pause']}
				});
				
			});
		})(jQuery, this);
	  </script>";
		return $output;
	}
	
	/*** add a button on the toolbar ***/
	static function tararama_add_media_button() {
	   echo '<a href="#" id="" class="tararama_btn button">Ajout diaporama</a>'; 
	}
	
   /* register scripts that will be called only when shortcode called */
	static function register_script() {
		
		wp_register_script( 'tarama-bxslider',  plugin_dir_url( __FILE__ ) . '/js/jquery.bxslider.min.js', array( 'jquery' ),'', false );
		//wp_register_script( 'tarama-script',  plugin_dir_url( __FILE__ ) . '/scripts.js', array( 'jquery' ),'', false );
	}

	
	static function register_style()
	{
		wp_enqueue_style( 'tarama-style',  plugin_dir_url( __FILE__ ).'/style.css' );
	}
	
	static function tararama_include_media_button_js_file() {   
		wp_enqueue_script( 'tarama-script',  plugin_dir_url( __FILE__ ) . '/scripts.js', array( 'jquery' ),'', false );
	}
	



	static function print_script() {
		
		if ( ! self::$add_tararama_script )
			return;
        // script called only if shorcode called
		wp_print_scripts('tarama-bxslider');
		
	}
}

tararama_shortcode::init();



?>