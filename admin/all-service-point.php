<?php
global $wpdb, $table_prefix;
$wp_emp = $table_prefix.'postal_location';
if(isset($_POST['	'])){
    $q = "SELECT * FROM `$wp_emp` WHERE `name` LIKE '%".$_POST['search_term']."%';";
}else{
    $q = "SELECT * FROM `$wp_emp`;";
}
$results = $wpdb->get_results($q);
ob_start();
?>
<div class="wrap">
	<h1 class="wp-heading-inline">Service Points</h1>
	<a href="<?php echo admin_url('admin.php?page=add-service-points') ?>" class="page-title-action">Add New Service Point</a>
</div>
<div class="wrap top mb-2">
	<div class="my-form">
		<form action="<?php echo admin_url('admin.php');?>" id="my-search-form">
		<p class="search-box" style="margin-bottom: 10px;">
			<input type="hidden" name="page" value="service-points"/>
			<input type="text" name="search_term" id="search_term"/>
			<input type="submit" class="button" name="search" value="search"/>
		</p>
		</form>
	</div>
</div>
<div class="wrap">
<table class="wp-list-table widefat fixed striped table-view-list posts" cellspacing="0">
    <thead>
    <tr>

            <th id="cb" class="manage-column column-title column-primary" scope="col">ID</th> 
            <th id="columnname" class="manage-column column-title column-primary" scope="col">Postal Code</th>
            <th id="columnname" class="manage-column column-tags" scope="col">Address</th>
            <th id="columnname" class="manage-column column-tags" scope="col">Description</th>
            <th id="columnname" class="manage-column column-tags" scope="col">status</th>

    </tr>
    </thead>

    <tbody id="my-table-result"> 
        <?php
	     if(!empty($results)):
         foreach($results as $row):?>
	         <tr>
	            <th class="title column-title has-row-actions column-primary page-title" scope="row"><?php echo $row->id;?></th>
	            <td class="column-columnname"><?php echo $row->postal_code; ?></td>
	            <td class="column-columnname"><?php echo $row->address; ?> </td>
	            <td class="column-columnname"><?php echo $row->description;?></td>
	            <td class="column-columnname"><?php echo $row->status;?></td>
	        </tr>
        <?php endforeach;
    endif;
         ?>
    </tbody>
    <tfoot>
    <tr>

            <th class="manage-column column-cb check-column" scope="col"></th>
            <th class="manage-column column-title column-primary" scope="col"></th>
            <th class="manage-column column-title column-primary num" scope="col"></th>
            <th class="manage-column column-title column-primary num" scope="col"></th>
            <th class="manage-column column-title column-primary num" scope="col"></th>         

    </tr>
    </tfoot>
</table>
</div>


