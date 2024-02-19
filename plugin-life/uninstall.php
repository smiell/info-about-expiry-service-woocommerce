<?php

function Zhngrupa_Expired_service_Manager_Uninstall() {
    // Delate Plugin options form DB
    delate_site_option( 'zhngrupa_expired_service' );
}