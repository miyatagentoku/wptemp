    <?php get_template_part('inc/controller/search-form'); ?>
    <?php get_template_part('inc/controller/sidebar'); ?>

    <?php
        $paged = get_query_var('paged')? get_query_var('paged') : 1;

        $args = array(
            'post_type' => get_post_type(),
            'posts_per_page' => 5,
            'paged' => $paged,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'key_name',
                    'value' => array('test'),
                    'compare' => 'IN'
                )
            ),
            'tax_query' => array(
                'relation' => 'OR', // AND OR
                array(
                    'taxonomy' => get_query_var( 'taxonomy' ),
                    'terms' => $term,
                    'field' => 'slug',
                    'operator' => 'IN'
                )
            )
        );
    ?>

    <?php $wp_query = new WP_Query( $args ); ?>
    <?php if( $wp_query->have_posts() ) : ?>
    <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

    <?php endwhile; ?>
    <?php else: ?>

    <?php endif; ?>

    <div class="m-pageNav">
        <?php m_getPageNavigation( $wp_rewrite, $wp_query, $paged ); ?>
    </div>

    <?php wp_reset_postdata(); ?>