<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Remove Meta Boxes
 * Plugin URI:        https://www.kntnt.com/
 * Description:       Removes meta boxes.
 * Version:           1.0.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Kntnt\Remove_Meta_Boxes;

defined( 'ABSPATH' ) && new Plugin;

class Plugin {

    private $hide_meta_boxes = null;

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'run' ] );
    }

    public function run() {

        $this->hide_meta_boxes = apply_filters( 'kntnt-remove-meta-boxes', [
            'dashboard' => [
                'dashboard_primary',
                'dashboard_quick_press',
                'dashboard_right_now',
                'welcome_panel',
            ],
            'post' => [
                'commentstatusdiv',
                'postcustom',
                'revisionsdiv',
                'slugdiv',
                'trackbacksdiv',
            ],
        ] );

        if ( isset( $this->hide_meta_boxes['dashboard'] ) && ( $key = array_search( 'welcome_panel', $this->hide_meta_boxes['dashboard'] ) ) !== false ) {
            remove_action( 'welcome_panel', 'wp_welcome_panel' );
            unset( $this->hide_meta_boxes['dashboard'][ $key ] );
        }

        add_action( 'do_meta_boxes', [ $this, 'remove_meta_boxes' ], 5, 2 );

    }

    public function remove_meta_boxes( $screen, $context ) {
        if ( isset( $this->hide_meta_boxes[ $screen ] ) ) {
            foreach ( $this->hide_meta_boxes[ $screen ] as $meta_box ) {
                remove_meta_box( $meta_box, $screen, $context );
            }
        }
    }

}
