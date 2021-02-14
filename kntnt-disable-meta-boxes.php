<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Disable Meta Boxes
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Disables meta boxes.
 * Version:           1.1.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Kntnt\Disable_Meta_Boxes;

defined( 'ABSPATH' ) && new Plugin;

class Plugin {

	private $remove_meta_boxes = null;

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'run' ] );
	}

	public function run() {

		// Array of meta boxes on dashboard and posts. The name of meta boxes
		// are keys and whether they should be removed (true) or just hidden
		// (false) is their values.
		$this->remove_meta_boxes = apply_filters( 'kntnt-remove-meta-boxes', [
			'dashboard' => [
				'welcome_panel' => true,
				'dashboard_primary' => true,
				'dashboard_quick_press' => true,
				'dashboard_right_now' => true,
			],
			'post' => [
				'slugdiv' => false, // If completely removed, edit of permalink will not take effect on saving a post; thus it is better to hide divslug instead of removing it.
				'revisionsdiv' => true,
				'trackbacksdiv' => true,
			],
		] );

		if ( isset( $this->remove_meta_boxes['dashboard'] ) && isset( $this->remove_meta_boxes['dashboard']['welcome_panel'] ) ) {
			remove_action( 'welcome_panel', 'wp_welcome_panel' );
			unset( $this->remove_meta_boxes['dashboard']['welcome_panel'] );
		}

		$hide_meta_boxes = [];
		foreach ( $this->remove_meta_boxes as $screen => $remove_meta_boxes ) {
			foreach ( $remove_meta_boxes as $meta_box => $remove ) {
				if ( ! $remove ) {
					$hide_meta_boxes[ $screen ][] = $meta_box;
					unset( $this->remove_meta_boxes[ $screen ][ $meta_box ] );
				}
				if ( $hide_meta_boxes ) {
					add_filter( 'hidden_meta_boxes', function ( $hidden, $screen ) use ( $hide_meta_boxes ) {
						if ( isset( $hide_meta_boxes[ $screen->id ] ) ) {
							$hidden += $hide_meta_boxes[ $screen->id ];
						}
						return $hidden;
					}, 10, 2 );
				}
			}
		}

		add_action( 'do_meta_boxes', [ $this, 'remove_meta_boxes' ], 5, 2 );

	}

	public function remove_meta_boxes( $screen, $context ) {
		if ( isset( $this->remove_meta_boxes[ $screen ] ) ) {
			foreach ( array_keys( $this->remove_meta_boxes[ $screen ] ) as $meta_box ) {
				remove_meta_box( $meta_box, $screen, $context );
			}
		}
	}

}
