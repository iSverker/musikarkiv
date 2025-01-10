<?php
/**
 * Template Name: Musikarkiv Artist Info
 */

if (!class_exists('Musikarkiv')) {
    return;
}

$artist_id = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 0;

if ($artist_id > 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'musikarkiv_artists';
    $artist = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $artist_id));

    if ($artist) {
        echo '<h1>' . esc_html($artist->name) . '</h1>';

        // Fetch possible items without artist ID
        $possible_items = Musikarkiv::get_items_by_artist_name($artist->name);
        if (!empty($possible_items)) {
            echo '<h2>Possible Items</h2>';
            foreach ($possible_items as $item) {
                $format = !empty($item->getDiscogsType()) ? $item->getDiscogsType() : Musikarkiv::get_type_name($item->getType());
                echo '<p><a href="' . esc_url(add_query_arg('id', $item->getId(), trailingslashit(dirname(get_permalink())) . 'musikarkiv-item-info/')) . '" target="_self">' . esc_html($item->getArtist()) . ' - ' . esc_html($item->getTitle()) . ' (' . esc_html($format) . ')</a>';
                if (current_user_can('edit_others_posts')) {
                    echo ' <button onclick="linkArtistToItem(' . $item->getId() . ', ' . $artist_id . ', \'' . esc_js($artist->name) . '\')">Link to ' . esc_html($artist->name) . ' (id ' . esc_html($artist_id) . ')</button>';
                }
                echo '</p>';
            }
        }
    } else {
        echo '<p>Artist not found.</p>';
    }
} else {
    echo '<p>No artist ID provided.</p>';
}
?>

<script>
function linkArtistToItem(itemId, artistId, artistName) {
    if (confirm(`Vill du länka objektet till ${artistName} (id ${artistId})?`)) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'link_artist_to_item',
                item_id: itemId,
                artist_id: artistId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to link artist.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to link artist.');
        });
    }
}
</script>

<?php
?>
