<?php
/**
 * Template Name: Musikarkiv Item Info
 */

if (!class_exists('Musikarkiv')) {
    return;
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    $item = Musikarkiv::get_item_info($item_id);

    if ($item) {
        $format = !empty($item->getDiscogsType()) ? $item->getDiscogsType() : Musikarkiv::get_type_name($item->getType());
        $year = !empty($item->getReleaseYear()) ? $item->getReleaseYear() : '';

        $title_suffix = $format;
        if (!empty($year)) {
            $title_suffix .= ', ' . $year;
        }

        echo '<h1>' . esc_html($item->getSortedArtist()) . ': "' . esc_html($item->getTitle()) . '" (' . esc_html($title_suffix) . ')</h1>';

        if (!empty($item->getImage())) {
            echo '<p><img src="' . esc_url($item->getImage()) . '" alt="' . esc_attr($item->getTitle()) . '"></p>';
        }

        if (!empty($item->getDonated())) {
            echo '<p>Donerad av ' . esc_html($item->getDonated()) . '.</p>';
        }

        if (!empty($item->getDescription())) {
            echo '<p>' . nl2br(esc_html($item->getDescription())) . '</p>';
        }

        if (!empty($item->getLink())) {
            echo '<p><a href="' . esc_url($item->getLink()) . '">Mer information på Discogs</a></p>';
        }

        $artist_name = trim($item->getSortedArtist());
        $artist_id = Musikarkiv::get_artist_id_by_name($artist_name);
        $has_link = Musikarkiv::has_artist_link($item_id, $artist_id);
        $has_multiple_artists = Musikarkiv::has_multiple_artists_with_name($artist_name);

        if ($artist_id && current_user_can('edit_others_posts') && !$has_link && !$has_multiple_artists) {
            echo '<button onclick="linkArtistToItem(' . $item_id . ', ' . $artist_id . ', \'' . esc_js($artist_name) . '\')">Link to ' . esc_html($artist_name) . ' (id ' . esc_html($artist_id) . ')</button>';
        } elseif ($has_multiple_artists) {
            echo '<p>Det finns flera artister med namnet ' . esc_html($artist_name) . '. Vänligen kontrollera och välj rätt artist.</p>';
        }

        if (current_user_can('edit_others_posts')) {
            $edit_item_url = add_query_arg('id', $item_id, dirname(trailingslashit(get_permalink())) . '/edit-item');
            echo '<p><a href="' . esc_url($edit_item_url) . '">Edit Item</a></p>';
        }
    } else {
        echo '<p>Item not found.</p>';
    }
} else {
    echo '<p>No item ID provided.</p>';
}
?>

<script>
/**
 * Länkar en artist till ett objekt.
 *
 * @param {number} itemId - ID för objektet som ska länkas.
 * @param {number} artistId - ID för artisten som ska länkas.
 * @param {string} artistName - Namn på artisten som ska länkas.
 */
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
                alert(`Failed to link artist. Error: ${data.data.message}`);
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