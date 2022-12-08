<?php
class ZXPP_Popup {
    private $wpdb;
    private $table_name;

    function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'zxpopup';
        add_action( 'admin_menu', array( &$this, 'zx_add_menu' ));
    }

    function zx_add_menu(){
        add_menu_page( 'ZX POPUP', 'POPUP', 'administrator', 'popup', array( &$this, 'zx_main_page'), '', 33 );
    }

    function zx_main_page(){
        if(isset($_POST['popup_action'])) {
            if($_POST['popup_action'] == 'add' && isset($_FILES['img'])) {
              $upload_dir = wp_upload_dir();
              $upload_dir['basedir'] = $upload_dir['basedir'] . '/zyrex_popup';
    					if (!file_exists($upload_dir['basedir'])) {
    						mkdir($upload_dir['basedir'], 0777, true);
    					}
              $img = $_FILES["img"]["name"];
              $target_file = $upload_dir['basedir'] . '/' . $img;
              if(move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                echo '';
              }
                //Dodawanie wiadomości
                if($this->add_post($_POST['tytul'], $_POST['link'], $img)) {
                    $notice = '<div class="notice notice-success">Dodano popup</div>';
                } else {
                    $notice = '<div class="notice notice-error">Nie dodano popup</div>';
                }
            } else if($_POST['popup_action'] == 'edit') {
                //edycja wiadomości
                if($this->edit_post($_POST['popup_post_id'],$_POST['tytul'])) {
                    $notice = '<div class="notice notice-success">Edytowano popup</div>';
                } else {
                    $notice = '<div class="notice notice-error">Nie udało się zaktualizować popup</div>';
                }
            } else if($_POST['popup_action'] == 'aktywuj') {
                //edycja wiadomości
                $a = 1;
                if($this->edit_active($_POST['popup_id'],$a)) {
                    $notice = '<div class="notice notice-success">Aktywowano popup</div>';
                } else {
                    $notice = '<div class="notice notice-error">Nie udało się aktywować popupa/div>';
                }
            } else if($_POST['popup_action'] == 'dezaktywuj') {
                //edycja wiadomości
                $a = 0;
                if($this->edit_active($_POST['popup_id'],$a)) {
                    $notice = '<div class="notice notice-success">Dezaktywowano popup</div>';
                } else {
                    $notice = '<div class="notice notice-error">Nie udało się dezaktywować popupa/div>';
                }
            }
        }

        if(isset($_POST['popup_delete'])) {
            //usuwanie wiadomości
            if($this->delete_post($_POST['popup_post_id'])) {
                $notice = '<div class="notice notice-success">Usunięto popup</div>';
            } else {
                $notice = '<div class="notice notice-error">Nie usunięto popup</div>';
            }
        }

        //pobieram wiadomość do edycji
        $edit = FALSE;
        if(isset($_POST['popup_to_edit'])) {
            $edit = $this->get_popup_post($_POST['popup_post_id']);
        }

        ?>
        <div class="warp">
            <h2><span class="dashicons dashicons-welcome-write-blog"></span>POPUP Zyrex</h2>
            <?php if (isset($notice)) {
              echo esc_html($notice);
            }  else {
              echo '';
            } ?>
            <form method="POST" enctype='multipart/form-data'>
                <?php if ($edit != FALSE) {
                  echo '<input type="hidden" name="popup_post_id" value="' . esc_($edit->id) . '" />';
                  echo '<input type="hidden" name="popup_action" value="edit"/>';
                  echo '<input type="text" name="tytul" value="' . esc_($edit->tytul) . '" placeholder="Tytuł"/>';
                  echo '<input type="text" name="link" value="' . esc_($edit->link) . '" placeholder="Link"/>';
                  echo '<input type="hidden" name="img" value="' . esc_($edit->img) . '" />';
                  echo '<input type="submit" value="Edytuj popup" class="button-primary"/>';
                } else {
                  echo '<input type="hidden" name="popup_action" value="add"/>';
                  echo '<input type="text" name="tytul" value="" placeholder="Tytuł"/>';
                  echo '<input type="text" name="link" value="" placeholder="Link"/>';
                  echo '<input type="file" name="img" required>';
                  echo '<input type="submit" value="Dodaj popup" class="button-primary"/>';
                }  ?>
            </form>
            <hr>
            <?php
            $all_posts = $this->get_popup_img();
            if ($all_posts) {
                echo '<table class="widefat">';
                echo '<thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tytuł</th>
                                        <th></th>
                                        <th>Link</th>
                                        <th>Zdj</th>
                                        <td>Akcja</td>
                                    </tr>
                                </thead>';
                echo '<tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tytuł</th>
                                        <th></th>
                                        <th>Link</th>
                                        <th>Zdj</th>
                                        <th>Akcja</th>
                                    </tr>
                                </tfoot>';
                echo '<tbody>';
                foreach ($all_posts as $p) {
                  $id = $p->id ;
                  $tytul = $p->tytul;
                  $link = $p->link;
                  $img = $p->img;
                    echo '<tr>';
                    echo '<td>' . esc_html($id) . '</td>';
                    echo '<td>' . esc_html($tytul) . '</td>';
                    if ($p->active == 0) {
                      echo '<td><form method="POST">
                                          <input type="hidden" name="popup_id" value="' . esc_html($id) . '" />
                                          <input type="hidden" name="popup_action" value="aktywuj"/>
                                          <input type="submit" name="popup_aktywuj" value="Aktywuj" class="button-primary" />
                                      </form></td>';
                    } else {
                      echo '<td><form method="POST">
                                          <input type="hidden" name="popup_id" value="' . esc_html($id) . '" />
                                          <input type="hidden" name="popup_action" value="dezaktywuj"/>
                                          <input type="submit" name="popup_dezaktywuj" value="wyłącz" class="button-secondary error" />
                                      </form></td>';
                    }
                    echo '<td>' . esc_html($link) . '</td>';
                    echo '<td><img src="' . esc_html($img) . '"style="width:100px;height:auto;"></td>';
                    echo '<td><form method="POST">
                                        <input type="hidden" name="popup_post_id" value="' . esc_html($id) . '" />
                                        <input type="submit" name="popup_to_edit" value="Edytuj" class="button-primary" />
                                        <input type="submit" name="popup_delete" value="Usuń" class="button-secondary error" />
                                    </form></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            }
            ?>
            </div>
        <?php
    }

    function add_post($tytul, $link, $img) {
        //sprawdzam czy nie pusty i czy jest zalogowany
        if(trim($tytul) != ''){
          $upload_dir = wp_upload_dir();
          $upload_dir['basedir'] = $upload_dir['basedir'] . '/zyrex_popup';
            $tytul = esc_sql($tytul);
            $link = esc_sql($link);
            $img = esc_sql($img);
            $imgurl = $upload_dir['baseurl'] . '/zyrex_popup/' . $img;
            $this->wpdb->insert( $this->table_name, array('tytul' => $tytul, 'link' => $link, 'img' => $imgurl) );

            return TRUE;
        }
        return FALSE;
    }

    function get_popup_img() {
        return $this->wpdb->get_results("SELECT * FROM $this->table_name");
    }

    //funkcja służąca do pobrania wiadomości o konkretnym id
    //zwraca obiekt
    function get_popup_post($id) {
        $id = esc_sql($id);
        $popup_post = $this->wpdb->get_results("SELECT * FROM $this->table_name WHERE id = '" . $id . "'");
        if(isset($popup_post[0])){
            return $popup_post[0];
        } else {
            return FALSE;
        }
    }

    //funkcja edycji wiadomości pobiera id oraz nową treść
    function edit_post($id, $content){
        if(trim($content) != '') {
            $id = esc_sql($id);
            $content = esc_sql($content);
            $res = $this->wpdb->update($this->table_name, array('tytul' => $content), array('id' => $id));
            return $res;
        }else {
            return FALSE;
        }
    }

    function edit_active($id, $a){
        if(trim($a) != '') {
            $id = esc_sql($id);
            $a = esc_sql($a);
            $res = $this->wpdb->update($this->table_name, array('active' => $a), array('id' => $id));
            return $res;
        }else {
            return FALSE;
        }
    }

    //funkcja odpowiedzialna za usuwanie wiadomości
    function delete_post($id) {
        $id = esc_sql($id);
        return $this->wpdb->delete($this->table_name, array('id' => $id));
    }
}
