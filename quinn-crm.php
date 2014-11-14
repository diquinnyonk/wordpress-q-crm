<?php 
/*
Plugin Name: Quinn CRM
Plugin URI: http://www.paulmarkquinn.co.uk
Description: A plugin to manage crm. 
Version: 1.0.0
Author: Paul Mark Quinn
Author URI: http://www.paulmarkquinn.co.uk
License: A "Slug" license name e.g. GPL2
*/
//namespace quinn;

//////////////////////////////////////////
// include Q.php /////////////////////////

require(dirname(__FILE__) . '/core/Q.php');
require(dirname(__FILE__) . '/core/Q_includes.php');
require(dirname(__FILE__) . '/crm/QuinnCrm.php');


////////////////////////////////////
// lets go /////////////////////////
////////////////////////////////////
if(is_admin()):


    $fields_to_pass = array(
        'forename'   => 'varchar',
        'surname'    => 'varchar',
        'email'      => 'varchar',
        'number'     => 'varchar',
        'number_mob' => 'varchar',
        'address'    => 'varchar',
        'city'       => 'varchar',
        'county'     => 'varchar',
        'postcode'   => 'varchar',
        'job'        => 'text',
        'bio'        => 'text'
    );

    $fields_to_show = array(
        'forename'   => 'varchar',
        'surname'    => 'varchar',
        'email'      => 'varchar',
    );
    
    $quinn_table = 'quinncrm';

	$QuinnCrm    = QuinnCrm::get_instance($qunn_table,'q',$fields_to_pass, $fields_to_show);
    register_deactivation_hook( __FILE__ , array( $QuinnCrm, 'q_delete_tables' ) );
    register_activation_hook( __FILE__ , array( $QuinnCrm, 'q_create_plugin_tables' ) );

endif;





