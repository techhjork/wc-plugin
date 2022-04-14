<?php

if(isset($_POST["createservice"]))
{
  $postal_code = $_POST["postal_code"];
  $service_address = $_POST["service_address"];
  $service_description = $_POST["service_description"];
  $service_status = $_POST["service_status"];

  global $wpdb,$table_prefix;
  $wp_emp = $table_prefix."postal_location";
  $wpdb->insert($wp_emp, array(
      'postal_code' => $postal_code,
      'address' => $service_address,
      'description' => $service_description, 
      'status' => $service_status, 
      "time"=>date("Y/m/d g:i:s A"),
  ));
}



?>

<div class="wrap">
  <h1 id="add-new-user">Add Service Point</h1>

  <div id="ajax-response"></div>

  <p>Create a service point for customers</p>
  <form
    method="post"
    name="createuser"
    id="createuser"
    class="validate"
    novalidate="novalidate"
  >
    <table class="form-table" role="presentation">
      <tbody>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="postal_code"
              >Postal Code <span class="description">(required)</span></label
            >
          </th>
          <td>
            <input
              name="postal_code"
              type="text"
              id="postal_code"
              value=""
              aria-required="true"
              autocapitalize="none"
              autocorrect="off"
              maxlength="60"
            />
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="service_address"
              >Address <span class="description">(required)</span></label
            >
          </th>
          <td><input name="service_address" type="email" id="service_address" value="" /></td>
        </tr>

        <tr class="form-field user-description-wrap">
          <th scope="row"><label for="service_description">Description</label></th>
          <td>
            <textarea name="service_description" style="width:500px"id="service_description" rows="5" cols="30"></textarea>
          </td>
        </tr>

    
        <tr class="form-field">
          <th scope="row"><label for="role">Status</label></th>
          <td>
            <select name="service_status" id="role">
              <option disabled selected>Choose Status</option>
              <option value="closed">Closed</option>
              <option value="opened">Opened</option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>

    <p class="submit">
      <input
        type="submit"
        name="createservice"
        id="createservice"
        class="button button-primary"
        value="Add New Service Point"
      />
    </p>
  </form>
</div>
