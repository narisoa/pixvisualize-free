<?php

/**
 * PixVisualize licensing (Freemius).
 *
 * Free + Pro plans, 1/3/5 sites. Quotas are enforced by Freemius,
 * never in code. Provides billing vs. feature gates and uninstall cleanup.
 *
 * @package PixVisualize
 */
defined( 'ABSPATH' ) || exit;
/**
 * Pro demo URL (single source of truth, Free + Pro builds).
 * Change here to update every "View Pro demo" button at once.
 */
if ( !defined( 'PIXVISUALIZE_DEMO_URL' ) ) {
    define( 'PIXVISUALIZE_DEMO_URL', 'https://i-naridigit.com/demo/pixvisualize/' );
}
if ( !function_exists( 'pixvisualize_get_demo_url' ) ) {
    /**
     * Filterable Pro demo URL.
     *
     * @return string
     */
    function pixvisualize_get_demo_url() {
        return apply_filters( 'pixvisualize_demo_url', PIXVISUALIZE_DEMO_URL );
    }

}
if ( !function_exists( 'pixvisualize_fs' ) ) {
    // Create a helper function for easy SDK access.
    function pixvisualize_fs() {
        global $pixvisualize_fs;
        if ( !isset( $pixvisualize_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/../vendor/freemius/start.php';
            $pixvisualize_fs = fs_dynamic_init( array(
                'id'               => '39570',
                'slug'             => 'pixvisualize',
                'premium_slug'     => 'pixvisualize-pro',
                'type'             => 'plugin',
                'public_key'       => 'pk_44e83f9419877a06ace791677ad52',
                'is_premium'       => false,
                'premium_suffix'   => 'Pro',
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => true,
                'menu'             => array(
                    'slug'    => 'pixvisualize',
                    'support' => false,
                    'pricing' => true,
                ),
                'is_live'          => true,
            ) );
        }
        return $pixvisualize_fs;
    }

    // Init Freemius.
    pixvisualize_fs();
    // Signal that SDK was initiated.
    do_action( 'pixvisualize_fs_loaded' );
}
if ( !function_exists( 'pixvisualize_is_pro' ) ) {
    /**
     * Licensed for Pro (billing). True with an active license, even on
     * the Free build before Pro is installed. Billing only — never gates features.
     *
     * @return bool
     */
    function pixvisualize_is_pro() {
        if ( !function_exists( 'pixvisualize_fs' ) ) {
            return false;
        }
        $fs = pixvisualize_fs();
        return $fs && $fs->can_use_premium_code();
    }

}
if ( !function_exists( 'pixvisualize_has_premium_code' ) ) {
    /**
     * Whether the active build is the premium package.
     *
     * @return bool
     */
    function pixvisualize_has_premium_code() {
        if ( !function_exists( 'pixvisualize_fs' ) ) {
            return false;
        }
        $fs = pixvisualize_fs();
        return $fs && $fs->is_premium();
    }

}
if ( !function_exists( 'pixvisualize_can_use_pro' ) ) {
    /**
     * Pro features available (active license + premium code).
     * Single source of truth for feature gating.
     *
     * @return bool
     */
    function pixvisualize_can_use_pro() {
        if ( !function_exists( 'pixvisualize_fs' ) ) {
            return false;
        }
        $fs = pixvisualize_fs();
        if ( !$fs ) {
            return false;
        }
        if ( is_callable( array($fs, 'can_use_premium_code__premium_only') ) ) {
            return (bool) $fs->can_use_premium_code__premium_only();
        }
        return (bool) ($fs->is_premium() && $fs->can_use_premium_code());
    }

}
if ( !function_exists( 'pixvisualize_needs_premium_install' ) ) {
    /**
     * Licensed but still on the Free build: Pro install required.
     *
     * @return bool
     */
    function pixvisualize_needs_premium_install() {
        if ( !function_exists( 'pixvisualize_is_pro' ) || !function_exists( 'pixvisualize_has_premium_code' ) ) {
            return false;
        }
        return pixvisualize_is_pro() && !pixvisualize_has_premium_code();
    }

}
if ( !function_exists( 'pixvisualize_get_plan' ) ) {
    /**
     * Current plan: 'pro' when Pro features are usable, 'free' otherwise.
     *
     * @return string
     */
    function pixvisualize_get_plan() {
        if ( function_exists( 'pixvisualize_can_use_pro' ) ) {
            return ( pixvisualize_can_use_pro() ? 'pro' : 'free' );
        }
        return ( pixvisualize_is_pro() ? 'pro' : 'free' );
    }

}
if ( function_exists( 'pixvisualize_fs' ) ) {
    pixvisualize_fs()->add_filter(
        'is_submenu_visible',
        function ( $is_visible, $submenu_id ) {
            if ( 'contact' === $submenu_id || 'support' === $submenu_id ) {
                return false;
            }
            return $is_visible;
        },
        10,
        2
    );
}
/**
 * Uninstall cleanup via Freemius (replaces uninstall.php).
 * Freemius rejects uninstall.php at deploy time; cleanup runs on
 * `after_uninstall` so it stays tracked. Works in Free and Pro builds.
 */
function pixvisualize_after_uninstall_cleanup() {
    delete_option( 'pixvisualize' );
    delete_option( 'pixvisualize_block' );
}

if ( function_exists( 'pixvisualize_fs' ) ) {
    pixvisualize_fs()->add_action( 'after_uninstall', 'pixvisualize_after_uninstall_cleanup' );
}
function pixvisualize_force_remove_contact_menu() {
    global $submenu;
    $parent_slug = 'pixvisualize';
    if ( isset( $submenu[$parent_slug] ) ) {
        foreach ( $submenu[$parent_slug] as $key => $item ) {
            if ( isset( $item[2] ) && (false !== strpos( $item[2], 'contact' ) || false !== strpos( $item[2], 'support' )) ) {
                unset($submenu[$parent_slug][$key]);
            }
        }
    }
}

add_action( 'admin_menu', 'pixvisualize_force_remove_contact_menu', 999999 );