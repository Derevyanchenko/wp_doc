

<!--/*В форме добавляем скрытое поле*/-->

    <form action="/" class="estimate_request_form ajax_form">
        <input type="hidden" name="action" value="estimate_request">

 <!--В функц подкл scripts-->

<?php

wp_localize_script('main-script', 'mm_ajax_object',  // main-script - name in main js include
    array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'templ_dir_uri' => get_template_directory_uri(),
        'uploads_dir_uri' => wp_upload_dir()['baseurl'],
        'home_url' => home_url()
    ));

include "includes/ajax.php";


?>

<!--ajax script in js code-->

<script>

    $(".estimate_request_form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: mm_ajax_object.ajax_url,
            type: 'POST',
            data: $(this).serialize(),
            success: function( data ) {
                close_popup();
                thanks_popup();
                $(this).trigger("reset");
            }
        });
    });

</script>

<!--Сам обработчик в каталог темы/includes/alax.php-->


<?php


add_action('wp_ajax_estimate_request', 'estimate_request_callback'); // form action, function down
//
add_action('wp_ajax_nopriv_estimate_request', 'estimate_request_callback');

function estimate_request_callback()
{
    if (!empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email'])) {

        $new_post_id = wp_insert_post(array('post_type' => 'estimate_requests', 'post_status' => 'publish'));
        wp_update_post(array(
            'ID' => $new_post_id,
            'post_type' => 'estimate_requests',
            'post_title' => 'Estimate request ' . $new_post_id . ' from ' . $_POST['name']
        ));

        $fields = array('name', 'phone', 'email', 'message');
        foreach ($fields as $field) {
            update_post_meta($new_post_id, 'mm_estimate_request_' . $field, $_POST[$field]);
        }

        $to = get_option('admin_email');
        $subject = 'Estimate request ' . $new_post_id . ' from ' . $_POST['name'];
        $body =
            'name: ' . $_POST['name'] .'<br>' .
            'phone: ' . '<a href="tel:' . $_POST['phone'] . '">' . $_POST['phone'] .'</a>' . '<br>' .
            'email: ' . '<a href="mailto:' . $_POST['email'] . '">' . $_POST['email'] .'</a>' . '<br>' .
            'message: ' . $_POST['message'] .'<br>' .
            '[' . get_bloginfo('name') . '] New estimate request, here is a link: <a href="' . admin_url() . 'post.php?post=' . $new_post_id . '&action=edit">Estimate request ' . $new_post_id . '</a>';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $body = wordwrap($body, 70, "\r\n");

        wp_mail($to, $subject, $body, $headers);


        wp_die('ok');

    } else {
        wp_die('fail');
    }
}

function estimate_request_adding_custom_meta_boxes($post)
{
    add_meta_box(
        'mm_estimate_request_metabox',
        __('Estimate request'),
        'mm_estimate_request_metabox_cb',
        'estimate_requests',
        'normal',
        'default'
    );
}

add_action('add_meta_boxes_estimate_requests', 'estimate_request_adding_custom_meta_boxes');

function mm_estimate_request_metabox_cb($post){
    echo 'Имя: ';
    echo get_post_meta($post->ID, 'mm_estimate_request_name', true);
    echo '<br>';

    echo 'Телефон: ';
    echo '<a href="tel:' . preg_replace("/[^0-9]/", "",
            get_post_meta($post->ID, 'mm_call_request_phone', true)) . '">' . get_post_meta($post->ID,
            'mm_estimate_request_phone', true) . '</a>';
    echo '<br>';

    echo 'Почта: ';
    echo '<a href="mailto:' . get_post_meta($post->ID, 'mm_call_request_email', true) . '">' . get_post_meta($post->ID,
            'mm_estimate_request_email', true) . '</a>';
    echo '<br>';

    echo 'Сообщение: ';
    echo get_post_meta($post->ID, 'mm_estimate_request_message', true);
    echo '<br>';
}

