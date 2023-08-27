<?php
/*
Plugin Name: FI Users
Description: Show All Affiliates (Phone and Type)
Version: 1.6
Author: Hatem Amir
Author URI: https://github.com/notsaaad
Email: amirhatem549@gmail.com
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ){
  die;
}


add_action('admin_menu', 'FI_users');
function FI_users() {
	add_menu_page(
		'FI_users',
		'FI users',
		'manage_options',
		'FI-users',
		'FI_Users_admin_page',//Call Back Function

	);
	// add_submenu_page('FI_users', 'AFF_C_Users', 'AFF Customer Users', 'manage_options', 'FI_users-Add', 'FI_List_custormer');


}

function FI_Users_admin_page(){
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
  $url = "https://";
else
  $url = "http://";
// Append the host(domain name, ip) to the URL.
  $url.= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
  $url.= $_SERVER['REQUEST_URI'];


  $url_components = parse_url($url);
  parse_str($url_components['query'], $params);

  $pramContent = $params['content'] ?? '';
  global $wpdb;
  ?>
  <style>
.table-g1 td{
  font-size: 16px;
}
.table-g1 table {
  border: 1px solid #ccc;
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  width: 100%;
  table-layout: fixed;
}

.table-g1 table caption {
  font-size: 1.5em;
  margin: .5em 0 .75em;
}

.table-g1 table tr {
  background-color: #f8f8f8;
  border: 1px solid #ddd;
  padding: .35em;
}

.table-g1 table th,
.table-g1 table td {
  padding: .625em;
  text-align: center;
}

.table-g1 table th {
  font-size: 18px;
  weight:bold;
  letter-spacing: .1em;

}

@media screen and (max-width: 600px) {
  .table-g1 table {
    border: 0;
  }

  .table-g1 table caption {
    font-size: 1.3em;
  }

  .table-g1 table thead {
    border: none;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
  }

  .table-g1 table tr {
    border-bottom: 3px solid #ddd;
    display: block;
    margin-bottom: .625em;
  }

  .table-g1 table td {
    border-bottom: 1px solid #ddd;
    display: block;
    font-size: 16px !important;
    text-align: right;
  }

  .table-g1 table td::before {
    /*
    * aria-label has no advantage, it won't be read inside a table
    content: attr(aria-label);
    */
    content: attr(data-label);
    float: left;
    font-weight: bold;

  }

  .table-g1 table td:last-child {
    border-bottom: 0;
  }
}

.Fi-list-buttons{
  width:100%;
  display:flex;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
  </style>


  <?php
  if ($pramContent == 'g2'){

    $args = array(
      'role'    => 'g2'
      // 'orderby' => 'user_nicename',
      // 'order'   => 'ASC'
  );
  $users_G2 = get_users( $args );

  ?>
    <div class="wrap">

    <div class="Fi-list-buttons">
      <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">g1</button></a>
      <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users&content=c"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">C</button></a>
    </div>

      <div class="table-g1">
      <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Gmail</th>
          <th>Phone</th>
          <th>type</th>
          <th>country</th>
          <th>Parent</th>
        </tr>
      </thead>
      <tbody>
      <?php
      foreach ( $users_G2 as $user ) {
        $phone = get_user_meta($user->ID,'phone',true);
        $country = get_user_meta($user->ID ,'billing_country',true);
        /*-----------------Getting g2_id--------------------*/
        $table_name_aff = "wp40_uap_affiliates";
        $user_aff_id = $wpdb->get_results("SELECT * FROM $table_name_aff where (uid = $user->ID)");
        /*-----------------Getting g2_parent_id--------------------*/
        $table_name_aff_mlm = "wp40_uap_mlm_relations";
        $user_aff_ID = $user_aff_id[0]->id;
        $user_aff_Parent_id = $wpdb->get_results("SELECT * FROM $table_name_aff_mlm where (affiliate_id = $user_aff_ID )");
        $user_aff_Parent_ID = $user_aff_Parent_id[0]->	parent_affiliate_id;
        /*-----------------Getting g2_parent_uid--------------------*/

        $user_aff_parent_uid = $wpdb->get_results("SELECT * FROM $table_name_aff where ( id = $user_aff_Parent_ID)");
        $user_aff_parent_UID =  $user_aff_parent_uid[0]->uid;

        /*-----------------Getting g2_parent_Name--------------------*/
        $user_parent_name_fristName = get_user_meta($user_aff_parent_UID,'first_name',true);
        $user_parent_name_LastName = get_user_meta($user_aff_parent_UID,'last_name',true);
        $user_parent_name = $user_parent_name_fristName . ' ' . $user_parent_name_LastName;
        if ($user_parent_name == "" || $user_parent_name == " "){
          $user_parent_name = get_user_meta($user_aff_parent_UID,'nickname',true);
        }
        if ($phone ==""){
          $phone = get_user_meta($user->ID,'billing_phone',true);
        }
        if ($country ==""){
          $country = get_user_meta($user->ID,'uap_country',true);
        }
        // echo '<li>' . esc_html( $user->display_name ) . '[' . esc_html( $user->user_email ) . ']</li>';
        echo '<tr><td>'. $user->display_name . '</td>
                  <td>'.$user-> user_email .'</td>
                  <td>'. $phone .'</td>
                  <td>G2</td>
                  <td>'.$country.'</td>
                  <td>'.$user_parent_name .'</td></tr>';

      }
      ?>
      </tbody>
    </table>
      </div>


    </div>


  <?php




  }else if($pramContent == 'c'){

?>


  <div class="wrap">
    <div class="Fi-list-buttons">
    <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">g1</button></a>
    <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users&content=g2"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">g2</button></a>
    </div>
    <div class="table-g1">
      <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
          <th>type</th>
          <th>country</th>
          <th>Parent</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $tabel_name_cus = "wp40_uap_affiliate_referral_users_relations";
      $results_c  = $wpdb->get_results("SELECT * FROM $tabel_name_cus");
      foreach ($results_c as $result_c) {

        $user = get_user_by( 'id', $result_c->referral_wp_uid );

        if($user->roles[0] == 'g1' || $user->roles[0] == 'G1' || $user->roles[0] == 'G2' || $user->roles[0] == 'g2' ){
          continue;
        }

        $parent_aff_id = $result_c->affiliate_id;

        $tabel_aff_name = "wp40_uap_affiliates";
        $result_aff_name = $wpdb->get_results("SELECT * FROM $tabel_aff_name Where id=$parent_aff_id");
        $parent_name_fristName = get_user_meta($result_aff_name[0]->uid , 'first_name',true);
        $parent_name_lastName = get_user_meta($result_aff_name[0]->uid , 'last_name',true);
        $parent_name =  $parent_name_fristName . ' ' . $parent_name_lastName;
        $name = get_user_meta($result_c->referral_wp_uid, 'nickname',true);
        $phone = get_user_meta($result_c->referral_wp_uid, 'billing_phone',true);
        $type = 'c1';
        $table_name_aff_mlm = "wp40_uap_mlm_relations";
        $results_IS_c2 = $wpdb->get_results("SELECT * FROM  $table_name_aff_mlm  where (affiliate_id =  $result_c->affiliate_id )");
        if (! empty($results_IS_c2)){
          $type= 'c2';
        }
        $countryy = get_user_meta($result_c->referral_wp_uid, 'billing_country',true);

        // echo '<li>' . esc_html( $user->display_name ) . '[' . esc_html( $user->user_email ) . ']</li>';
        echo '<tr><td>'. $name . '</td>
        <td>'. $phone .'</td>
        <td>'. $type .'</td>
        <td>'.$countryy.'</td>
        <td>'.$parent_name .'</td></tr>';
      }


      ?>
      </tbody>
    </table>
  </div>
<?php


}else{







  //==================================
  $args = array(
    'role'    => 'g1'
    // 'orderby' => 'user_nicename',
    // 'order'   => 'ASC'
);
$users_G1 = get_users( $args );

?>
  <div class="wrap">

  <div class="Fi-list-buttons">
    <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users&content=g2"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">g2</button></a>
    <a href="https://sawyancom.com/wp-admin/admin.php?page=FI-users&content=c"><button class="button button-primary" style="margin:10px; padding:8px 27px; font-weight:bold">C</button></a>
  </div>

    <div class="table-g1">
    <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Gmail</th>
        <th>Phone</th>
        <th>type</th>
        <th>country</th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ( $users_G1 as $user ) {
      $phone = get_user_meta($user->ID,'phone',true);
      $country = get_user_meta($user->ID ,'billing_country',true);
      if ($phone ==""){
        $phone = get_user_meta($user->ID,'billing_phone',true);
      }
      if ($country ==""){
        $country = get_user_meta($user->ID,'uap_country',true);
      }
      // echo '<li>' . esc_html( $user->display_name ) . '[' . esc_html( $user->user_email ) . ']</li>';
      echo '<tr><td>'. $user->display_name . '</td>
                <td>'.$user-> user_email .'</td>
                <td>'. $phone .'</td>
                <td>G1</td>
                <td>'.$country.'</td></tr>';

    }
    ?>
    </tbody>
  </table>
    </div>

  </div>


  <?php
}









}