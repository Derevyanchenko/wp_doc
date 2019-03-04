

<!-- Подключение скриптов -->

	<?php 

		wp_deregister_script( "jquery" );
		wp_register_script( "jquery", get_template_directory_uri() . "/assets/js/jquery.min.js" );

		wp_enqueue_script( "name-script", get_template_directory_uri() . "/assets/js/script-name.js", array("jquery"), "", true );

	?>

		


<!-- // Меню -->

	<!-- - В header.php:  -->

		<div class="navbar">

			<?php 
				wp_register_nav_menu( array(
					"theme_location" => "menu-1",
					"menu_class" 	 => "nav navbar-nav navbar-right",
				) );
			?>

		</div> 	

<!-- 	- В functions.php: -->
	<?php 

		register_nav_menus ( array(
			"menu-1" => esc_html__ ("primary", "clean");
		) );

	?>
		


<!-- // Logo name and link: -->

	<!-- -example:  -->

		<a id="logo" href="<?php echo home_url("/"); ?>">
			<?php bloginfo("name"); ?>
		</a>

		<h1> <?php bloginfo("description"); ?> </h1>


<!-- // Делаем секцию постов портфолио (Категория) -->

	<!-- - in front-page.php: -->

		<div class="portfolio">

			<?php 
				$query = new WP_Query( array(
					"category_name" => "home"
				) );
			?>

			<?php if( $query->have_posts() ): while( $query->have_posts() ): $query->the_post(); ?>

			<?php 
				if( has_post_thumbnail() ) {		// рандом картинки если картинка категории не найдена
					$img_url = get_the_post_thumbnail_url();
				} else {
					$img_url = "https://picsum.photos/1280/864";
				}
			?>

			<div class="portfolio_img" style="background-image: url(<?php echo $img_url ?>);"></div>
			<a href="<?php the_permalink(); ?>" class="btn">
				<?php _e("Read more", clean); ?>
			</a>

			<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>


		</div>


<!-- // Настройка секции портфолио (категория) в кастомайзере -->

	in front-page.php:

		<?php if( is_front_page() $$ get_theme_mod("clean_home_category") ): ?>

			<?php 
				$query = new WP_Query( array(
					"category_name" => get_theme_mod("clean_home_category"),
				) );
			?>
			<!-- // portfolio blocks -->

		<?php endif; ?>


	<!-- in customizer.php: -->

		<!-- // theme custom settings -->

		<?php  

			$wp_customize->add_section("clean_theme_options", array(
				"title" 	=> __("Theme Options", "clean"),
				"priority"  => 10,
			));

			$wp_customize->add_settings("clean_home_categories", array(
				"default" => "",
			));
			$wp_customize->add_control(
				"clean_home_category",
				array(
					"label" => __("Category on Home Page", "clean"),
					"section" => "clean_theme_options",
					"type"	=> "text",
				)
			);


		?>

		

	<!-- в кастомайзере-настройки глав стр- отображать статическую страницу -->



<!-- вывод повторителя ACF -->

<?php $info = get_field("info"); ?>

    <?php foreach( $info as $i ): ?>
      
      <div class="columns">
        <h2>
          <?php echo $i["info__title"]; ?>
        </h2>

        <p>
          <?php echo $i["info__text"]; ?>
        </p>
     </div>

<?php endforeach; ?>  

<!-- // Добавление Страницы настроек в меню -->


<?php  

add_action('acf/init', 'my_acf_init');

function my_acf_init() {
	
	if( function_exists('acf_add_options_page') ) {
		
		$option_page = acf_add_options_page(array(
			'page_title' => 'Настройки темы',
			'menu_title' => 'Настройки темы',
			'menu_slug' => 'theme-general-settings',
			'capability' => 'edit_posts',
			'redirect' => false,
			'position' => '75.1',
			'post_id' => 'theme-general-settings',
		));
		
	}
	
}


?>

<!-- // Вывод в разметке поля из настроек темы -->

<?php echo get_field('header_number', 'theme-general-settings'); ?>




<!-- // создание своего типа записи -->

<!-- // register post type portfolio  -->


<?php  

add_action('init', 'my_custom_init');
function my_custom_init(){
	register_post_type('portfolio', array(
		'labels'             => array(
			'name'               => 'portfolio', // Основное название типа записи
			'singular_name'      => 'portfolio', // отдельное название записи типа Book
			'add_new'            => 'Добавить новую',
			'add_new_item'       => 'Добавить новую книгу',
			'edit_item'          => 'Редактировать книгу',
			'new_item'           => 'Новая книга',
			'view_item'          => 'Посмотреть книгу',
			'search_items'       => 'Найти книгу',
			'not_found'          =>  'Книг не найдено',
			'not_found_in_trash' => 'В корзине книг не найдено',
			'parent_item_colon'  => '',
			'menu_name'          => 'portfolio'

		  ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array('title','editor','author','thumbnail','excerpt','comments')
	) );
}


?>



<!-- // вывод типа записи в разметке -->

<?php  

    $args = array(
     
      "post_type" => "portfolio",
      "suppress_filters" => true,
    );

    $posts = get_posts($args);

    ?>

    <?php foreach($posts as $post) { setup_postdata($post); ?>

       <div class="columns portfolio-item">
	       <div class="item-wrap">
	    		   <a href="portfolio.html">
	             <?php the_post_thumbnail(); ?>
	             <div class="overlay"></div>
	             <div class="link-icon"><i class="fa fa-link"></i></div>
	          </a>
	    			<div class="portfolio-item-meta">
	    			   <h5><a href="portfolio.html">
	          <?php the_title(); ?>     
	         </a></h5>
	             <p>Illustration</p>
	    			</div>
	       </div>
    	</div>

    <?php } wp_reset_postdata(); ?>








<!-- коментарии -->



<!-- show comment form -->
<?php comment_form(); ?>


<!-- вывод комментов -->


<?php
    // Получаем комментарии поста с ID XXX из базы данных 
    $comments = get_comments(array(
    ));

    // Формируем вывод списка полученных комментариев
    wp_list_comments(array(
       'per_page' => 10, // Пагинация комментариев - по 10 на страницу
       'reverse_top_level' => false // Показываем последние комментарии в начале
    ), $comments);
?>