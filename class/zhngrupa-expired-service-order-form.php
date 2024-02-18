<?php

class Zhngrupa_Membership_MetaBox {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'zhngrupa_order_meta_box'));
        add_action('save_post_shop_order', array($this, 'save_order_meta'), 10, 2);
    }

    public function zhngrupa_order_meta_box() {
        add_meta_box('zhngrupa_membership_box', 'ZHNGRUPA Membership', array($this, 'zhngrupa_meta_box_content'), 'shop_order', 'normal', 'high');
    }

    public function zhng_order_meta_box($post) {
        add_meta_box('zhngrupa_membership_box', 'ZHNGRUPA Membership', array($this, 'zhngrupa_meta_box_content'), 'shop_order', 'normal', 'high');
    }

    public function zhngrupa_meta_box_content($post) {
        echo 'ZHNGrupa meta box';
        // Tutaj dodaj pola formularza zgodnie z potrzebami
    }

    public function save_order_meta($post_id, $post) {
        // Tutaj obsłuż zapisywanie danych, jeśli to konieczne
    }
}