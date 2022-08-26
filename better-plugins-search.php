<?php
/**
 * Plugin Name: Better Plugins Search
 * Plugin URI: https://LaxMariappan.com
 * Description: Improved plugins search results in WP Admin.
 * Author: Lax Mariappan
 * Version: 1.0.0
 *
 * @package LaxMariappan/Better_Plugins_Search
 */

namespace LaxMariappan\Better_Plugins_Search;

/**
 * Better_Plugins_Search class
 *
 * @author LaxMariappan
 */
class Better_Plugins_Search {
	/**
	 * The only "Better_Plugins_Search" instance.
	 *
	 * @author LaxMariappan
	 * @var Better_Plugins_Search|null
	 */
	private static $instance = null;

	/**
	 * Singleton.
	 *
	 * @author LaxMariappan
	 * @return Better_Plugins_Search
	 */
	public static function get_instance() : Better_Plugins_Search {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialization hooks.
	 *
	 * @author LaxMariappan
	 * @return void
	 */
	public function init() : void {
		add_filter( 'plugins_api_result', array( $this, 'sort_plugin_results' ), 10, 3 );
		add_filter( 'plugins_api_args', array( $this, 'update_search_args' ), 10, 2 );
		// add the action
		add_action( 'admin_enqueue_scripts', array( $this, 'bps_scripts' ) );
	}

	/**
	 * Adding scripts.
	 *
	 * @return void
	 * @since  1.0
	 * @author Lax Mariappan <lax@webdevstudios.com>
	 */
	public function bps_scripts() {
		wp_register_script( 'bps_form', plugin_dir_url( __FILE__ ) . '/bps_form.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'bps_form' );

	}

	/**
	 * Adding arguments based on query.
	 *
	 * @param  Object $args Object of arguments.
	 * @param  String $action Action.
	 * @return $args
	 * @since  1.0
	 * @author Lax Mariappan <lax@webdevstudios.com>
	 */
	public function update_search_args( $args, $action ) {

		if ( 'query_plugins' === $action ) {

			if ( isset( $_GET['sort_by'] ) ) {
				$sort_by = sanitize_text_field( wp_unslash( $_GET['sort_by'] ) );
			} else {
				$sort_by = 'name';
			}
			if ( isset( $_GET['sort_by'] ) ) {
				$order    = sanitize_text_field( wp_unslash( $_GET['sort_by'] ) );
				$order_by = ( 'asc' === $order ) ? SORT_ASC : SORT_DESC;
			} else {
				$order_by = SORT_ASC;
			}

			$args->sort_by  = $sort_by;
			$args->order_by = $order_by;

		}

		return $args;
	}

	/**
	 * Undocumented function
	 *
	 * @param  Object $res Object of results.
	 * @param  String $action Action.
	 * @param  Object $args Object of arguments.
	 * @return $res
	 * @since  1.0
	 * @author Lax Mariappan <lax@webdevstudios.com>
	 */
	public function sort_plugin_results( $res, $action, $args ) {

		if ( 'query_plugins' === $action && $res->info['results'] > 1 ) {
			$plugins_list = $res->plugins;
			$keys         = array_column( $plugins_list, $args->sort_by );
			array_multisort( $keys, $args->order_by, $plugins_list );
		}
		$res->plugins = $plugins_list;

		return $res;
	}
}

Better_Plugins_Search::get_instance()->init();
