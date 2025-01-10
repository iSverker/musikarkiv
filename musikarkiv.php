<?php
/**
 * Plugin Name: Musikarkiv
 * Description: En WordPress-plugin för att hantera musikarkiv,
 * anpassad för Divi.
 * Version: 1.0
 * Author: Sverker Åslund
 */

// Aktivera felrapportering
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', plugin_dir_path(__FILE__) . 'error_log.txt');

// Inkludera nödvändiga filer
require_once plugin_dir_path(__FILE__) . 'includes/class-musikarkiv.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-musikarkiv-item.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-functions.php';

// Initiera huvudklassen för pluginen
if (class_exists('Musikarkiv')) {
    Musikarkiv::init();
}

// Simborgare

// Enqueue JavaScript and CSS files
function musikarkiv_enqueue_scripts() {
    wp_enqueue_script('musikarkiv-search-dropdown', plugins_url('assets/js/search-dropdown.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('musikarkiv-search-dropdown', plugins_url('assets/css/search-dropdown.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'musikarkiv_enqueue_scripts');

// Funktion för att hantera sökformuläret
function musikarkiv_search_form() {
    if (isset($_GET['sok'])) {
        $search_query = sanitize_text_field(stripslashes($_GET['sok']));
    }

    ob_start();
    $search_query = isset($_GET['sok']) ? sanitize_text_field(stripslashes($_GET['sok'])) : '';
    ?>
    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="search" value="<?php echo esc_attr($search_query); ?>" name="sok" />
        <input type="submit" value="Sök" />
    </form>
    <?php
    echo !empty($search_query) ? '<p>Du sökte efter: "' . esc_html($search_query) . '"</p>' : '';
    return ob_get_clean();
}
add_shortcode('musikarkiv_search', 'musikarkiv_search_form');
add_shortcode('musikarkiv_artist_search', 'musikarkiv_search_form');
add_shortcode('musikarkiv_artist_info', 'musikarkiv_search_form');
?>
