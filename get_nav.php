<!-- Функция подключения навигации -->

<!-- functions.php: -->


<?php 


function get_nav()
{
	$templates = array();
	$templates[] = 'navigation.php';

	locate_template( $templates );
}

?>

<!-- in header.php: -->

<?php 

	get_nav();

?>