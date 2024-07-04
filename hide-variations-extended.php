<?php
/*
Plugin Name: Hide Variations by User Roles Extended
Plugin URI: https://mustbesocial.co.uk
Description: Extends the functionality of the Hide Variations by User Roles plugin to support multiple roles.
Author: MBS
Version: 1.0.0
Author URI: https://mustbesocial.co.uk
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Check if the original plugin is active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'hide-variations-by-user-roles-for-woocommerce/variations-for-roles.php' ) ) {

	class ADDF_VF_Roles_Extended {

		public function __construct() {
			add_filter( 'woocommerce_variation_is_active', array( $this, 'addf_vf_roles_variation_is_active' ), 10, 2 );
			add_filter( 'woocommerce_variation_is_visible', array( $this, 'addf_vf_roles_variation_is_visible' ), 10, 2 );
		}

		public function addf_vf_roles_variation_is_active( $active, $variation ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;
			} else {
				$user_roles = array( 'guest' );
			}
			$addf_vf_roles_opt = get_post_meta( $variation->get_id(), 'addf_vf_roles_restriction_type', true );
			$addf_vf_roles_sel_users = get_post_meta( $variation->get_id(), 'addf_vf_roles_select_roles', true );
			
			foreach ( $user_roles as $role ) {
				if ( in_array( $role, (array) $addf_vf_roles_sel_users ) ) {
					if ( 'unselectable' === $addf_vf_roles_opt ) {
						return false;
					}
				}
			}
			return $active;
		}

		public function addf_vf_roles_variation_is_visible( $visible, $variation_id ) {
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$user_roles = $user->roles;
			} else {
				$user_roles = array( 'guest' );
			}
			$addf_vf_roles_opt = get_post_meta( $variation_id, 'addf_vf_roles_restriction_type', true );
			$addf_vf_roles_sel_users = get_post_meta( $variation_id, 'addf_vf_roles_select_roles', true );
			
			foreach ( $user_roles as $role ) {
				if ( in_array( $role, (array) $addf_vf_roles_sel_users ) ) {
					if ( 'completely_hide' === $addf_vf_roles_opt ) {
						return false;
					}
				}
			}
			return $visible;
		}
	}

	new ADDF_VF_Roles_Extended();
} else {
	add_action( 'admin_notices', function() {
		echo '<div class="notice notice-error"><p>Hide Variations by User Roles Extended requires the Hide Variations by User Roles plugin to be active.</p></div>';
	} );
}
