
<?php
include("../../../../wp-load.php");
include(QCRM_URL . "core/Q_crud.php");

//echo 'before the madness <br/>';
//echo 'QCRM_URL: ' . QCRM_URL . '<br/>';
//echo $quinn_table;

$quinn_table = base64_decode($_GET['helper']);
$table  = new Q_Crud( $quinn_table );
//Q_helpers::debug($table);
$user_from 	= get_userdata(1);
$user 		= $table->get_by( array('q_id' => $_GET['q_id'], 'q_deleted' => '0'), '=', TRUE );
$user_email = $user['q_email'];

//Q_helpers::debug($user);
//print_r($_GET);
?>
<div class="the_region">
	<form action="#" class="the_form">
		<h3>Your Email to <?php echo $user['q_forename'] . ' ' . $user['q_surname'];  ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<label for="subject"><span class="description">Subject</span></label>
					</th>
					<td>
						<input type="text" name="subject" id="subject" placeholder="Subject" value="" required/>
					</td>
				</tr>

				<tr>
					<th><label for="textarea">Your Message</label></th>
					<td><textarea name="textarea" id="textarea" cols="30" rows="10"></textarea></td>
				</tr>

			</tbody>
		</table>
		
		<p class="submit">
			<button class="button button-primary form__submit form__submit--email">Submit</button>
		</p>
	</form>
	<div class="the_feedback"></div>

	<script>
		(function($){
	    //console.log('ready twp', document.URL, window.location);


	    $('.form__submit--email').on('click',function(e){
	        
	        subject  = $('#subject').val();
	        subject  = (subject != '') ? subject : 'Email from Company';

	        textarea 	 = $('#textarea').val();
	        email_to 	 = '<?php echo $user_email; ?>' ;
	        email_from 	 = '<?php echo $user_from->data->user_email; ?>' ;
	        
	        url_use  	 = '<?php echo QCRM_URL; ?>partials/mailer-script.php';

	        //console.log('submit 22222', subject, textarea, email_from, email_to, url_use);
	        $feedback = $('.the_feedback');

	        $.ajax({
	        	type: "POST",
	        	url:  url_use,
	        	data: {
	        		subject 	: subject,
	        		textarea 	: textarea,
	        		email_to 	: email_to,
	        		email_from 	: email_from
	        	},
	        	success : function(data){
	        		$data = JSON.parse(data);
	        		//console.log($data, $data.success, $data['success']);
	        		if($data.success === 'yes'){
	        			$('.the_form').remove();
	        			$feedback.html('<div style="text-align:center;"><h1>Thank you!</h1><p>The email has been sent!</p></div>');
	        		}else{
	        			$feedback.html('<p>Sorry, there has been an error, perhaps the email is not correct?</p>');
	        		}

	        	}
	        });

	        e.preventDefault();
	    });

	})(jQuery);
	</script>
</div>
