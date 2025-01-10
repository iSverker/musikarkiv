<?php
/**
 * Template Name: Musikarkiv Item Search
 */

if (!class_exists('Musikarkiv')) {
    return;
}

$search_query = isset($_GET['sok']) ? sanitize_text_field(stripslashes($_GET['sok'])) : '';

?>

<form method="get" action="<?php echo esc_url(get_permalink()); ?>">
    <input type="text" name="sok" value="<?php echo esc_attr($search_query); ?>" placeholder="Sök artist eller titel...">
    <button type="submit">Sök</button>
</form>

<?php if (!empty($search_query)) { ?>
    <p>Du sökte efter: "<?php echo esc_html($search_query); ?>"</p>
    <?php
    $results = Musikarkiv::search_items($search_query);
    $current_url = get_permalink();
    $item_info_url = trailingslashit(dirname($current_url)) . 'item-info/';
    echo !empty($results) ? '<ul>' . implode('', array_map(fn($item) => '<li><a href="' . esc_url(add_query_arg('id', $item->getId(), $item_info_url)) . '">' . esc_html($item->getArtist() . ' - ' . $item->getTitle()) . '</a></li>', $results)) . '</ul>' : '<p>Inga resultat hittades.</p>';
    ?>
<?php } ?>