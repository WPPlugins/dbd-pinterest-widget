<?php

/*
Plugin Name: DBD Pinterest Widget
Plugin URI: http://wordpress.org/extend/plugins/jn-pinterest-widget/
Description: This is a simple plugin to provide a pinterest widget based on a feed you specify.
Author: Justin Norton
Version: 1.0
Author URI: http://jnorton.co.uk
*/

//------------------------------------------------------------------------------------------------------------------------
// REGISTER THE JAVASCRIPT AND CSS
//------------------------------------------------------------------------------------------------------------------------

/* LATEST NEWS POSTS */
class DBD_Pinterest_Widget extends WP_Widget {

	var $version = '1.0';

	function DBD_Pinterest_Widget() {
		$this->WP_Widget('DBD_Pinterest_Widget', 'DBD Pinterest Widget');
		add_action('init', array(&$this, 'enqueueResources'));
	}

	function enqueueResources() {
		wp_enqueue_style('dbd_pinterest_style', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/theme/css/dbd_pinterest.css', false, $this->version, 'screen');
		wp_enqueue_script('dbd_pinterest_script', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/theme/js/dbd_pinterest.js', array('jquery'), $this->version);
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;

		$instance['dbd_pin_account_url'] = strip_tags(stripslashes($new_instance['dbd_pin_account_url']));
		$instance['dbd_pin_feed_url'] = strip_tags(stripslashes($new_instance['dbd_pin_feed_url']));

		if (!$new_instance['dbd_pin_max_items']) {
			$instance['dbd_pin_max_items'] = 6;
		} else {
			$instance['dbd_pin_max_items'] = strip_tags(stripslashes($new_instance['dbd_pin_max_items']));
		}

		return $instance;
	}

	function form($instance) {
		//Defaults
		$instance = wp_parse_args((array) $instance, array('tile' => null));

		$dbd_pin_feed_url = htmlspecialchars($instance['dbd_pin_feed_url']);
		$dbd_pin_max_items = htmlspecialchars($instance['dbd_pin_max_items']);
		$dbd_pin_account_url = htmlspecialchars($instance['dbd_pin_account_url']);

		# Pinterest Account URL
		$output .= '<p><label for="' . $this->get_field_name('dbd_pin_account_url') . '">';
		$output .= 'Pinterest Account URL:</label>';
		$output .= '<input class="widefat"';
		$output .= 'id="' . $this->get_field_id('dbd_pin_account_url') . '"';
		$output .= 'name="' . $this->get_field_name('dbd_pin_account_url') . '"';
		$output .= 'type="text"';
		$output .= 'value="' . $dbd_pin_account_url . '" /><span style="font-size: 9px;">E.g. http://pinterest.com/your_account/</span></p>';

		# Pinterest Feed URL
		$output .= '<p><label for="' . $this->get_field_name('dbd_pin_feed_url') . '">';
		$output .= 'Pinterest Feed URL:</label>';
		$output .= '<input class="widefat"';
		$output .= 'id="' . $this->get_field_id('dbd_pin_feed_url') . '"';
		$output .= 'name="' . $this->get_field_name('dbd_pin_feed_url') . '"';
		$output .= 'type="text"';
		$output .= 'value="' . $dbd_pin_feed_url . '" /><span style="font-size: 9px;">E.g. http://pinterest.com/your_account/feed.rss</span></p>';

		# Number of items to display
		$output .= '<p><label for="' . $this->get_field_name('dbd_pin_max_items') . '">';
		$output .= 'Number of items to display:</label>';
		$output .= '<input id="' . $this->get_field_id('dbd_pin_max_items') . '"';
		$output .= 'name="' . $this->get_field_name('dbd_pin_max_items') . '"';
		$output .= 'type="text"';
		$output .= 'value="';
		if (!$dbd_pin_max_items) {
			$output .= '6';
		} else {
			$output .= $dbd_pin_max_items;
		}
		$output .= '" /></p>';
		echo $output;

	}

	function widget($args, $instance) {

		$pinterest_feed = fetch_feed( $instance['dbd_pin_feed_url'] );

		if (!is_wp_error( $pinterest_feed ) ) :
			$maxitems = $instance['dbd_pin_max_items'];
		$pinterest_feed = $pinterest_feed->get_items(0, $maxitems);
		endif;

		if ( !empty( $pinterest_feed ) ) {

			$output .= '<div class="widget" id="dbd-pinterest-widget">';
			$output .= '<div class="header">';
			$output .= '<span></span>';
			$output .= '<a href="'.$instance['dbd_pin_account_url'].'"><img src="http://passets-cdn.pinterest.com/images/about/buttons/follow-me-on-pinterest-button.png" width="169" height="28" alt="Follow Me on Pinterest" /></a>';
			$output .= '</div>';
			$output .= '<ul class="pinterest-list">';

			foreach ( $pinterest_feed as $item ) {

				$pinterest_content = $item->get_content();

				//GET IMAGE SRC
				$img_pattern = "/<img[^>]+\>/i";
				preg_match($img_pattern, $pinterest_content, $matches);
				$src_pattern = '/src=[\'"]?([^\'" >]+)[\'" >]/';
				preg_match($src_pattern, $matches[0], $link);
				$img_src = $link[1];

				//GET TEXTUAL DATA
				$text = strip_tags($pinterest_content);
				$text = substr($text, 0, 20) . ' &hellip;';

				$pinterest_content = str_replace( '<a', '<a target="_blank"', $pinterest_content );
				$output .= '<li>';
				$output .= '<a class="pin-img" href="'. esc_url( $item->get_permalink() ) .'" title="Posted '.$item->get_date('j F Y | g:i a') .'">';
				$output .= '<img src="'.$img_src.'" alt="'.$text.'" />';
				$output .= '</a>';
				$output .= '<p>';
				$output .= $text;
				$output .= '</p>';
				$output .= '</li>';

			}

			$output .= '</ul>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';

			echo $output;

		}

	}

}
add_action( 'widgets_init', create_function('', 'return register_widget("DBD_Pinterest_Widget");') );
?>