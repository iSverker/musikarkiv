<?php
/**
 * Template Name: Musikarkiv Edit Item
 */

// Enqueue the discogs-fetch.js script
wp_enqueue_script('discogs-fetch', plugin_dir_url(__FILE__) . '../assets/js/discogs-fetch.js', array(), null, true);

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0 && current_user_can('edit_others_posts')) {
    $item = Musikarkiv::get_item_info($item_id);

    if ($item) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update item with new values
            $item->setArtist(sanitize_text_field($_POST['artist']));
            $item->setTitle(sanitize_text_field($_POST['title']));
            $item->setDescription(sanitize_textarea_field($_POST['description']));
            $item->setReleaseYear(intval($_POST['releaseYear']));
            $item->setImage(sanitize_text_field($_POST['image']));
            $item->setThumbnail(sanitize_text_field($_POST['thumbnail']));
            $item->setDonated(sanitize_text_field($_POST['donated']));
            $item->setSortedArtist(sanitize_text_field($_POST['sortedArtist']));
            $item->setArtistID(intval($_POST['artistID']) ?: null);
            $item->setType(sanitize_text_field($_POST['type']));
            $item->setDiscogsType(sanitize_text_field($_POST['discogsType']));
            $item->setLink(sanitize_text_field($_POST['link']));
            $item->setCollection(isset($_POST['collection']) ? 1 : 0);
            $item->setPublic(isset($_POST['public']) ? 1 : 0);
            $item->setArchived(isset($_POST['archived']) ? 1 : 0);
            $item->setDiscogsMaster(intval($_POST['discogsMaster']));
            $item->setDiscogsID(intval($_POST['discogsID']));

            // Save updates to the database
            global $wpdb;
            $table_name = $wpdb->prefix . 'musikarkiv_inventory';
            $wpdb->update($table_name, array(
                'title' => $item->getTitle(),
                'artist' => $item->getArtist(),
                'description' => $item->getDescription(),
                'releaseYear' => $item->getReleaseYear(),
                'image' => $item->getImage(),
                'thumbnail' => $item->getThumbnail(),
                'donated' => $item->getDonated(),
                'sortedArtist' => $item->getSortedArtist(),
                'artistID' => $item->getArtistID() ?: null,
                'type' => $item->getType(),
                'discogsType' => $item->getDiscogsType(),
                'link' => $item->getLink(),
                'collection' => $item->getCollection(),
                'public' => $item->getPublic(),
                'archived' => $item->getArchived(),
                'discogsMaster' => $item->getDiscogsMaster(),
                'discogsID' => $item->getDiscogsID()
            ), array('id' => $item_id));

            echo '<p>Objektet har uppdaterats.</p>';
        }

        // Fetch the Discogs ID for the artist
        $artist_discogs_id = '';
        if ($item->getArtistID()) {
            $artist_discogs_id = $wpdb->get_var($wpdb->prepare("SELECT discogsid FROM {$wpdb->prefix}musikarkiv_artists WHERE id = %d", $item->getArtistID()));
        }
        ?>

        <style>
            table {
                width: auto !important;
                border-collapse: collapse;
            }

            td {
                padding: 2px 5px;
                vertical-align: top;
                white-space: nowrap;
            }

            label {
                margin: 0;
                padding: 0;
                display: block;
                width: 100%;
            }

            input[type="text"], textarea {
                width: calc(50ch + 4px);
                padding: 2px 4px;
                margin: 0;
                box-sizing: border-box;
            }

            input[type="text"].half-width {
                width: calc(30ch + 2px);
            }

            textarea {
                height: auto;
                min-height: 5em;
            }

            input[type="submit"] {
                margin-top: 10px;
                display: block;
            }

            #thumbnail-cell img {
                margin-left: 0;
                display: block;
            }
        </style>

        <form method="post">
            <table border="1">

                <tr>
                    <td><label for="artist">Artist:</label></td>
                    <td colspan="2"><input type="text" id="artist" name="artist" value="<?php echo esc_attr($item->getArtist()); ?>"></td>
                </tr>

                <tr>
                    <td><label for="sortedArtist">Sorteras under:</label></td>
                    <td colspan="2">
                        <input type="text" id="sortedArtist" name="sortedArtist" class="half-width" value="<?php echo esc_attr($item->getSortedArtist()); ?>">
                        <button type="button" id="lastname-first">Efternamn först</button>
                    </td>
                </tr>

                <tr>
                    <td><label for="title">Titel:</label></td>
                    <td colspan="2"><input type="text" id="title" name="title" value="<?php echo esc_attr($item->getTitle()); ?>"></td>
                </tr>

                <tr>
                    <td><label for="artistID">Artist ID:</label></td>
                    <td colspan="2"><input type="text" id="artistID" name="artistID" value="<?php echo esc_attr($item->getArtistID() ?: ''); ?>"></td>
                </tr>

                <tr>
                    <td><label for="artistDiscogsID">Artist-ID Discogs:</label></td>
                    <td colspan="2"><input type="text" id="artistDiscogsID" name="artistDiscogsID" value="<?php echo esc_attr($artist_discogs_id ?: ''); ?>" readonly></td>
                </tr>

                <tr>
                    <td><label for="type">Typ:</label></td>
                    <td colspan="2">
                    <?php
                        $types = Musikarkiv::get_types();
                        if ($types) {
                            echo '<select id="type" name="type">';
                            foreach ($types as $type) {
                                $selected = $item->getType() == $type->id ? 'selected' : '';
                                echo '<option value="' . esc_attr($type->id) . '" ' . $selected . '>' . esc_html($type->name) . '</option>';
                            }
                            echo '</select>';
                        } else {
                            echo '<input type="text" id="type" name="type" value="' . esc_attr($item->getType()) . '">';
                        }
                    ?>
                    </td>
                </tr>

                <tr>
                    <td ><label for="discogsType">Discogs Typ:</label></td>
                    <td colspan="2"><input type="text" id="discogsType" name="discogsType" value="<?php echo esc_attr($item->getDiscogsType()); ?>"></td>
                </tr>

                <tr>
                    <td><label for="releaseYear">Utgivningsår:</label></td>
                    <td colspan="2"><input type="number" id="releaseYear" name="releaseYear" value="<?php echo esc_attr($item->getReleaseYear() ?: ''); ?>" min="0" step="1"></td>
                </tr>

                <tr>
                    <td><label for="link">Länk:</label></td>
                    <td colspan="2">
                        <div><input type="text" id="link" name="link" value="<?php echo esc_attr($item->getLink()); ?>"></div>
                        <div><button type="button" id="fetch-discogs-info">Hämta info från Discogs</button></div>
                    </td>
                </tr>

                <tr>
                    <td><label for="image">Bild:</label></td>
                    <td colspan="2"><input type="text" id="image" name="image" value="<?php echo esc_attr($item->getImage()); ?>"></td>
                </tr>

                <tr>
                    <td><label for="thumbnail">Miniatyr:</label></td>
                    <td colspan="2"><input type="text" id="thumbnail" name="thumbnail" value="<?php echo esc_attr($item->getThumbnail()); ?>"></td>
                </tr>

                <tr>
                    <td><label for="collection">Samling:</label></td>
                    <td><input type="checkbox" id="collection" name="collection" <?php checked($item->getCollection(), 1); ?>></td>
                    <td rowspan="5" id="thumbnail-cell">
                        <?php
                            if (!empty($item->getThumbnail())) {
                                echo '<img src="' . esc_url($item->getThumbnail()) . '" alt="' . esc_attr($item->getTitle()) . '">';
                            }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td><label for="public">Offentlig:</label></td>
                    <td><input type="checkbox" id="public" name="public" <?php checked($item->getPublic(), 1); ?>></td>
                </tr>

                <tr>
                    <td><label for="archived">Arkiverad:</label></td>
                    <td><input type="checkbox" id="archived" name="archived" <?php checked($item->getArchived(), 1); ?>></td>
                </tr>

                <tr>
                    <td><label for="discogsID">Discogs ID:</label></td>
                    <td><input type="number" id="discogsID" name="discogsID" value="<?php echo esc_attr($item->getDiscogsID()); ?>" style="width: 100px;"></td>
                </tr>

                <tr>
                    <td><label for="discogsMaster">Discogs Master:</label></td>
                    <td><input type="number" id="discogsMaster" name="discogsMaster" value="<?php echo esc_attr($item->getDiscogsMaster()); ?>" style="width: 100px;"></td>
                </tr>

                <tr>
                    <td><label for="description">Beskrivning:</label></td>
                    <td colspan="2"><textarea id="description" name="description" rows="5"><?php echo esc_textarea($item->getDescription()); ?></textarea></td>
                </tr>

                <tr>
                    <td><label for="donated">Donerat:</label></td>
                    <td colspan="2"><input type="text" id="donated" name="donated" value="<?php echo esc_attr($item->getDonated()); ?>"></td>
                </tr>

                <tr>
                    <td colspan="3"><input type="submit" value="Uppdatera"></td>
                </tr>
            </table>
        </form>

        <script>
            document.getElementById('fetch-discogs-info').addEventListener('click', function() {
                const linkInput = document.getElementById('link');
                const link = linkInput.value;
                const discogsIDMatch = link.match(/release\/(\d+)/);
                if (discogsIDMatch) {
                    const discogsID = discogsIDMatch[1];
                    const shortenedLink = `https://www.discogs.com/release/${discogsID}`;
                    linkInput.value = shortenedLink;
                    const apiUrl = `https://api.discogs.com/releases/${discogsID}?key=TFXgOyzSXIVzNrIcgIQv&secret=NIEVpICslDlHEQDHgtgjnWIfrqmlXryl`;
                    fetch(apiUrl)
                        .then(response => response.json())
                        .then(data => {
                            // Populate the form fields with the fetched data
                            document.getElementById('artist').value = data.artists_sort;
                            document.getElementById('title').value = data.title;
                            const descriptionField = document.getElementById('description');
                            if (!descriptionField.value) {
                                descriptionField.value = data.notes || '';
                            }
                            document.getElementById('releaseYear').value = data.year || '';
                            document.getElementById('image').value = data.images && data.images[0] ? data.images[0].uri : '';
                            document.getElementById('thumbnail').value = data.thumb;
                            document.getElementById('discogsID').value = discogsID;
                            document.getElementById('discogsMaster').value = data.master_id || '';
                            document.getElementById('discogsType').value = data.formats && data.formats[0] ? data.formats.map(format => format.name + (format.descriptions ? ', ' + format.descriptions.join(', ') : '')).join('; ') : '';

                            // Fetch the artist's Discogs ID
                            if (data.artists && data.artists.length > 0) {
                                const artistDiscogsID = data.artists[0].id;
                                document.getElementById('artistDiscogsID').value = artistDiscogsID;

                                // Check if the artist exists in wp_musikarkiv_artists
                                fetch(`<?php echo admin_url('admin-ajax.php'); ?>?action=check_artist_exists&discogs_id=${artistDiscogsID}`)
                                    .then(response => response.json())
                                    .then(artistData => {
                                        if (artistData.exists) {
                                            const artistID = artistData.artist_id;
                                            document.getElementById('artistID').value = artistID;
                                            if (confirm(`Artisten finns redan i databasen med ID ${artistID}. Vill du koppla Discogs artist-ID till detta ID?`)) {
                                                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/x-www-form-urlencoded'
                                                    },
                                                    body: new URLSearchParams({
                                                        action: 'link_discogs_id_to_artist',
                                                        artist_id: artistID,
                                                        discogs_id: artistDiscogsID
                                                    })
                                                })
                                                .then(response => response.json())
                                                .then(linkData => {
                                                    if (linkData.success) {
                                                        alert('Discogs artist-ID har kopplats till artisten.');
                                                    } else {
                                                        alert('Misslyckades med att koppla Discogs artist-ID.');
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('Misslyckades med att koppla Discogs artist-ID.');
                                                });
                                            }
                                        }
                                    });
                            }

                            // Display the thumbnail in the cell
                            const thumbnailCell = document.getElementById('thumbnail-cell');
                            if (data.thumb) {
                                thumbnailCell.innerHTML = '<img src="' + data.thumb + '" alt="' + data.title + '">';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching data from Discogs:', error);
                            alert('Kunde inte hämta data från Discogs.');
                        });
                } else {
                    alert('Vänligen ange en giltig Discogs-länk.');
                }
            });

            document.getElementById('artist').addEventListener('blur', function() {
                const artistInput = document.getElementById('artist');
                const sortedArtistInput = document.getElementById('sortedArtist');
                sortedArtistInput.value = artistInput.value.replace(/^The\s+/i, '');
            });

            document.getElementById('lastname-first').addEventListener('click', function() {
                const sortedArtistInput = document.getElementById('sortedArtist');
                const nameParts = sortedArtistInput.value.trim().split(' ');
                if (nameParts.length > 1) {
                    const lastName = nameParts.pop();
                    sortedArtistInput.value = `${lastName}, ${nameParts.join(' ')}`;
                }
            });
        </script>

        <?php
    } else {
        echo '<p>Item not found.</p>';
    }
} else {
    echo '<p>No item ID provided or insufficient permissions.</p>';
}
?>