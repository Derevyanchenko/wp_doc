<?php 

/*
	1.  Гуглим Support woocommerce in theme и вставляем код в functions.php

	2.  Гуглим How to check if woocommerce is installed
	и в этот код добавляем в if нашу функцию с первого пункта

	3. в папке с нашей темой создаем папку woocommerce

*/

?>


<?php 

/*

------- Делаем страницу продуктов ----------

1. Переходим в wp-content/plugins/woocommerce/templates
и копируем файлы archive-product.php and content-product.php
и закидываем их в папку с темой/woocommerce

2. В файле functions.php подключаем наши новые style 
"/assets/css/woocommerce.css" и создаем этот файл

3. Скачиваем плагин simply show hooks

4. для хлебных крошек - спецсимвол пробела и хук смены вукомерсовских крошек

5. Для пагинации - переходим в кастомайзер / вукомерс / строк на странице - 1

6. коректируем карточки товаров с помощью хуков и экшенов
Пример:

*/

// product link

remove_action( "woocommerce_after_shop_loop_item", "woocommerce_template_loop_product_link_close", 5 );
add_action( "woocommerce_before_shop_loop_item_title", "woocommerce_template_loop_product_link_close", 10 );

// remove sidebar

add_action("woocommerce_before_main_content", "remove_sidebar");

function remove_sidebar() {
	if ( is_shop() ) {
		remove_action( "woocommerce_sidebar", "woocommerce_get_sidebar", 10 );
	}
}

