<?php
add_action( 'admin_init', 'Zhngrupa_Expired_service_Manager_Activation' );

function Zhngrupa_Expired_service_Manager_Activation() {
    // Check is Woocommerce installed and active
    if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) OR !class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'Zhngrupa_Expired_service_Manager_Show_Admin_Notice_Activation_Problem' );
    }
    else {
        add_action( 'admin_notices', 'Zhngrupa_Expired_service_Manager_Show_Admin_Notice_Activation_Go_Through' );
    }
}

function Zhngrupa_Expired_service_Manager_Show_Admin_Notice_Activation_Problem() {
    $class = 'notice notice-error';
    $message = __( 'ZHNGRUPA Expired Service Manager require Woocommerce to working properly. Please install or activate Woocommerce to start using plugin.', 'sample-text-domain' );

    return printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

function Zhngrupa_Expired_service_Manager_Show_Admin_Notice_Activation_Go_Through() {
    $class = 'notice notice-success';
    $message = __( 'Thanks for installing ZHNGRUPA Expired Service Manager. For fast start, please <a href="tools.php?page=zhngrupa-expired-service">setup basic plugin configuration</a>.', 'sample-text-domain' );

    // Check is required options was set.
    if( !get_option( 'zhngrupa_expired_service' ) ) {
        return printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }
}