<?php
/**
 * Access manager js template.
 *
 * phpcs:ignoreFile:Squiz.PHP.EmbeddedPhp
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?> id="vc_role_access_manager_script">
	(function ( $ ) {
		var _localCapabilities, _check, _groupAccessRules, _shortcodesPartSet, _mergedCaps;
		_localCapabilities = <?php echo wp_json_encode( vc_user_roles_get_all() ); ?>;
		_shortcodesPartSet = <?php echo vc_user_access()->part( 'shortcodes' )->checkStateAny( true, null )->get() ? 'true' : 'false'; ?>;
		_groupAccessRules = <?php echo wp_json_encode( array_merge( array( 'current_user' => wp_get_current_user()->roles ), (array) vc_settings()->get( 'groups_access_rules' ) ) ); ?>;
		_mergedCaps = <?php echo wp_json_encode( vc_user_access()->part( 'shortcodes' )->getMergedCaps() ); ?>;
		_check = function ( part, rule, custom, not_check_state ) {
			<?php
			// phpcs:ignore
			if ( current_user_can( 'administrator' ) ) {
				echo 'return rule==="disabled_ce_editor" ? false : true;';
			}
			?>
			var state, partObj, findRule;

			partObj = _.isUndefined( _localCapabilities[ part ] ) ? {} : _localCapabilities[ part ];
			rule = vc_user_access().updateMergedCaps( rule );
			if ( ! not_check_state ) {
				state = _.isUndefined( partObj.state ) ? false : partObj.state; // if we don't have state it is incorrect part
				if ( null === state ) {
					return true;
				} else if ( _.isBoolean( state ) ) {
					return state;
				}
			}

			findRule = (
				_.isUndefined( partObj.capabilities ) ||
				_.isUndefined( partObj.capabilities[ rule ] )
			) ? false : partObj.capabilities[ rule ];

			return _.isBoolean( findRule ) ? findRule : findRule === custom;
		};
		// global function
		window.vc_user_access = function () {
			return {
				editor: function ( editor ) {
					return this.partAccess( editor );
				},
				partAccess: function ( editor ) {
					return <?php
					if ( is_multisite() && is_super_admin() ) {
						echo 'true;';
					// phpcs:ignore
					} elseif ( current_user_can( 'administrator' ) ) {
						echo 'true;';
					} else {
						?>!_.isUndefined( _localCapabilities[ editor ] ) && false !== _localCapabilities[ editor ][ 'state' ];
					<?php } ?>
				},
				check: function ( part, rule, custom, not_check_state ) {
					return _check( part, rule, custom, not_check_state );
				},
				getState: function ( part ) {
					var state, partObj;

					partObj = _.isUndefined( _localCapabilities[ 'shortcodes' ] ) ? {} : _localCapabilities[ part ];
					state = _.isUndefined( partObj.state ) ? false : partObj.state;

					return state;
				},
				shortcodeAll: function ( shortcode ) {
					if ( ! _shortcodesPartSet ) {
						return this.shortcodeValidateOldMethod( shortcode );
					}
					var state = this.getState( 'shortcodes' );
					if ( state === 'edit' ) {
						return false;
					}
					return _check( 'shortcodes', shortcode + '_all' );
				},
				shortcodeEdit: function ( shortcode ) {
					if ( ! _shortcodesPartSet ) {
						return this.shortcodeValidateOldMethod( shortcode );
					}

					var state = this.getState( 'shortcodes' );
					if ( state === 'edit' ) {
						return true;
					}
					return _check( 'shortcodes', shortcode + '_all' ) || _check( 'shortcodes', shortcode + '_edit' );
				},
				shortcodeValidateOldMethod: function ( shortcode ) {
					<?php
					if ( is_multisite() && is_super_admin() ) {
						echo 'return true;';
                    // phpcs:ignore
					} elseif ( current_user_can( 'administrator' ) ) {
						echo 'return true;';
					}
					?>
					if ( 'vc_row' === shortcode ) {
						return true;
					}
					return _.every( _groupAccessRules.current_user, function ( role ) {
						return ! (! _.isUndefined( _groupAccessRules[ role ] ) && ! _.isUndefined( _groupAccessRules[ role ][ 'shortcodes' ] ) && _.isUndefined( _groupAccessRules[ role ][ 'shortcodes' ][ shortcode ] ));
					} );
				},
				updateMergedCaps: function ( rule ) {
					if ( undefined !== _mergedCaps[ rule ] ) {
						return _mergedCaps[ rule ];
					}
					return rule;
				},
				isBlockEditorIsEnabled: function () {
					return <?php echo function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( get_post_type() ) ? 'true' : 'false'; ?>;
				}
			};
		};
	})( window.jQuery );
</<?php echo esc_attr( $custom_tag ); ?>>
