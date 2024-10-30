<?php
/*
* Plugin Name: Company History
* Description: 회사의 연혁을 목록으로 보여주는 플러그인입니다.
* Version: 1.0
* Author:  ECPlaza Network Inc.
* Author URI: http://www.ecplaza.net/
* License:  ECPlaza Network Inc.
*/

wp_enqueue_style( 'chadminoptionstyle',  plugins_url( '/css/ch-plugin-option-page.css', __FILE__ ), false );
wp_enqueue_style( 'chuserviewstyle',  plugins_url( '/css/ch-plugin-user-view-page.css', __FILE__ ), false );

register_activation_hook( __FILE__, 'ch_create_history_table' );

function ch_create_history_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'company_history';

    $table_create_query = "CREATE TABLE $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            ch_year VARCHAR(20) NOT NULL,
            ch_month VARCHAR(20) NOT NULL,
            ch_content VARCHAR(3000) NOT NULL,
            ch_seq int(10) NOT NULL,
            ch_status VARCHAR(10) NOT NULL,
            PRIMARY KEY  (id)
        );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $table_create_query );
}


add_action('admin_menu', 'ch_create_admin_menu');

function ch_create_admin_menu() {
    add_menu_page('Company History', 'Company History', 'manage_options', 'my-page-slug', 'ch_view_history_data');
}

function ch_view_history_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'company_history';

    $select_query = 'SELECT * FROM '.$table_name.' ORDER BY ch_seq desc, ch_year desc, ch_month desc';

    $result = $wpdb->get_results($select_query);
?>
    <h1>Basic Company History</h1>
    <button id="ch_plugin_modal" class="button button-primary customize load-customize hide-if-no-customize ch-plugin-data-plus">Add Data</button>
    <div class="ch-plugin-warn">
        <p> 플러그인을 사용하기 위해서는 페이지를 생성하셔야 합니다. ShortCode : [companyhistoryview] </p>
    </div>
    <div class="ch-plugins-place">
        <div class="ch-plugin-modal-show-hide ch-plugin-data-create">
            <p class="ch-plugin-modal-title">Company History</p>
            <form id="company_history_send" name="company_history_send">
                <div class="ch-year">
                    <input type="text" id="ch_year" name="ch_year" placeholder="year" value="">
                </div>
                <div class="ch-month">
                    <input type="text" id="ch_month" name="ch_month" placeholder="month">
                </div>
                <div class="ch-content">
                    <textarea id="ch_content" name="ch_content"></textarea>
                </div>
                <div class="ch-seq">
                    <input type="text" id="ch_seq" name="ch_seq" placeholder="sort">
                </div>
                <div class="ch-status">
                    <label>
                        SHOW
                        <input type="radio" class="history-status" name="ch_status" value="Y" checked>
                    </label>
                    <label>
                        HIDE
                        <input type="radio" class="history-status" name="ch_status" value="N">
                    </label>
                </div>
                <div class="ch-btn">
                    <button type="submit" class="ch-plugin-create-btn">Add</button>
                    <button type="button" class="ch-plugin-cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
        <div class="ch-plugins-box">
            <ul class="ch-plugin-data-title">
                <li>YEAR</li>
                <li>MONTH</li>
                <li>CONTENT</li>
                <li>SORT</li>
                <li>SHOW/HIDE</li>
                <li>REMARKS</li>
            </ul>
            <script>
                jQuery(document).ready(function($) {
                    $('#company_history_send').submit(function() {
                        if(!$('#ch_year').val()) {
                            alert('연도를 입력해주세요!');
                            return false;
                        } else if(!$('#ch_month').val()) {
                            alert('월을 입력해주세요!');
                            return false;
                        } else if(!$('#ch_content').val()) {
                            alert('내용을 입력해주세요!');
                            return false;
                        } else if(!$('#ch_seq').val()) {
                            alert('정렬을 입력해주세요!');
                            return false;
                        } else if(!$('.history-status:checked').val()) {
                            alert('노출여부를 선택해주세요!');
                            return false;
                        }
                        var history_data = {
                            action: 'ch_create_row',
                            year: $('#ch_year').val(),
                            month: $('#ch_month').val(),
                            content: $('#ch_content').val(),
                            seq: $('#ch_seq').val(),
                            status: $('.history-status:checked').val()
                        };
                        $.post(ajaxurl, history_data, function(response) {
                            window.history.go(0);
                        });
                        return false;
                    });
                });
            </script>
            <form id="ch_plugin_update" name="ch_plugin_update">
            <?php
                foreach ($result as $data_template) {
            ?>
            <ul id="ch_plugin_repeat_data_<?php echo $data_template->id ?>" class="ch-plugin-repeat-data">
                <li class="ch-plugin-list-row ch-plugin-repeat-data-row">
                    <div class="ch-plugin-mobile-sub-title">YEAR</div>
                    <div class="ch-plugin-mobile-data year"><?php echo $data_template->ch_year ?></div>
                </li>
                <li class="ch-plugin-repeat-data-edit">
                    <div class="ch-plugin-mobile-sub-title">YEAR</div>
                    <div class="ch-plugin-mobile-data">
                        <input type="text" id="ch_plugin_update_year_<?php echo $data_template->id ?>" name="ch_plugin_update_year_<?php echo $data_template->id ?>" value="<?php echo $data_template->ch_year ?>" class="focus-input-form">
                    </div>
                </li>
                <li class="ch-plugin-list-row ch-plugin-repeat-data-row">
                    <div class="ch-plugin-mobile-sub-title">MONTH</div>
                    <div class="ch-plugin-mobile-data month"><?php echo $data_template->ch_month ?></div>
                </li>
                <li class="ch-plugin-repeat-data-edit">
                    <div class="ch-plugin-mobile-sub-title">MONTH</div>
                    <div class="ch-plugin-mobile-data">
                        <input type="text" id="ch_plugin_update_month_<?php echo $data_template->id ?>" name="ch_plugin_update_month_<?php echo $data_template->id ?>" value="<?php echo $data_template->ch_month ?>" class="focus-input-form">
                    </div>
                </li>
                <li class="ch-plugin-list-row ch-plugin-repeat-data-row content">
                    <div class="ch-plugin-mobile-sub-title">CONTENT</div>
                    <div class="ch-plugin-mobile-content"> <?php echo $data_template->ch_content ?> </div>
                </li>
                <li class="ch-plugin-repeat-data-edit">
                    <div class="ch-plugin-mobile-sub-title">CONTENT</div>
                    <div class="ch-plugin-content">
                        <textarea id="ch_plugin_update_content_<?php echo $data_template->id ?>" name="ch_plugin_update_content_<?php echo $data_template->id ?>" class="ch-update-content focus-input-form"><?php echo $data_template->ch_content ?></textarea>
                    </div>
                </li>
                <li class="ch-plugin-list-row ch-plugin-repeat-data-row seq">
                    <div class="ch-plugin-mobile-sub-title">SORT</div>
                    <div class="ch-plugin-mobile-data"> <?php echo $data_template->ch_seq ?> </div>
                </li>
                <li class="ch-plugin-repeat-data-edit">
                    <div class="ch-plugin-mobile-sub-title">SORT</div>
                    <div class="ch-plugin-mobile-data">
                        <input type="text" id="ch_plugin_update_seq_<?php echo $data_template->id ?>" name="ch_plugin_update_seq_<?php echo $data_template->id ?>" value="<?php echo $data_template->ch_seq ?>" class="focus-input-form">
                    </div>
                </li>
                <li class="ch-plugin-list-row">
                    <div class="ch-plugin-mobile-sub-title">SHOW/HIDE</div>
                    <div class="ch-plugin-mobile-data">
                        <select class="chos-status" id="ch_plugin_status_<?php echo $data_template->id ?>" name="ch_plugin_status_<?php echo $data_template->id ?>" class="select_plugin_status">
                            <option <?php if($data_template->ch_status === "Y") echo "selected"; ?> value="Y">Y</option>
                            <option <?php if($data_template->ch_status === "N") echo "selected"; ?> value="N">N</option>
                        </select>
                    </div>
                </li>
                <li class="ch-plugin-list-row">
                    <button type="submit" class="ch-plugin-update-btn" disabled value="<?php echo $data_template->id ?>">Save</button>
                    <button type="submit" class="ch-plugin-delete-btn" value="<?php echo $data_template->id ?>">Delete</button>
                    <button type="button" class="ch-plugin-cancel-btn" value="<?php echo $data_template->id ?>">Cancel</button>
                </li>
            </ul>
            <?php
                }
            ?>
            </form>
            <script>
                jQuery(document).ready(function($) {
                    $('.ch-plugin-data-plus').click(function() {
                        $('.ch-plugin-modal-show-hide').slideDown( "slow" );
                    });
                    $('.ch-plugin-cancel-btn').click(function() {
                        $('.ch-plugin-modal-show-hide').hide();
                    });
                    $(document).mouseup(function(e) {
                        var modal = $('.ch-plugin-modal-show-hide');
                        if (!modal.is(e.target) && modal.has(e.target).length === 0){
                            modal.hide();
                            modal.prev().show();
                        }
                    });
                    $('.ch-plugin-repeat-data-edit').hide();
                    $('.ch-plugin-repeat-data-row').click(function() {
                        $('.ch-plugin-repeat-data').addClass('outher-disabled');
                        $(this).parent().removeClass('outher-disabled');
                        $(this).hide();
                        $(this).next().show();
                        $(this).next().find('.focus-input-form').focus();
                        $(this).parent().find('.ch-plugin-update-btn').removeAttr('disabled');
                        $(this).parent().find('.ch-plugin-delete-btn').hide();
                        $(this).parent().find('.ch-plugin-cancel-btn').show();
                    });
                    $('.chos-status').click(function() {
                        $('.ch-plugin-repeat-data').addClass('outher-disabled');
                        $(this).parent().parent().parent().removeClass('outher-disabled');
                        $(this).parent().parent().parent().find('.ch-plugin-update-btn').removeAttr('disabled');
                        $(this).parent().parent().parent().find('.ch-plugin-delete-btn').hide();
                        $(this).parent().parent().parent().find('.ch-plugin-cancel-btn').show();
                    });
                    $('.ch-plugin-cancel-btn').click(function() {
                        location.reload();
                    });
                    $('.ch-plugin-update-btn').click(function() {
                        var update_id = $(this).val();
                        var history_update_data = {
                            action: 'ch_update_row',
                            id: $(this).val(),
                            year: $('#ch_plugin_update_year_'+update_id).val(),
                            month: $('#ch_plugin_update_month_'+update_id).val(),
                            content: $('#ch_plugin_update_content_'+update_id).val(),
                            seq: $('#ch_plugin_update_seq_'+update_id).val(),
                            status: $('#ch_plugin_status_'+update_id+' option:checked').val()
                        };
                        $.post(ajaxurl, history_update_data, function(response) {
                            window.history.go(0);
                        });
                        return false;
                    });
                    $('.ch-plugin-delete-btn').click(function() {
                        var yes_or_no = confirm('정말로 삭제하시겠습니까?');
                        if(yes_or_no) {
                            var delete_id = $(this).val();
                            var history_delete_data = {
                                action: 'ch_delete_row',
                                id: $(this).val()
                            };
                            $.post(ajaxurl, history_delete_data, function(response) {
                                window.history.go(0);
                            });
                            return false;
                        } else {
                            return false;
                        }
                    });
                });
            </script>
        </div>
    </div>
<?php
}

function companyHistory($atts, $content = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'company_history';
    $select_years = 'SELECT ch_year FROM '.$table_name.' WHERE ch_status="Y" GROUP BY ch_year ORDER BY ch_year desc';
    $ch_years = $wpdb->get_results($select_years);
    ?>
    <div class="ch-plugin-widget-box">
        <div class="ch-plugin-widget-place">
            <div class="ch-plugin-years-box">
                <ul class="ch-plugin-repeat-list">
                    <?php
                    $year_array_list = [];
                    foreach ($ch_years as $ch_menu_year) {
                        $replace_str = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $ch_menu_year->ch_year);
                        array_push($year_array_list, $replace_str);
                    ?>
                        <li class="year-data-btns-hide year-data-btns-hide-<?php echo $replace_str; ?> ch-plugin-repeat-list-item limit-view-block">
                            <button class="year-send-data-btn" value="<?php echo $replace_str; ?>"><?php echo $ch_menu_year->ch_year; ?></button>
                        </li>
                    <?php } ?>
                    <li class="ch-plugin-repeat-list-item">
                        <button id="ch_plugin_next_btn" class="ch-plugin-next-bth"> > </button>
                    </li>
                </ul>
            </div>
            <div class="ch-plugin-data-box">
                <?php
                $select_months = 'SELECT * FROM '.$table_name.' WHERE ch_status="Y" GROUP BY ch_month ORDER BY ch_month desc';
                $ch_months = $wpdb->get_results($select_months);
                $month_array_list = [];
                $js_month_array = [];
                foreach ($ch_months as $key => $array_month) {
                    array_push($month_array_list, $array_month->ch_month);
                    array_push($js_month_array, $array_month->ch_month);
                }
                $select_datas = 'SELECT * FROM '.$table_name.' WHERE ch_status="Y" ORDER BY ch_month desc';
                $ch_datas = $wpdb->get_results($select_datas);

                foreach ($year_array_list as $ch_year_repeac) {
                    $replace_year_str = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $ch_year_repeac);
                ?>
                    <div class="hide-list-group-hide hide-list-group year-data-<?php echo $replace_year_str; ?>">
                        <?php
                        foreach ($month_array_list as $ch_month_repeac) {
                            $replace_month_str = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $ch_month_repeac);
                        ?>
                        <div class="list-month-group list-month-group-<?php echo $replace_month_str; ?>"><div class="month-group-title"><?php echo $ch_month_repeac; ?></div><?php foreach ($ch_datas as $ch_data_content) {
                                $compare_year_str = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $ch_data_content->ch_year);
                                $compare_month_str = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "", $ch_data_content->ch_month);
                                if($compare_year_str === $replace_year_str) {
                                    if($compare_month_str === $replace_month_str) {
                            ?><div class="list-content-data"><?php echo $ch_data_content->ch_content; ?></div>
                            <?php } } } ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <script>
                    jQuery(document).ready(function($) {
                        $('.list-month-group:empty').remove();
                        $('.list-month-group').each(function() {
                            if($(this).children('.list-content-data').length === 0) {
                                $(this).remove();
                            }
                        });

                        var limit_block = 3;
                        var first_year_block = <?php echo json_encode($year_array_list)?>;
                        for(var view_blcok = 0; view_blcok < limit_block; view_blcok++) {
                            $('.year-data-btns-hide-'+first_year_block[view_blcok]).css('display', 'block');
                        }
                        $('.year-data-btns-hide-'+first_year_block[0]).addClass('active');
                        $('.year-data-'+first_year_block[0]).show();

                        $('.year-send-data-btn').click(function() {
                            $('.hide-list-group-hide').hide();
                            $('.year-data-btns-hide').removeClass('active');
                            var show_years = $(this).val();
                            $('.year-data-'+show_years).show();
                            $(this).parent().addClass('active');
                        });

                        $('#ch_plugin_next_btn').click(function() {
                            var now_btn = $('.active button').val();
                            var years_block_list = <?php echo json_encode($year_array_list)?>;
                            var now_btn_index = $.inArray(now_btn, years_block_list);
                            var prev_btn_index2 = $.inArray(now_btn, years_block_list)-2;
                            var prev_btn_index = $.inArray(now_btn, years_block_list)-1;
                            var next_btn_index = $.inArray(now_btn, years_block_list)+1;
                            var next_btn_index2 = $.inArray(now_btn, years_block_list)+2;
                            var next_btn_index3 = $.inArray(now_btn, years_block_list)+3;

                            if(now_btn_index === years_block_list.length - 1) {
                                $('.year-data-btns-hide').hide();
                                $('.hide-list-group-hide').hide();
                                $('.year-data-btns-hide').removeClass('active');

                                var limit_view_block = 3;
                                for(var view_blcok = 0; view_blcok < limit_view_block; view_blcok++) {
                                    $('.year-data-btns-hide-'+years_block_list[view_blcok]).css('display', 'block');
                                }
                                $('.year-data-btns-hide-'+years_block_list[0]).addClass('active');
                                $('.year-data-'+years_block_list[0]).show();
                                $('.blank-block').remove();
                            } else {
                                $('.hide-list-group-hide').hide();
                                $('.year-data-btns-hide').removeClass('active');

                                $('.year-data-btns-hide-'+years_block_list[prev_btn_index]).hide();
                                $('.year-data-btns-hide-'+years_block_list[prev_btn_index2]).hide();
                                $('.year-data-btns-hide-'+years_block_list[now_btn_index]).hide();

                                if(next_btn_index2 >= years_block_list.length) {
                                    $('.blank-block').remove();
                                    $('.year-data-btns-hide-'+years_block_list[years_block_list.length-1]).after('<li class="blank-block">empty</li>');
                                    $('.blank-block').after('<li class="blank-block">empty</li>');
                                } else if(next_btn_index3 >= years_block_list.length) {
                                    $('.blank-block').remove();
                                    $('.year-data-btns-hide-'+years_block_list[years_block_list.length-1]).after('<li class="blank-block">empty</li>');
                                }

                                $('.year-data-btns-hide-'+years_block_list[next_btn_index]).css('display', 'block');
                                $('.year-data-btns-hide-'+years_block_list[next_btn_index]).addClass('active');
                                $('.year-data-'+years_block_list[next_btn_index]).show();
                                $('.year-data-btns-hide-'+years_block_list[next_btn_index2]).css('display', 'block');
                                $('.year-data-btns-hide-'+years_block_list[next_btn_index3]).css('display', 'block');
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>
<?php
}

add_shortcode('companyhistoryview', 'companyHistory');

add_action( 'wp_ajax_ch_create_row', 'ch_create_history_row' );
add_action( 'wp_ajax_nopriv_ch_create_row', 'ch_create_history_row' );

function ch_create_history_row() {
    $chYear = sanitize_text_field( $_POST['year'] );
    $chMonth = sanitize_text_field( $_POST['month'] );
    $chContent = sanitize_text_field( $_POST['content'] );
    $chSeq = sanitize_text_field( $_POST['seq'] );
    $chStatus = sanitize_text_field( $_POST['status'] );

    global $wpdb;

    $wpdb->query( $wpdb->prepare( 'INSERT INTO wp_company_history (ch_year, ch_month, ch_content, ch_seq, ch_status)
        SELECT * FROM (SELECT "'.$chYear.'", "'.$chMonth.'", "'.$chContent.'", "'.$chSeq.'", "'.$chStatus.'") AS tmp
        WHERE NOT EXISTS (
            SELECT ch_seq FROM wp_company_history WHERE ch_seq = "'.$chSeq.'"
        ) LIMIT 1',
        10 ) );
}

add_action( 'wp_ajax_ch_update_row', 'ch_update_history_row' );
add_action( 'wp_ajax_nopriv_ch_update_row', 'ch_update_history_row' );

function ch_update_history_row() {
    $chId = intval( $_POST['id'] );
    $chYear = sanitize_text_field( $_POST['year'] );
    $chMonth = sanitize_text_field( $_POST['month'] );
    $chContent = sanitize_text_field( $_POST['content'] );
    $chSeq = sanitize_text_field( $_POST['seq'] );
    $chStatus = sanitize_text_field( $_POST['status'] );

    global $wpdb;

    $wpdb->update(
        'wp_company_history',
        array(
            'ch_year' => $chYear,
            'ch_month' => $chMonth,
            'ch_content' => $chContent,
            'ch_seq' => $chSeq,
            'ch_status' => $chStatus
        ),
        array( 'id' => $chId ),
        array(
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
        ),
        array( '%d' )
    );
}

add_action( 'wp_ajax_ch_delete_row', 'ch_delete_history_row' );
add_action( 'wp_ajax_nopriv_ch_delete_row', 'ch_delete_history_row' );

function ch_delete_history_row() {
    $chId = intval( $_POST['id'] );

    global $wpdb;

    $wpdb->query( $wpdb->prepare( 'DELETE FROM wp_company_history where id = '.$chId ));
}

add_action('widgets_init', 'ch_plugin_widget_show');

function ch_plugin_widget_show () {
    register_widget('chPluginWidgetShow');
}

class chPluginWidgetShow extends WP_Widget {
    public function __construct() {
        $widget_ops = array(' ' => 'ch_plugin_widget_show', 'description'=> '회사 연혁 플러그인입니다.');
        $this->WP_Widget('ch_plugin_widget_show','Company History Widget',$widget_ops);
    }

    public function form($instance) {
        // 관리자 화면에서 보여줄 내용
    }

    function update($new_instance, $old_instance){
        // 관리자 화면에서 넘어온 값을 처리하는 곳
    }

    function widget($args, $instance){

    }
}

?>