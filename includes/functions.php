<?php
/**
 * Innehåller hjälpfunktioner för Musikarkiv-pluginen.
 */

/**
 * En hjälpfunktion för att göra något.
 *
 * @return void
 */
function my_plugin_function() {
    // Gör något
}

/**
 * Register REST API route for search.
 */
function register_search_route() {
    register_rest_route('musikarkiv/v1', '/search', array(
        'methods' => 'GET',
        'callback' => 'handle_search_request',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_search_route');

/**
 * Register REST API route for artist search.
 */
function register_artist_search_route() {
    register_rest_route('musikarkiv/v1', '/search_artists', array(
        'methods' => 'GET',
        'callback' => 'handle_artist_search_request',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_artist_search_route');

/**
 * Register REST API route for item search.
 */
function register_item_search_route() {
    register_rest_route('musikarkiv/v1', '/search_items', array(
        'methods' => 'GET',
        'callback' => 'handle_item_search_request',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_item_search_route');

/**
 * Handle search request.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function handle_search_request($request) {
    $query = sanitize_text_field($request->get_param('query'));
    $results = Musikarkiv::search_items($query);
    $formatted_results = array_map(function($item) {
        return [
            'id' => $item->getId(),
            'title' => $item->getTitle()
        ];
    }, $results);

    return new WP_REST_Response($formatted_results, 200);
}

/**
 * Handle artist search request.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function handle_artist_search_request($request) {
    $query = sanitize_text_field($request->get_param('query'));
    $results = Musikarkiv::search_artists($query);
    $formatted_results = array_map(function($artist) {
        return [
            'id' => $artist->id,
            'name' => $artist->name
        ];
    }, $results);

    return new WP_REST_Response($formatted_results, 200);
}

/**
 * Handle item search request.
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function handle_item_search_request($request) {
    $query = sanitize_text_field($request->get_param('query'));
    $results = Musikarkiv::search_items($query);
    $formatted_results = array_map(function($item) {
        return [
            'id' => $item->getId(),
            'title' => $item->getTitle(),
            'artist' => $item->getArtist()
        ];
    }, $results);

    return new WP_REST_Response($formatted_results, 200);
}

/**
 * Handle adding artist_id to an item.
 */
function add_artist_id_to_item() {
    if (current_user_can('edit_others_posts') && isset($_POST['item_id']) && isset($_POST['artist_id'])) {
        global $wpdb;
        $item_id = intval($_POST['item_id']);
        $artist_id = intval($_POST['artist_id']);
        $table_name = $wpdb->prefix . 'musikarkiv_inventory';
        $result = $wpdb->update($table_name, array('artistID' => $artist_id), array('id' => $item_id));

        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_add_artist_id_to_item', 'add_artist_id_to_item');
add_action('wp_ajax_nopriv_add_artist_id_to_item', 'add_artist_id_to_item');

/**
 * Handle linking an artist to an item.
 */
function link_artist_to_item() {
    if (current_user_can('edit_others_posts') && isset($_POST['item_id']) && isset($_POST['artist_id'])) {
        global $wpdb;
        $item_id = intval($_POST['item_id']);
        $artist_id = intval($_POST['artist_id']);
        $table_name = $wpdb->prefix . 'musikarkiv_artist_inventory';
        
        // Kontrollera om länken redan finns
        $existing_link = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE artist_id = %d AND inventory_id = %d", $artist_id, $item_id));
        
        if ($existing_link == 0) {
            $result = $wpdb->insert($table_name, array('artist_id' => $artist_id, 'inventory_id' => $item_id), array('%d', '%d'));

            if ($result !== false) {
                error_log("Successfully linked item $item_id to artist $artist_id");
                wp_send_json_success();
            } else {
                $wpdb_error = $wpdb->last_error;
                error_log("Failed to link item $item_id to artist $artist_id. Error: $wpdb_error");
                wp_send_json_error(array('message' => "Database error: $wpdb_error"));
            }
        } else {
            error_log("Link already exists between item $item_id and artist $artist_id");
            wp_send_json_error(array('message' => 'Link already exists'));
        }
    } else {
        error_log("Permission denied or missing parameters");
        wp_send_json_error(array('message' => 'Permission denied or missing parameters'));
    }
}
add_action('wp_ajax_link_artist_to_item', 'link_artist_to_item');
add_action('wp_ajax_nopriv_link_artist_to_item', 'link_artist_to_item');
