<?php
/**
 * Plugin Name: JN Portfolio Post Type
 * Plugin URI: https://github.com/jessengatai/jn-portfolio-post-type
 * Description: Enables a portfolio post type for use in my (Jesse Ngatai) compatible themes
 * Version: 1.4
 * Author: Jesse Ngatai
 * Author URI: https://www.jessengatai.com
 */

/**
 * CHANGELOG
 * 10/04/2017 - Version 1.4
 * - renamed to prefix and class from Visualkicks to JN
 * - cleaned up php for better documenation
 *
 * 09/10/2015 - Version 1.3
 * - constructor method for WP_Widget is deprecated since 4.3.0 (use __construct instead)
 *
 * 31/12/2013 - Version 1.2
 * - portfolio-project is now portfolio-category
 * - recent portfolio posts widget is now built into the plugin
 *
 * 21/10/2013 - Version 1.1
 * 1.1 (21/10/2013)
 * - portfolio post type labels have been updated
 * - taxonomy labels have been udpated
 * - fixed an issue where the portfolio tax filter would return notices
 */

if ( ! class_exists( 'JN_Portfolio' ) ) {
	class JN_Portfolio {



		/**
		 * Our constructor for the portfolio plugin
		 *
		 * @since 1.4
		 * @return void
		 */
		function __construct() {

			// register
			register_activation_hook( __FILE__, array( &$this, 'plugin_activation' ) );
			add_action( 'init', array( &$this, 'portfolio_init' ) );

			// dashboard count
			add_action( 'right_now_content_table_end', array( &$this, 'add_portfolio_counts' ) );

			// taxonomy filter in admin
			add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );

		}



		/**
		 * Flush the plugin rules on activation
		 *
		 * @since 1.4
		 * @return void
		 */
		function plugin_activation() {
			$this->portfolio_init();
			flush_rewrite_rules();
		}



		/**
		 * Register our custom post type and it's taxonomies
		 *
		 * @since 1.4
		 * @return void
		 */
		function portfolio_init() {

			// labels
			$labels = array(
				'name' => __( 'Portfolios', 'jn-text-domain' ),
				'singular_name' => __( 'Portfolio Post', 'jn-text-domain' ),
				'all_items' => __( 'All Portfolio Posts', 'jn-text-domain' ),
				'add_new' => __( 'Add New', 'jn-text-domain' ),
				'add_new_item' => __( 'Add New Portfolio Post', 'jn-text-domain' ),
				'edit_item' => __( 'Edit Portfolio Post', 'jn-text-domain' ),
				'new_item' => __( 'Add New', 'jn-text-domain' ),
				'view_item' => __( 'View Portfolio Post', 'jn-text-domain' ),
				'search_items' => __( 'Search Portfolio Posts', 'jn-text-domain' ),
				'not_found' => __( 'No portfolio items found', 'jn-text-domain' ),
				'not_found_in_trash' => __( 'No portfolio items found in trash', 'jn-text-domain' )
			);

			// settings
			$args = array(
		    'labels' => $labels,
		    'public' => true,
				'supports' => array( 'title', 'editor', 'thumbnail', 'author','custom-fields', 'post-formats'),
				'capability_type' => 'post',
				'rewrite' => array("slug" => "portfolio"),
				'menu_position' => 5,
				'has_archive' => true
			);

			// filters
			$args = apply_filters('vk_args', $args);

			// register post type
			register_post_type( 'portfolio', $args );

			// category labels
	    $taxonomy_portfolio_category_labels = array(
				'name' => __( 'Portfolio Categories', 'jn-text-domain' ),
				'singular_name' => __( 'Portfolio Category', 'jn-text-domain' ),
				'search_items' => __( 'Search Portfolio Categories', 'jn-text-domain' ),
				'popular_items' => __( 'Popular Portfolio Categories', 'jn-text-domain' ),
				'all_items' => __( 'All Portfolio Categories', 'jn-text-domain' ),
				'parent_item' => __( 'Parent Portfolio Category', 'jn-text-domain' ),
				'parent_item_colon' => __( 'Parent Portfolio Category:', 'jn-text-domain' ),
				'edit_item' => __( 'Edit Portfolio Category', 'jn-text-domain' ),
				'update_item'	=> __( 'Update Portfolio Category', 'jn-text-domain' ),
				'add_new_item' => __( 'Add New Portfolio Category', 'jn-text-domain' ),
				'new_item_name' => __( 'New Portfolio Category Name', 'jn-text-domain' ),
				'separate_items_with_commas' => __( 'Separate portfolio categories with commas', 'jn-text-domain' ),
				'add_or_remove_items' => __( 'Add or remove portfolio categories', 'jn-text-domain' ),
				'choose_from_most_used' => __( 'Choose from the most used portfolio categories', 'jn-text-domain' ),
				'menu_name' => __( 'Categories', 'jn-text-domain' ),
	    );

			// category settings
	    $taxonomy_portfolio_category_args = array(
				'labels' => $taxonomy_portfolio_category_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => array("slug" => "portfolio-category"),
				'query_var' => true,
	    );

			// register taxonomy
		  register_taxonomy( 'portfolio_category', array( 'portfolio' ), $taxonomy_portfolio_category_args );

			// filter labels
	    $taxonomy_portfolio_filter_labels = array(
				'name' => __( 'Portfolio Filters', 'jn-text-domain' ),
				'singular_name' => __( 'Portfolio Filter', 'jn-text-domain' ),
				'search_items' => __( 'Search Portfolio Filters', 'jn-text-domain' ),
				'popular_items' => __( 'Popular Portfolio Filters', 'jn-text-domain' ),
				'all_items' => __( 'All Portfolio Filters', 'jn-text-domain' ),
				'parent_item' => __( 'Parent Portfolio Filter', 'jn-text-domain' ),
				'parent_item_colon' => __( 'Parent Portfolio Filter:', 'jn-text-domain' ),
				'edit_item' => __( 'Edit Portfolio Filter', 'jn-text-domain' ),
				'update_item' => __( 'Update Portfolio Filter', 'jn-text-domain' ),
				'add_new_item' => __( 'Add New Portfolio Filter', 'jn-text-domain' ),
				'new_item_name' => __( 'New Portfolio Filter Name', 'jn-text-domain' ),
				'separate_items_with_commas' => __( 'Separate portfolio filters with commas', 'jn-text-domain' ),
				'add_or_remove_items' => __( 'Add or remove portfolio filters', 'jn-text-domain' ),
				'choose_from_most_used' => __( 'Choose from the most used portfolio filters', 'jn-text-domain' ),
				'menu_name' => __( 'Filters', 'jn-text-domain' ),
	    );

			// filter settings
	    $taxonomy_portfolio_filter_args = array(
				'labels' => $taxonomy_portfolio_filter_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => true,
				'query_var' => true,
	    );

			// register taxonomy
		  register_taxonomy( 'portfolio_filter', array( 'portfolio' ), $taxonomy_portfolio_filter_args );

		}



		/**
		 * Add the portfolio count number in the admin UI top bar
		 *
		 * @since 1.4
		 * @return void
		 */
		function add_portfolio_counts() {

			// function conditions
			if ( !post_type_exists( 'portfolio' ) ) { return; }

			// count portfolios
	    $num_posts = wp_count_posts( 'portfolio' );

	    // i18n count
	    $num = number_format_i18n( $num_posts->publish );

			// text vars
	    $text = _n( 'Portfolio', 'Portfolios', intval($num_posts->publish) );

	    // admin links
	    if ( current_user_can( 'edit_posts' ) ) {
	    	$num = "<a href='edit.php?post_type=portfolio'>$num</a>";
	    	$text = "<a href='edit.php?post_type=portfolio'>$text</a>";
	    }

	    // markup
	    echo '<td class="first b b-portfolio">' . $num . '</td>';
	    echo '<td class="t portfolio">' . $text . '</td>';
	    echo '</tr>';

	    // pending conditions
	    if ($num_posts->pending > 0) {

	    	// pending i18n count
	      $num = number_format_i18n( $num_posts->pending );

	      // pending text vars
	      $text = _n( 'Portfolio Item Pending', 'Portfolio Items Pending', intval($num_posts->pending) );

	        // admin links
	        if ( current_user_can( 'edit_posts' ) ) {
	        	$num = "<a href='edit.php?post_status=pending&post_type=portfolio'>$num</a>";
	        	$text = "<a href='edit.php?post_status=pending&post_type=portfolio'>$text</a>";
	        }

	        // markup
	        echo '<td class="first b b-portfolio">' . $num . '</td>';
	        echo '<td class="t portfolio">' . $text . '</td>';
	        echo '</tr>';

	    }

		}


		/**
		 * Admin Taxonomy Filters (original from pippins plugins)
		 *
		 * @since 1.4
		 * @return void
		 */
		function add_taxonomy_filters() {

			global $typenow;

			// taxonomy filter array
			$taxonomies = array('portfolio_category');

			// current post type
			if( $typenow == 'portfolio' ){
				foreach ($taxonomies as $tax_slug) {

					// tax
					$tax_obj = get_taxonomy($tax_slug);
					$tax_name = $tax_obj->labels->name;

					// currently selected term slug
					if(isset($_GET[$tax_slug])) {
					   $url_tax = $_GET[$tax_slug];
					} else {
						$url_tax='';
					}

					// terms
					$terms = get_terms($tax_slug);

					// if in business
					if(count($terms) > 0) {
						echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
							echo "<option value=''>Show All $tax_name</option>";
							// selects
							foreach ($terms as $term) {
								if( $url_tax == $term->slug ) {
									echo '<option value="'.$term->slug.'" selected="selected">';
								} else {
									echo '<option value="'.$term->slug.'">';
								}
								echo $term->name.' ('.$term->count.')';
								echo '</option>';
							}
						echo "</select>";
					} // out of business

				}
			}
		}

	} // end class JN_Portfolio

	// create a new instance of the above class
	new JN_Portfolio;

}



/**
 * Recent Portfolio Posts Widget Setup
 *
 * @since 1.4
 * @return void
 */

if ( !function_exists( 'jn_recent_portfolio_posts' ) ) {
	function jn_recent_portfolio_posts() {
		register_widget( 'JN_Recent_Portfolio_Posts' );
	}
	add_action( 'widgets_init', 'jn_recent_portfolio_posts' );
}



/**
 * [JN_Recent_Portfolio_Posts description]
 */
if ( ! class_exists( 'jn_recent_portfolio_posts' ) ) {
	class jn_recent_portfolio_posts extends WP_Widget {



		/**
		 * Create / setup the widget params
		 *
		 * @since 1.4
		 * @return void
		 */
		function JN_Recent_Portfolio_Posts() {
			// settings
			$widget_ops = array(
		    'classname' => 'jn_recent_portfolio_posts',
		    'description' => __('The most recent portfolios posts on your site', 'jn-text-domain'),
			);
			// control
			$control_ops = array(
		    'width' => 250,
		    'height' => 350,
		    'id_base' => 'jn_recent_portfolio_posts',
			);
			// create
			parent::__construct( 'jn_recent_portfolio_posts', __('Recent Portfolio Posts', 'jn-text-domain'), $widget_ops, $control_ops );
		}



		/**
		 * Render widget output
		 *
		 * @since 1.4
		 * @param array $args     The args passed via sidebar registration
		 * @param array $instance The settings of the widget
		 * @return void
		 */
		function widget( $args, $instance ) {
			extract( $args );

			// variables
			$title = apply_filters('widget_title', $instance['title'] );
			$number = $instance['number'];

			// widget before
			echo $before_widget;

			// widget title
			if ( $title ) { echo $before_title . $title . $after_title; } ?>

				<ul>
					<?php

					// query args
					$portfolio_posts = array(
						'post_type'         => 'portfolio',
						'posts_per_page'    => $number,
						'post_status'       => 'publish',
						'orderby'           => 'date',
						'order'             => 'DESC',
					);

					// the query
					$wp_query = new WP_Query($portfolio_posts);

					// the loop
					if ( $wp_query->have_posts() ) {
						while ( $wp_query->have_posts() ) {
							$wp_query->the_post(); ?>

							<li>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</li>

						<?php } // end if
					} // end while

					// reset the post query
					wp_reset_query();

					?>
				</ul>

			<?php // widget after
			echo $after_widget;

		}



		/**
		 * Update the widget
		 *
		 * @since 1.4
		 * @param array $new_instance The new settings passed setuping the widget
		 * @param array $old_instance The old settings that we will replace
		 * @return void
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			return $instance;
		}



		/**
		 * The form html for the widget settings
		 *
		 * @since 1.4
		 * @param  [type] $instance [description]
		 * @return [type]           [description]
		 */
		function form( $instance ) {

			// defaults
			$defaults = array(
				'title' => 'Recent Portfolio Posts',
				'number' => '99',
			);

			$instance = wp_parse_args( (array) $instance, $defaults );

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'jn-text-domain') ?></label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number of posts to show:', 'jn-text-domain') ?></label>
				<input size="3" type="text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" />
			</p>
		<?php
	} // end form()

	} // end class
} // end class check

?>
