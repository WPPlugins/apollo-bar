<?php
/*
Plugin Name: Apollo Bar
Plugin URI: http://code-art.hu/apollo-bar/
Description: Apollo Bar is a simple announcements plugin that allows you to create colorful notification bars for your website.
Version: 1.2
Author: codee47
Author URI: http://gergoszalai.com
License: GPL2
*/

/*  Copyright 2012  codee47  (email : support@code-art.hu)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Global Variables

$apb_plugin_name = 'Apollo Bar';
$apb_options = get_option( 'apb_settings' );

// Define version number
define( 'APB_VERSION', '1.0.2' );


// Define Plugin Path

define( 'APOLLO_BAR_PATH', plugin_dir_url( __FILE__ ) );


// Include Options Page

include dirname( __FILE__ ) . '/includes/apollo-bar-options.php';


// Enable Localization

function apb_plugin_setup() {
	load_plugin_textdomain( 'apollo-bar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'apb_plugin_setup' );


// Creating Announcement Custom Post Type

	function apb_register_announcements() {
		$labels = array(
			'name' => __( 'Announcements', 'apollo-bar' ),
			'singular_name' => __( 'Announcement', 'apollo-bar' ),
			'add_new' => __( 'Add New', 'apollo-bar' ),
			'add_new_item' => __( 'Add New Announcement', 'apollo-bar' ),
			'all_items' => __( 'All items', 'apollo-bar' ),
			'edit_item' => __( 'Edit Announcement', 'apollo-bar' ),
			'new_item' => __( 'New Announcement', 'apollo-bar' ),
			'view_item' => __( 'View Announcement', 'apollo-bar' ),
			'search_items' => __( 'Search Announcements', 'apollo-bar' ),
			'not_found' =>  __( 'No Announcements found', 'apollo-bar' ),
			'not_found_in_trash' => __( 'No Announcements found in Trash', 'apollo-bar' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Apollo Bar', 'apollo-bar' )
		);
		$args = array(
			'labels' => $labels,
			'singular_label' => __( 'Announcement', 'announcements', 'apollo-bar' ),
			'public' => true,
			'capability_type' => 'post',
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ),
		);
		register_post_type( 'announcements', $args );
	}
	add_action( 'init', 'apb_register_announcements' );


// Add Columns to Announcements Custom Post Type

	// Add Columns
	add_filter( 'manage_edit-announcements_columns', 'apb_edit_announcements_columns' ) ;
	function apb_edit_announcements_columns( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'apollo-bar' ),
			'apb_start_date' => __( 'Start Date', 'apollo-bar' ),
			'apb_end_date' => __( 'End Date', 'apollo-bar' ),
			'date' => __( 'Date', 'apollo-bar' )
		);
		return $columns;
	}

	// Add Columns Content
	add_action( 'manage_announcements_posts_custom_column', 'apb_manage_announcements_columns', 10, 2 );
	function apb_manage_announcements_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {

			/* If displaying the 'start date' column. */
			case 'apb_start_date' :

				/* Get the post meta. */
				$apb_start_date = get_post_meta( $post_id, 'apb_start_date', true );

				/* If no start date is found, output a default message. */
				if ( empty( $apb_start_date ) )
					echo '<span style="color: #de5959;font-weight:bold;">' . __( 'Not selected!', 'apollo-bar' ) . '</span>';

				/* If there is a start date, displaying the 'start date' */
				else
					echo $apb_start_date;

				break;

			/* If displaying the 'end date' column. */
			case 'apb_end_date' :

				/* Get the post meta. */
				$apb_end_date = get_post_meta( $post_id, 'apb_end_date', true );

				/* If no end date is found, output a default message. */
				if ( empty( $apb_end_date ) )
					echo '<span style="color: #de5959;font-weight:bold;">' . __( 'Not selected!', 'apollo-bar' ) . '</span>';

				/* If there is a end date, displaying the 'end date' */
				else
					echo $apb_end_date;

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}


// Add Menu Icon

	function apollo_bar_icons() {
		echo '<style type="text/css" media="screen">#menu-posts-announcements .wp-menu-image {background: url( '. plugins_url( 'images/apollo-bar-small.png', __FILE__ ) .' ) no-repeat 6px -17px !important;}#menu-posts-announcements:hover .wp-menu-image, #menu-posts-announcements.wp-has-current-submenu .wp-menu-image {background-position:6px 7px !important;}</style>';
	}
	add_action( 'admin_head', 'apollo_bar_icons' );

// Add Custom Screen Icon

	function apollo_bar_screen_icon() {
		$post_type = get_current_screen()->post_type;

		if ( 'announcements' == $post_type ) {

			echo '<style type="text/css">';
			echo '#icon-edit { background: url('. plugins_url( 'images/apollo-bar-big.png', __FILE__ ) .'); width: 32px; height: 32px; }';
			echo '</style>';

		}
	}
	add_action( 'admin_head', 'apollo_bar_screen_icon' );


// Creating Custom Meta Boxes

	// Adding Meta Box
	function apb_add_metabox() {
		add_meta_box( 'apb_metabox_id', __( 'Scheduling', 'apollo-bar' ), 'apb_metabox', 'announcements', 'side', 'high' );
	}
	add_action( 'add_meta_boxes', 'apb_add_metabox' );

	// Adding the Fields
	function apb_metabox( $post ) {
		$values = get_post_custom( $post->ID );
		$start_date = isset( $values['apb_start_date'] ) ? esc_attr( $values['apb_start_date'][0] ) : '';
		$end_date = isset( $values['apb_end_date'] ) ? esc_attr( $values['apb_end_date'][0] ) : '';
		wp_nonce_field( 'apb_metabox_nonce', 'metabox_nonce' );
	?>
		<p>
			<label for="start_date"><?php _e( 'Start date', 'apollo-bar' ); ?></label>
			<input type="text" name="apb_start_date" id="apb_start_date" value="<?php echo $start_date; ?>" />
		</p>
		<p>
			<label for="end_date"><?php _e( 'End date', 'apollo-bar' ); ?></label>
			<input type="text" name="apb_end_date" id="apb_end_date" value="<?php echo $end_date; ?>" />
		</p>
	<?php
	}


// Saving Meta Box Data

	function apb_metabox_save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		if ( !isset( $_POST['metabox_nonce'] ) || !wp_verify_nonce( $_POST['metabox_nonce'], 'apb_metabox_nonce' ) )
			return $post_id;
		if ( !current_user_can( 'edit_post' ) )
			return $post_id;
		// Make sure data is set
		if ( isset( $_POST['apb_start_date'] ) ) {
			$valid = 0;
			$old_value = get_post_meta( $post_id, 'apb_start_date', true );
			if ( $_POST['apb_start_date'] != '' ) {
				$date = $_POST['apb_start_date'];
				$date = explode( '-', (string) $date );
				$valid = checkdate( $date[1], $date[2], $date[0] );
			}
			if ( $valid )
				update_post_meta( $post_id, 'apb_start_date', $_POST['apb_start_date'] );
			elseif ( !$valid && $old_value )
				update_post_meta( $post_id, 'apb_start_date', $old_value );
			else
				update_post_meta( $post_id, 'apb_start_date', '' );
		}
		if ( isset( $_POST['apb_end_date'] ) ) {
			if ( $_POST['apb_start_date'] != '' ) {
				$old_value = get_post_meta( $post_id, 'apb_end_date', true );
				$date = $_POST['apb_end_date'];
				$date = explode( '-', (string) $date );
				$valid = checkdate( $date[1], $date[2], $date[0] );
			}
			if ( $valid )
				update_post_meta( $post_id, 'apb_end_date', $_POST['apb_end_date'] );
			elseif ( !$valid && $old_value )
				update_post_meta( $post_id, 'apb_end_date', $old_value );
			else
				update_post_meta( $post_id, 'apb_end_date', '' );
		}
	}
	add_action( 'save_post', 'apb_metabox_save' );


// Registering JavaScript and CSS Files

	// BackEnd Options Page
	function apb_backend_options_scripts( $hook ) {
		global $apb_options_page;
		global $wp_version;

		if( $hook != $apb_options_page )
			return;

			if ( 3.5 <= $wp_version ) {
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'apb-color-picker-js', plugins_url( 'apollo-bar/js/color-picker.js' , dirname(__FILE__) ) , array( 'jquery', 'wp-color-picker' ), APB_VERSION, true );
			} else {
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_script( 'farbtastic' );
				wp_enqueue_script( 'apb-color-picker-js', plugins_url( 'apollo-bar/js/color-picker.js' , dirname(__FILE__) ) , array( 'jquery', 'wp-color-picker' ), APB_VERSION, true );
			}
	}
	add_action( 'admin_enqueue_scripts', 'apb_backend_options_scripts' );

	// BackEnd Announcement
	function apb_backend_post_scripts( $hook ) {
		global $post;

		if ( ( !isset( $post ) || $post->post_type != 'announcements' ) )
			return;

		wp_enqueue_style( 'jquery-ui-fresh', plugins_url( 'apollo-bar/css/jquery-ui-fresh.css' , dirname(__FILE__) ) );
		wp_enqueue_script( 'apb-date-picker-js', plugins_url( 'apollo-bar/js/date-picker.js' , dirname(__FILE__) ) , array( 'jquery', 'jquery-ui-datepicker' ), APB_VERSION, true );
	}
	add_action( 'admin_enqueue_scripts', 'apb_backend_post_scripts' );

	// FrontEnd
	function apb_frontend_scripts() {
		wp_enqueue_style( 'apb-core-style', plugins_url( 'apollo-bar/css/apollo-bar.css' , dirname(__FILE__) ), APB_VERSION );
		wp_enqueue_script( 'apb-core-js', plugins_url( 'apollo-bar/js/apollo-bar-core.js' , dirname(__FILE__) ) , array( 'jquery' ), APB_VERSION, true );
		wp_enqueue_script( 'cookies', plugins_url( 'apollo-bar/js/jquery.cookie.js' , dirname(__FILE__) ) , array( 'jquery' ), APB_VERSION );
	}
	add_action( 'wp_enqueue_scripts', 'apb_frontend_scripts' );

// Add backgrund color style to head section

	function apb_add_color_to_head() {
		global $apb_options;
		$bgcolor = $apb_options['apollo_bar_color'];
		$textcolor = $apb_options['apollo_bar_textcolor'];
		if( $apb_options['apollo_bar_noisy'] == true ) {
			echo '<style type="text/css">#apollo-bar{background: ' . $bgcolor . ' url(' . APOLLO_BAR_PATH . 'images/noisy-texture.png) !important;}#apollo-bar .apb-message p {color:' . $textcolor . ';}</style>',"\n";
		} else {
			echo '<style type="text/css">#apollo-bar{background-color: ' . $bgcolor . ' !important;}#apollo-bar .apb-message p {color:' . $textcolor . ';}</style>',"\n";
		}
	}
	add_action( 'wp_head', 'apb_add_color_to_head' );

// Displaying Apollo Bar

	function apb_filter_where( $where = '' ) {
		// ...where dates are blank
		$where .= " OR (mt1.meta_key = 'apb_start_date' AND CAST(mt1.meta_value AS CHAR) = '') OR (mt2.meta_key = 'apb_end_date' AND CAST(mt2.meta_value AS CHAR) = '')";
		return $where;
	}
	function apb_display_announcement() {
		global $wpdb, $apb_options;
		$today = date( 'Y-m-d' );
		$args = array(
			'post_type' => 'announcements',
			'posts_per_page' => 0,
			'post_status' => 'publish',
			'meta_key' => 'apb_end_date',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'apb_start_date',
					'value' => $today,
					'compare' => '<=',
				),
				array(
					'key' => 'apb_end_date',
					'value' => $today,
					'compare' => '>=',
				)
			)
		);
		// Add a filter to do complex 'where' clauses...
		add_filter( 'posts_where', 'apb_filter_where' );
		$query = new WP_Query( $args );
		// Take the filter away again so this doesn't apply to all queries.
		remove_filter( 'posts_where', 'apb_filter_where' );
		$announcements = $query->posts;
		if ( $announcements && $apb_options['apollo_bar_display'] == true ) :
?>

	<div id="apollo-bar" class="apb-hidden<?php if( $apb_options['apollo_bar_fixed'] ) : echo ' fixed'; endif; ?>">

		<?php if( $apb_options['apollo_bar_logo_display'] == true ) : ?>

		<div id="promo"><a href="http://www.code-art.hu/apollo-bar" title="<?php _e( 'Download Apollo Bar', 'apollo-bar' ); ?>"><img src="<?php echo plugins_url( '/images/apollo-bar-logo.png', __FILE__ ); ?>" alt="Download Apollo Bar" class="object rocket move-up" /></a></div>

		<?php endif; ?>

		<div class="apb-wrapper">
			<?php if( $apb_options['apollo_bar_close'] == true ) : ?>
				<a class="apb-close" href="#" id="close"><?php _e( 'x', 'apollo-bar' ); ?></a>
			<?php endif; ?>

			<div class="apb-message">
				<?php
					foreach ( $announcements as $announcement ) {
						echo '<div>' . do_shortcode( wpautop( ( $announcement->post_content ) ) ) . '</div>';
					}
				?>
			</div><!-- .apb-message -->
		</div><!-- .apb-wrapper -->
	</div><!-- #apollo-bar -->

<?php 
		endif;
	}
	add_action( 'wp_footer', 'apb_display_announcement' );
