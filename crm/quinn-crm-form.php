<?php 
/*
* we also have access to:
* Qlist_Table (object)
* Q_crud (object)
* Q_helpers (static methods)
*
* this file is within quinn-crm.php this file is so $this is QuinnCrm class
*/

global $wpdb;
//$table = new Q_Crud( "{$wpdb->prefix}{$this->qtable}" );
	if($_GET['action'] == 'edit' && $_GET['added'] == 'added'){
		?>
			<div id="message" class="updated"><p>New user added!</p></div>
			<?php
	}
	if($_GET['action'] == 'edit' && isset($_POST['q_forename']) )
	{
		
		$post_update = $_POST;
		$post_update['q_updated'] = date("Y-m-d H:i:s");
		$post_update['q_id'] 	  = $_GET['q_id'];
		unset($post_update['submit']);

		$updated = $table->update( $post_update, array('q_id' => $_GET['q_id']) );
		
		if($updated == 1)
		{
			?>
			<div id="message" class="updated"><p>Updated!</p></div>
			<?php
		}
	}

	if($_GET['action'] == 'new' && isset($_POST['q_forename']))
	{
		$post_insert = $_POST;
		$post_insert['q_created'] = date("Y-m-d H:i:s");
		$post_insert['q_updated'] = date("Y-m-d H:i:s");
		$post_insert['q_deleted'] = '0';
		unset($post_insert['submit']);

		//Q_helpers::debug($post_insert);

		$return = $table->insert( $post_insert );
		//Q_helpers::debug($return);
		if(isset($return))
		{
			$admin = $_SERVER['HTTP_ORIGIN'] . $_SERVER['REDIRECT_URL'];
			$location = $admin . '?page=p-crm-table.php&action=edit&q_id=' . $return .'&added=added';
			wp_redirect( $location );
			?>
			<script type="text/javascript">
				window.location = "<?php echo $location; ?>"
			</script>
			<?php
			exit();
		}
		//exit( var_dump( $wpdb->last_query ) );
	}

$retrieve_data = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}{$this->qtable}", ARRAY_A );

$update_create = ( $_GET['action'] == 'edit' ) ? 'Update' : 'Create' ;
$post_url 	   =  admin_url() . 'admin.php?' . $_SERVER['QUERY_STRING'] ;

$ignore = ['q_id' ,'q_created','q_updated','q_deleted'];
?>
<form action="<?php echo $post_url;  ?>" name="form_add_edit" id="form_add_edit" method="post">

     
<table class="form-table" >
    <tbody>
		<?php 
			if($_GET['action'] == 'new')
			{
				Q_helpers::loopCreate($retrieve_data,$ignore); 
			}

			if($_GET['action'] == 'edit')
			{
				$result = $table->get_by( array('q_id' => $_GET['q_id'], 'q_deleted' => '0'), '=', TRUE );
				Q_helpers::loopCreate($retrieve_data,$ignore,$result); 
			}
		?>
	</tbody>
</table>

<p class="submit"><button  id="submit" class="button button-primary" type="submit" name="submit"><?php echo $update_create; ?></button></p>
</form>
<script type="text/javascript">
	(function($){
		console.log('ready to add validation');



		// validate signup form on keyup and submit
		$("#form_add_edit").validate({
			rules: {
				q_forename: "required",
				q_surname: "required",
				q_forename: {
					required: true,
					minlength: 2
				},
				q_surname: {
					required: true,
					minlength: 2
				},
				q_number: {
					required: true,
					digits: true
				},
				q_email: {
					required: true,
					email: true
				}
			},
			messages: {
				q_forename: "Please enter your firstname",
				q_surname: "Please enter your lastname",
				q_email: "Please enter a valid email address"
			}
		});

	})(jQuery);



</script>
<?php
//$flattened = Q_helpers::flatten_array($retrieve_data,'Field');
//print_r($flattened);

