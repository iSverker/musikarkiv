<?php
/**
 * Template Name: Musikarkiv Artist Search
 */

if (!class_exists('Musikarkiv')) {
    return;
}

$search_query = isset($_GET['sok']) ? sanitize_text_field(stripslashes($_GET['sok'])) : '';

?>

<form method="get" action="<?php echo esc_url(get_permalink()); ?>">
    <input type="text" name="sok" value="<?php echo esc_attr($search_query); ?>" placeholder="Sök artist...">
    <button type="submit">Sök</button>
</form>

<?php if (!empty($search_query)) { ?>
    <p>Du sökte efter: "<?php echo esc_html($search_query); ?>"</p>
    <?php
    $results = Musikarkiv::search_artists($search_query);
    $current_url = get_permalink();
    $artist_info_url = trailingslashit(dirname($current_url)) . 'artist-info/';
    echo !empty($results) ? '<ul>' . implode('', array_map(fn($artist) => '<li><a href="' . esc_url(add_query_arg('artist_id', $artist->id, $artist_info_url)) . '">' . esc_html($artist->name) . '</a></li>', $results)) . '</ul>' : '<p>Inga resultat hittades.</p>';
    ?>
<?php } ?>
<?php // Lägg till sluttaggen för att avsluta PHP-koden korrekt ?>
<?php
?>
