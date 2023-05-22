<?php

include_once('xlsxwriter.class.php');

global $wpdb;

if (isset($_GET['export_booking_order'])) {

    $store_id = $_GET['store_id'];

    $table = array('Mã đặt', 'Mã cửa hàng', 'Họ tên khách hàng', 'Số điện thoại', 'Email', 'Ghi Chú', 'Ngày tạo', 'Đặt ngày', 'Từ', 'Đến', 'Trạng thái');

    if ($_GET['store_id']) {
        
        $data = $wpdb->get_results("SELECT id, store_id, name, phone, email, notes, create_date, booking_date, booking_time_start, booking_time_end, status FROM dafc_booking_order where store_id= $store_id ");
    
    } else {
        $data = $wpdb->get_results("SELECT id, store_id, name, phone, email, notes, create_date, booking_date, booking_time_start, booking_time_end, status FROM dafc_booking_order");
    }   
    $file = 'booking_order_store_' . rand(10000000, 99999999) . '.xlsx';

    $data_exp = json_decode(json_encode($data), true);

    $writer = new XLSXWriter();
    $writer->writeSheetRow('Sheet1', $table);
    $writer->writeSheet($data_exp);
    $writer->writeToFile($file);

    header('Content-Description: File Transfer');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=" . basename($file));
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Length: ' . filesize($file));

    ob_clean();
    flush();

    readfile($file);
    unlink($file);
    exit;
}


if (isset($_GET['export_info_customers'])) {

    $type = $_GET['type'];

    $table_info = array('Mã khach hang', 'Type', 'Họ tên khách hàng', 'Số điện thoại', 'Email', 'Giới tính', 'Version', 'Capacity', 'Color', 'utm', 'user_agent', 'area', 'create_date', 'birthday', 'nationality');

    if ($_GET['type']) {

        $info = $wpdb->get_results("SELECT id, type, name, phone, email, sex, version, capacity, color, utm, user_agent, area, create_date, birthday, nationality FROM dafc_info_customer where type = $type  ");
    } 
    else {
        $info = $wpdb->get_results("SELECT id, type, name, phone, email, sex, version, capacity, color, utm, user_agent, area, create_date, birthday, nationality FROM dafc_info_customer");
    }

    $file = 'booking_info_customers_' . rand(10000000, 99999999) . '.xlsx';
    $data_exp_info = json_decode(json_encode($info), true);

    $writer = new XLSXWriter();
    $writer->writeSheetRow('Sheet1', $table_info);
    $writer->writeSheet($data_exp_info);
    $writer->writeToFile($file);

    header('Content-Description: File Transfer');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=" . basename($file));
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Pragma: public");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Length: ' . filesize($file));

    ob_clean();
    flush();

    readfile($file);
    unlink($file);
    exit;
}


function add_admin_export()
{
    add_menu_page(
        'Export',
        'Export',
        'edit_posts',
        'export_file',
        'export_function',
        'dashicons-database-export',
        '10'
    );
}

add_action('admin_menu', 'add_admin_export');

function export_function() {
    
    global $wpdb;

    $get_type_customer = $wpdb->get_results("SELECT DISTINCT type FROM dafc_info_customer");

    $get_booking_order = $wpdb->get_results("SELECT DISTINCT store_id FROM dafc_booking_order");

?>
    <style>
        .bnt_action_add {
            padding: 5px 10px;
            width:100px;
            border: 1px solid #2271b1;
            font-weight: 600;
            font-size: 14px;
            line-height: normal;
            color: #2271b1;
            cursor: pointer;
        }

        .bnt_action_add>a {
            text-decoration: none;
        }

        select {
            width: 135px;
        }

        .export {
            width:100%; 
            height:50vh;
        }

        .column-export {
            width:100%; 
            height:100px; 
            background: white; 
            padding:20px;
        }

        .select_change_value {
            width:100px; 
            margin-left: 100px;
        }

    </style>

    <div class="export">
        <div class="column-export">
            <p><b>EXPORT BOOK AN APPOINTMENT</b></p>
            <div style="display:flex">
                <div id='btnExport_booking_order' class="bnt_action_add">
                    <a id="type_booking_order" href="admin.php?page=export_booking_order&export_booking_order=table&header=1">Export File</a>
                </div>
                <div class="select_change_value" >
                    <select name="exp_booking" id="exp_booking">
                        <option value="0" selected="selected"><?php echo 'Store' ?></option>
                        <?php
                        foreach ($get_booking_order as $order) { ?>
                            <option value="<?php echo $order->store_id;  ?>">
                                <?php echo $order->store_id;  ?>
                            </option> <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="column-export" style="background:#c1c1c1">
            <p><b>EXPORT INFO CUSTOMER</b></p>
            <div style="display:flex">
                <div id='btnExport_info_cus' class="bnt_action_add">
                    <a id="type_info_cus" href="admin.php?page=export_info_customers&export_info_customers=table&header=1">Export File</a>
                </div>
                <div class="select_change_value">
                    <select name="exp_info" id="exp_info">
                        <option value="0" selected="selected"><?php echo 'Type' ?></option>
                        <?php
                        foreach ($get_type_customer as $result) { ?>
                            <option value="<?php echo $result->type;  ?>">
                                <?php echo $result->type; ?>
                            </option> <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <script>
        jQuery(function($) {

            $("#btnExport_booking_order").click(function() {
                link = 'admin.php?page=export_info_customers&export_info_customers=table&header=1';
                store_id = jQuery("#exp_booking option:selected").val();
                let new_href_booking = $('#type_booking_order').attr('href') + '&store_id=' + store_id;
                $('#type_booking_order').attr('href', new_href_booking);
            })

            $("#btnExport_info_cus").click(function() {
                link = 'admin.php?page=export_info_customers&export_info_customers=table&header=1';
                type = jQuery("#exp_info option:selected").val();
                let new_href_info = $('#type_info_cus').attr('href') + '&type=' + type;
                $('#type_info_cus').attr('href', new_href_info);
            })
        })
    </script>

<?php

}

?>