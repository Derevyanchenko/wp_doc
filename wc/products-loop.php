<!--

get products list

-->

<?php

/*
*
* Template Name: Products page
*
* */

?>

<?php get_header(); ?>

<section>

    <?php
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 2
    );

    $featured_query = new WP_Query( $args );

    if ($featured_query->have_posts()) :

        while ($featured_query->have_posts()) :

            $featured_query->the_post();

            $product = wc_get_product( $featured_query->post->ID );  ?>

            <?php print_r($product); ?>

            <li class="featured-products-item">

                <a><?php the_post_thumbnail(); ?></a>
                <?php the_excerpt(); ?>
                <?php echo $product->name; ?>
            </li>

        <?php endwhile; ?>

    <?php endif; ?>

    <?php wp_reset_query(); // Remember to reset
    ?>



</section>

<?php get_footer(); ?>