<?php
/**
 * Template Name: Musikarkiv Artist Search Item
 */

if (!class_exists('Musikarkiv')) {
    return;
}

// Definiera $atts om den inte redan är definierad
if (!isset($atts)) {
    $atts = array('placeholder' => 'Sök för tusan...');
}

$search_query = isset($_GET['sok']) ? sanitize_text_field(stripslashes($_GET['sok'])) : '';

?>

<form method="get" action="<?php echo esc_url(get_permalink()); ?>">
    <input type="text" name="sok" value="<?php echo esc_attr($search_query); ?>" placeholder="<?php echo esc_attr($atts['placeholder']); ?>">
    <button type="submit">Sök</button>
</form>

<?php if (!empty($search_query)) { ?>
    <p>Du sökte efter: "<?php echo esc_html($search_query); ?>"</p>
    <?php
    $results = Musikarkiv::search_items($search_query, ['artist', 'title']);
    echo !empty($results) ? '<ul>' . implode('', array_map(fn($item) => '<li>' . esc_html($item->__toString()) . '</li>', $results)) . '</ul>' : '<p>Inga resultat hittades.</p>';
    ?>
<?php } ?>
