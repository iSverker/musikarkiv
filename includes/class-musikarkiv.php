<?php
class Musikarkiv {

    /**
     * Initierar pluginen.
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_shortcodes'));
    }

    /**
     * Registrerar kortkoder.
     */
    public static function register_shortcodes() {
        add_shortcode('musikarkiv_start', array(__CLASS__, 'shortcode_start'));
        add_shortcode('musikarkiv_archive_item', array(__CLASS__, 'shortcode_archive_item'));
        add_shortcode('musikarkiv_latest_items', array(__CLASS__, 'shortcode_latest_items'));
        add_shortcode('musikarkiv_search', array(__CLASS__, 'shortcode_search'));
        add_shortcode('musikarkiv_artist_search', array(__CLASS__, 'shortcode_artist_search'));
        add_shortcode('musikarkiv_artist_info', array(__CLASS__, 'shortcode_artist_info'));
        add_shortcode('musikarkiv_artist_discography', array(__CLASS__, 'shortcode_artist_discography'));
        add_shortcode('musikarkiv_item_info', array(__CLASS__, 'shortcode_item_info'));
        add_shortcode('musikarkiv_item_search', array(__CLASS__, 'shortcode_item_search'));
        add_shortcode('musikarkiv_edit_item', array(__CLASS__, 'shortcode_edit_item'));
    }

    /**
     * Hanterar kortkoden [musikarkiv_start].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_start($atts) {
        return 'Kortkodens utdata för musikarkiv_start';
    }

    /**
     * Hanterar kortkoden [musikarkiv_archive_item].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_archive_item($atts) {
        return 'Kortkodens utdata för musikarkiv_archive_item';
    }

    /**
     * Hämtar de fem senaste objekten med en tumnagelbild från musikarkiv_inventory.
     *
     * @return array De senaste objekten med tumnagelbild.
     */
    public static function get_latest_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_inventory';
        $query = "SELECT *, (SELECT typ FROM {$wpdb->prefix}musikarkiv_types WHERE id = type) as type_name FROM $table_name WHERE thumbnail IS NOT NULL AND thumbnail != '' ORDER BY added DESC LIMIT 5";
        $results = $wpdb->get_results($query);

        $items = array();
        foreach ($results as $result) {
            $items[] = new Musikarkiv_Item($result);
        }

        return $items;
    }

    /**
     * Hanterar kortkoden [musikarkiv_latest_items].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_latest_items($atts) {
        ob_start();
        $items = self::get_latest_items();
        echo '<h2>Nya föremål i arkivet</h2>';
        if (empty($items)) {
            echo 'Inga objekt hittades.';
        } else {
            echo '<div class="latest-items">';
            foreach ($items as $item) {
                $format = !empty($item->getType()) ? self::get_type_name($item->getType()) : $item->getDiscogsType();
                $year = !empty($item->getReleaseYear()) ? ', ' . $item->getReleaseYear() : '';
                $item_info_url = add_query_arg('id', $item->getId(), trailingslashit(get_permalink()) . 'item-info/');
                echo '<div class="latest-item" style="margin-bottom: 20px;">';
                if (!empty($item->getThumbnail())) {
                    echo '<a href="' . esc_url($item_info_url) . '"><img src="' . esc_url($item->getThumbnail()) . '" alt="' . esc_attr($item->getTitle()) . '" style="width: 33%; float: left; margin-right: 10px;"></a>';
                }
                echo '<p><a href="' . esc_url($item_info_url) . '">' . esc_html($item->getSortedArtist()) . ': "' . esc_html($item->getTitle()) . '" (' . esc_html($format) . esc_html($year) . ')</a></p>';
                echo '<div style="clear: both;"></div>';
                echo '</div>';
            }
            echo '</div>';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_search].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_search($atts = array()) {
        // Validera attributen
        $atts = shortcode_atts(array(
            'placeholder' => 'Sök för tusan...'
        ), $atts, 'musikarkiv_search');

        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-search-item.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_artist_search].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_artist_search($atts = array()) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-artist-search.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_artist_info].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_artist_info($atts = array()) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-artist-info.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_artist_discography].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_artist_discography($atts = array()) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-artist-discography.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_item_info].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_item_info($atts = array()) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-item-info.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_item_search].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_item_search($atts = array()) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-item-search.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Hanterar kortkoden [musikarkiv_edit_item].
     *
     * @param array $atts Attribut för kortkoden.
     * @return string Utdata för kortkoden.
     */
    public static function shortcode_edit_item($atts) {
        ob_start();
        $template_path = plugin_dir_path(__FILE__) . '../templates/musikarkiv-edit-item.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo 'Template file not found.';
        }
        return ob_get_clean();
    }

    /**
     * Sök efter objekt i musikarkiv_inventory.
     *
     * @param string $query Söksträng.
     * @return array Sökresultat.
     */
    public static function search_items($query) {
        global $wpdb;
        $query = sanitize_text_field(stripslashes($query));
        $table_name = $wpdb->prefix . 'musikarkiv_inventory';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE title LIKE %s OR sortedArtist LIKE %s", '%' . $wpdb->esc_like($query) . '%', '%' . $wpdb->esc_like($query) . '%');
        $results = $wpdb->get_results($sql);

        $items = array();
        foreach ($results as $result) {
            $items[] = new Musikarkiv_Item($result);
        }

        return $items;
    }

    /**
     * Sök efter artister i musikarkiv_artists.
     *
     * @param string $query Söksträng.
     * @return array Sökresultat.
     */
    public static function search_artists($query) {
        global $wpdb;
        $query = sanitize_text_field(stripslashes($query));
        $table_name = $wpdb->prefix . 'musikarkiv_artists';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE name LIKE %s", '%' . $wpdb->esc_like($query) . '%');
        return $wpdb->get_results($sql);
    }

    /**
     * Hämtar objekt från musikarkiv_inventory för en specifik artist.
     *
     * @param int $artist_id Artistens ID.
     * @return array Objekt för artisten.
     */
    public static function get_items_by_artist($artist_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_artist_inventory';
        $sql = $wpdb->prepare("SELECT inventory_id FROM $table_name WHERE artist_id = %d", $artist_id);
        $inventory_ids = $wpdb->get_col($sql);

        $items = array();
        if (!empty($inventory_ids)) {
            $placeholders = implode(',', array_fill(0, count($inventory_ids), '%d'));
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}musikarkiv_inventory WHERE id IN ($placeholders)", ...$inventory_ids);
            $results = $wpdb->get_results($sql);

            foreach ($results as $result) {
                $items[] = new Musikarkiv_Item($result);
            }
        }

        return $items;
    }

    /**
     * Hämtar artistens namn baserat på artist_id.
     *
     * @param int $artist_id Artistens ID.
     * @return string Artistens namn.
     */
    public static function get_artist_name($artist_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_artists';
        $sql = $wpdb->prepare("SELECT name FROM $table_name WHERE id = %d", $artist_id);
        return $wpdb->get_var($sql);
    }

    /**
     * Hämtar objekt från musikarkiv_inventory baserat på artistens namn.
     *
     * @param string $artist_name Artistens namn.
     * @return array Objekt för artisten.
     */
    public static function get_items_by_artist_name($artist_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_inventory';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE artist = %s AND artistID IS NULL", $artist_name);
        $results = $wpdb->get_results($sql);

        $items = array();
        foreach ($results as $result) {
            $items[] = new Musikarkiv_Item($result);
        }

        return $items;
    }

    /**
     * Hämtar information om ett objekt från musikarkiv_inventory.
     *
     * @param int $item_id Objektets ID.
     * @return Musikarkiv_Item Objektet.
     */
    public static function get_item_info($item_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_inventory';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $item_id);
        $result = $wpdb->get_row($sql);

        return $result ? new Musikarkiv_Item($result) : null;
    }

    /**
     * Hämtar typens namn baserat på typ_id.
     *
     * @param int $type_id Typens ID.
     * @return string Typens namn.
     */
    public static function get_type_name($type_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_types';
        $sql = $wpdb->prepare("SELECT typ FROM $table_name WHERE id = %d", $type_id);
        return $wpdb->get_var($sql);
    }

    /**
     * Hämtar artistens ID baserat på artistens namn.
     *
     * @param string $artist_name Artistens namn.
     * @return int|null Artistens ID eller null om artisten inte hittas.
     */
    public static function get_artist_id_by_name($artist_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_artists';
        $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE name = %s", $artist_name);
        return $wpdb->get_var($sql);
    }

    /**
     * Kontrollera om det finns en artist kopplad till ett objekt.
     *
     * @param int $item_id Objektets ID.
     * @param int $artist_id Artistens ID.
     * @return bool True om länken finns, annars false.
     */
    public static function has_artist_link($item_id, $artist_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_artist_inventory';
        $link_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE inventory_id = %d AND artist_id = %d", $item_id, $artist_id));
        return $link_exists > 0;
    }

    /**
     * Kontrollera om det finns flera artister med samma namn.
     *
     * @param string $artist_name Artistens namn.
     * @return bool True om det finns flera artister med samma namn, annars false.
     */
    public static function has_multiple_artists_with_name($artist_name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_artists';
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE name = %s", $artist_name));
        return $count > 1;
    }

    /**
     * Fetches id/name pairs from the wp_musikarkiv_types table.
     *
     * @return array Array of type objects with id and name properties.
     */
    public static function get_types() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'musikarkiv_types';
        $results = $wpdb->get_results("SELECT id, typ AS name FROM $table_name");
        return $results;
    }
}
