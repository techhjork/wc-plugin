<?php


add_action('woocommerce_before_order_notes', 'tdwd_add_extra_fields');
function tdwd_add_extra_fields( $checkout ) {
     echo '<h2>'.__('Location').'</h2>';
    woocommerce_form_field( 'Postcode', array(
        'type'          => 'text',
        'id'            => 'Postcode',
        'class'         => array( 'tdwd-text' ),
        'label'         => __( 'Postcode' ),
        'placeholder'   => __( 'Please enter your postcode' )
    ),$checkout->get_value( 'Postcode' ));

    woocommerce_form_field( 'Location', array(
        'type'          => 'select',
        'class'         => array( 'tdwd-dropdown' ),
        'label'         => __( 'Fitting Location' ),
        'id'            => 'Location',
        'options'       => array(
            'unselected'    => __( 'Please Enter a Valid Postal Code', 'tdwd' ),
        )
    ),$checkout->get_value( 'Location' ));
}


add_filter( 'woocommerce_checkout_fields', 'tdwd_remove_billing_fields' );
function tdwd_remove_billing_fields($fields) {
    // unset($fields['billing']['billing_first_name']);
    // unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_state']);
    unset($fields['order']['order_comments']);
    // unset($fields['billing']['billing_email']);
    // unset($fields['billing']['billing_phone']);
    unset($fields['billing']['billing_postcode']);
    return $fields;
}



//AJAX
add_action( 'wp_footer', 'tdwd_add_js_to_checkout', 9999 );
function tdwd_add_js_to_checkout() {
    global $wp;
    if ( is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
    ?>
    <script>
        jQuery(document).ready(function( $ ) {
            var pressInterval = 0;
            $('#Postcode').on('keyup', function() {
                let value = $(this).val();
                let dropdown = $('#Location');
                clearInterval(pressInterval)
                pressInterval = setInterval(function() {
                    clearInterval(pressInterval);
                    var ajax = "<?php echo get_home_url(); ?>/wp-admin/admin-ajax.php";
                    $.ajax ({
                        type: "post",
                        url:  ajax + '?action=get_fitting_locations',
                        data: {
                            postcode: value
                        },
                        success: function(data) {
                            console.log(data);
                            dropdown.empty();
                            dropdown.append($('<option>Please Select one ----</option>'));
                            $.each(JSON.parse(data), function (i, p) {
                                dropdown.append($('<option></option>').val(p.LocationName).html(p.AddressString));
                            });
                        }
                    });
                }, 2000);
            });

        });
    </script>
    <?php
    }
}



add_action( 'wp_ajax_get_fitting_locations', 'tdwd_get_fitting_locations' );
add_action( 'wp_ajax_nopriv_get_fitting_locations', 'tdwd_get_fitting_locations' );
function tdwd_get_fitting_locations() {
    $postcode = $_POST['postcode'];
     global $wpdb,$table_prefix;
    $wp_emp = $table_prefix."postal_location";


    $addresses = array();
    if(empty($postcode)){
        $q="SELECT * from `$wp_emp`" ;
    }else{
         $q = "SELECT * FROM `$wp_emp` WHERE 
        `postal_code` LIKE '%".$postcode."%'";
        //OR `address` LIKE '%".$search_term."%'
        //OR `status` LIKE '%".$search_term."%';";
    }
    $results = $wpdb->get_results($q);
    foreach($results as $row):
        $addresses[] = array(
                "LocationName" => $row->address,
                "AddressString" => $row->address
        );
    endforeach;
    wp_reset_postdata();

    echo json_encode($addresses);
     echo ob_get_clean();
    wp_die();
    exit();
}




add_action( 'woocommerce_checkout_update_order_meta', 'dack25_save_extra_checkout_fields', 10, 1 );
function dack25_save_extra_checkout_fields( $order_id ){
    if( isset($_POST['Postcode'] ) ) {
     update_post_meta( $order_id, '_Postcode', sanitize_text_field( $_POST['Postcode'] ) );
    }

    if( isset( $_POST['Location'] ) ) {
        update_post_meta( $order_id, '_Location', sanitize_text_field( $_POST['Location'] ) );
    }
}




add_action('woocommerce_checkout_process', 'dack25_check_if_selected');
function dack25_check_if_selected() {

    // you can add any custom validations here
    if ( empty( $_POST['Location'] ) )
        wc_add_notice( 'Please Location by pin code.', 'error' );

    if ( empty( $_POST['Postcode'] ) )
        wc_add_notice( 'Please enter pincode and select appropriate location.', 'error' );
    
}


// Display the Data to User
function dack25_display_order_data( $order_id ){  ?>
    <h2><?php _e( 'Extra Information' ); ?></h2>
    <table class="shop_table shop_table_responsive additional_info">
        <tbody>
            <tr>
                <th><?php _e( 'Postcode:' ); ?></th>
                <td><?php echo get_post_meta( $order_id, '_Postcode', true ); ?></td>
            </tr>
             <tr>
                <th><?php _e( 'Location:' ); ?></th>
                <td><?php echo get_post_meta( $order_id, '_Location', true ); ?></td>
            </tr>
        </tbody>
    </table>
<?php }
add_action( 'woocommerce_thankyou', 'dack25_display_order_data', 20 );
add_action( 'woocommerce_view_order', 'dack25_display_order_data', 20 );



// display data on the Dashboard WC order details page
function dack25_display_order_data_in_admin( $order ){  ?>
    <div class="order_data_column">
        <h4><?php _e( 'Additional Information', 'woocommerce' ); ?><a href="#" class="edit_address"><?php _e( 'Edit', 'woocommerce' ); ?></a></h4>
        <div class="address" style="width: 250px">
        <?php
            echo '<p><strong>' . __( 'Additional Field' ) . ':</strong></p>';
            echo '<p> Postcode : '.get_post_meta( $order->id, '_Postcode', true ) . '</p>';
            echo '<p> Location : '.get_post_meta( $order->id, '_Location', true ) . '</p>'; 
        ?>
        </div>


        <div class="edit_address">
            <?php woocommerce_wp_text_input( array( 'id' => '_Postcode', 'label' => __( 'Postcode' ), 'wrapper_class' => '_billing_company_field' ) ); ?>
        </div>
        <div class="edit_address">
            <?php woocommerce_wp_text_input( array( 'id' => '_Location', 'label' => __( 'Location' ), 'wrapper_class' => '_billing_company_field' ) ); ?>
        </div>
    </div>
<?php }
add_action( 'woocommerce_admin_order_data_after_order_details', 'dack25_display_order_data_in_admin' );

function dack25_save_extra_details( $post_id, $post ){
    update_post_meta( $post_id, '_Postcode', wc_clean( $_POST[ '_Postcode' ] ) );
    update_post_meta( $post_id, '_Location', wc_clean( $_POST[ '_Location' ] ) );
}
// save data from admin
add_action( 'woocommerce_process_shop_order_meta', 'dack25_save_extra_details', 45, 2 );




// add the field to email template
function dack25_email_order_meta_fields( $fields, $sent_to_admin, $order ) {
    $fields['email'] = array(
                'label' => __( 'Location' ),
                'value' => get_post_meta( $order->id, '_Location', true ),
            );
    return $fields;
}
add_filter('woocommerce_email_order_meta_fields', 'dack25_email_order_meta_fields', 10, 3 );
?>
   