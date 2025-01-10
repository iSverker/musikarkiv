<?php
/**
 * Template Name: Musikarkiv Artist Discography
 */

if (!class_exists('Musikarkiv')) {
    return;
}

$artist_id = isset($_GET['artist_id']) ? intval($_GET['artist_id']) : 0;

if ($artist_id > 0) {
    $items = Musikarkiv::get_items_by_artist($artist_id);
    $artist_name = Musikarkiv::get_artist_name($artist_id);
    $additional_items = Musikarkiv::get_items_by_artist_name($artist_name);

    if (!empty($items) || !empty($additional_items)) {
        echo '<h1>Discography</h1>';
        if (!empty($items)) {
            echo '<h2>Items with Artist ID</h2>';
            foreach ($items as $item) {
                $format = !empty($item->getDiscogsType()) ? $item->getDiscogsType() : Musikarkiv::get_type_name($item->getType());
                echo '<p><a href="' . esc_url(add_query_arg('id', $item->getId(), trailingslashit(dirname(get_permalink())) . 'musikarkiv-item-info/')) . '">' . esc_html($item->getTitle()) . ' (' . esc_html($format) . ')</a></p>';
            }
        }
        if (!empty($additional_items)) {
            echo '<h2>Possible Items without Artist ID</h2>';
            foreach ($additional_items as $item) {
                $format = !empty($item->getDiscogsType()) ? $item->getDiscogsType() : Musikarkiv::get_type_name($item->getType());
                echo '<p><a href="' . esc_url(add_query_arg('id', $item->getId(), trailingslashit(dirname(get_permalink())) . 'item-info/')) . '">' . esc_html($item->getArtist()) . ' - ' . esc_html($item->getTitle()) . ' (' . esc_html($format) . ')</a>';
                if (current_user_can('edit_others_posts')) {
                    echo ' <button onclick="addArtistIdToItem(' . $item->getId() . ', ' . $artist_id . ', \'' . esc_js($artist_name) . '\', \'' . esc_js($item->getTitle()) . ' (' . esc_js($format) . ')\')">Link to ' . esc_html($artist_name) . ' (id ' . esc_html($artist_id) . ')</button>';
                }
                echo '</p>';
            }
        }
    } else {
        echo '<p>No items found for this artist.</p>';
    }
} else {
    echo '<p>No artist ID provided.</p>';
}
?>

<script>
function addArtistIdToItem(itemId, artistId, artistName, itemTitle) {
    if (confirm(`Vill du lägga till artistID (${artistId}, ${artistName}) till objektet "${itemTitle}"?`)) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                action: 'add_artist_id_to_item',
                item_id: itemId,
                artist_id: artistId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to update item.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update item.');
        });
    }
}
</script>

<?php
?>
