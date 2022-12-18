<?php
/**
Plugin Name: POPUP ZYREX
Version: 1.0
Description: Wyskakujące okienko z banerem.
Author: Michał Żyrek
Author URI: http://zyrex.pl
Plugin URI: http://zyrex.pl/plugin/popup
 */

require_once 'class/class.php';
$popup = new ZXPP_Popup();


//add_shortcode('listatodo', 'wyswietl_liste');
function zxpp_add_scripts_and_styles() {
    wp_enqueue_style('zyrex-main-css', plugins_url( 'css/main.css', __FILE__ ));
    wp_enqueue_script( 'jquery', '/wp-includes/js/jquery/jquery.js' , array(), '3.6.1', true );
    wp_enqueue_script( 'zyrex-script-main', plugins_url( 'js/main.js', __FILE__ ) , array(), '1.0.0', true );
}

add_action('wp_enqueue_scripts', 'zxpp_add_scripts_and_styles');

function zxpp_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zxpopup';

    if ($wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") != $table_name) {
        $query = "CREATE TABLE " . $table_name . " (
        id int(9) NOT NULL AUTO_INCREMENT,
        tytul TEXT NOT NULL,
        link TEXT NOT NULL,
        img TEXT NOT NULL,
        active int(9) NOT NULL,
        PRIMARY KEY  (id)
        )";

        $wpdb->query($query);
    }
}

function zxpp_activation_data() {
	global $wpdb;

	$tytul = 'Mój pierwszy POPUP';
	$link = '#';
  $img = plugins_url( 'img/zyrex.jpg', __FILE__ );
  $active = '0';

	$table_name = $wpdb->prefix . 'zxpopup';

	$wpdb->insert(
		$table_name,
		array(
			'tytul' => $tytul,
			'link' => $link,
			'img' => $img,
      'active' => $active,
		)
	);
}

register_activation_hook(__FILE__, 'zxpp_activation');
register_activation_hook(__FILE__, 'zxpp_activation_data');

    function wyswietl_popup(){
        global $popup;
        $popup_img = $popup->get_popup_img();
        if ($popup_img) {
            foreach ($popup_img as $p) {
              $aktywny = $p->active;
              $img = $p->img;
              if ($aktywny == "1") {
        echo '<div id="js-cookie-popup" class="popup popup-hide">';
        echo '<div class="popup-content">';
        echo '<div id="js-cookie-popup-button" class="close">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="39" height="39" viewBox="0 0 39 39">';
        echo '<g id="close" transform="translate(-174 -937)">';
        echo '<circle id="Ellipse_12" data-name="Ellipse 12" cx="19.5" cy="19.5" r="19.5" transform="translate(174 937)" fill="#f0b6cc"/>';
        echo '<g id="Icon_feather-plus" data-name="Icon feather-plus" transform="translate(171.956 956.5) rotate(-45)">';
        echo '<path id="Path_29738" data-name="Path 29738" d="M18,7.5V22.968" transform="translate(-2.766)" fill="none" stroke="#9d7f65" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>';
        echo '<path id="Path_29739" data-name="Path 29739" d="M7.5,18H22.968" transform="translate(0 -2.766)" fill="none" stroke="#9d7f65" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>';
        echo '</g>';
        echo '</g>';
        echo '</svg>';
        echo '</div>';

                echo '<div>';
                echo '<img src="' . esc_url($img) . '" class="img-popup">';
            //    echo '<img src="/wp-content/uploads/2022/11/black-week.jpg" class="img-popup">';
                echo '</div>';

        echo '</div>';
        echo '</div>
        ';
        $title = $p->tytul;
        $link = strtolower($title);
        $link = str_replace(" ","-",$link);
        $link = str_replace(array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'), array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'), $link);
        echo '<script>';
        echo '
        $(".popup .close").on("click", function () {
          setCookie("' . $link . '", true, 1);
          closePopupScreen();
        });

        // auto open popup
        //openPopup();
        if (!getCookie("' . $link . '")) {
        	setTimeout(openPopup, 2000);
        }

        // close popup
        $(document).keyup(function (e) {
          if (e.key === "Escape") {
            setCookie("' . $link . '", true, 1);
            closePopup();
          }
        });
        ';
        echo '</script>';
      }
        }
      }
    }

 add_action( 'wp_footer', 'wyswietl_popup', 100);
// add_shortcode('popup', 'wyswietl_popup');
