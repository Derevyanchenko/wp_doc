<h1>test</h1>
<!--   --><?php /*echo get_page_template(); */?>
<?php if ( is_page_template("property.php") ): ?>
    <h1>Недвижимость</h1>
<?php else: ?>
    <h1><?php echo get_page_template_slug(); ?></h1>
<?php endif; ?>
