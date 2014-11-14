<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */

class Qlist_Table extends WP_List_Table
{

    /**
     * qtable
     * tablet name
     *
     * @access private
     * @var string
     */
	private $qtable = 'wp_quinncrm';

    /**
     * qcrud
     * qcrud object
     *
     * @access private
     * @var obj
     */
    private $qcrud;

    /**
     * quinnCrm
     * the quinnCrm.php class passed through
     *
     * @access private
     * @var obj
     */
    private $quinnCrm;

    /**
     * user
     * user data
     *
     * @access private
     * @var obj
     */
    private $user;


    /**
     * Constructor
     * Class constructor
     *
     * @access public
     * @param  [object]  qtable     An object to pull in
     */

    function __construct(Q_Crud $qtable, QuinnCrm $quinnCrm) { // dependency injection of Q_crud table being used
        
        if(isset($qtable)){
            
            $this->qcrud    = $qtable; // now have crud methods available
            $this->qtable   = $qtable->tableName;
            $this->quinnCrm = $quinnCrm;
            //echo 'test to show qcrud passed into this table';
            //Q_helpers::debug( $this->qcrud->get_by( array('q_id' => '1'), '=', TRUE ) );

        }

        // 1 is admin ////////////////
        $userdeets  = get_userdata(1); 
        //print_r($userdeets);
        $this->user = $userdeets;

        parent::__construct( array(
	      'singular'=> 'Qlist_Table',  //Singular label
	      'plural' => 'Qlist_Table',   //plural label, also this well be one of the table css class
	      'ajax'   => false            //We won't support Ajax for this table
	      ) );
       
    }

    /**
	 * Add extra markup in the toolbars before or after the list
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	function extra_tablenav( $which ) {
	   if ( $which == "top" ){
	      //The code that goes before the table is here
	     
          // place this code inside a php file and call it f.e. "download.php"
          //$path = $_SERVER['DOCUMENT_ROOT']."/path2file/ <br/>";
          //echo QCRM_PATH;
          //echo $path;
        
       
	   }
	   if ( $which == "bottom" ){
	      //The code that goes after the table is there
	      //echo "Hi, Im after the table";
         ?>
            <div class="alignleft top-buttons top-buttons--first" >
                <p style="margin:0;">Generate the CSV using dropdown above. <br/>To download latest:</p>
                <a style="margin:0 0 5px;" href="<?php echo QCRM_URL; ?>core/csv/download.php?download_file=users.csv" class="button button-primary table__email">Download here</a>
            </div>
            <div class="alignleft top-buttons">
                <p style="margin:0;">Add new user:</p>
                <a style="margin:0 0 5px;" href="<?php echo admin_url(). 'admin.php?page=p-crm-table.php&action=new'; ?>" class="button button-primary table__email">Add New User</a>
            </div>
          <?php
          
	   }
	}

	/**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
    	
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $this->process_bulk_action();
        
        //print_r($_POST['s']);
        $data = array();
        $return_data = (isset($_POST['s'])) ? $this->table_data($_POST['s']) : $this->table_data() ;

        if(is_array($return_data[0]))
        {
            $data = $return_data;
        }
        else
        {
            $data[0] = $return_data;
        }
        //usort( $data, array( &$this, 'sort_data' ) );
        //echo '<br/><br/> ist sorted: <br/>';
        //Q_helpers::debug($data);

        $perPage = 5;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);


        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
        //print_r($this->items);
    }

    /**
     * Get the table data
     *
     * @param string $search, if passed in, means we are searching
     * @return Array
     */
    public function table_data($search = NULL)
    {
    	global $wpdb;

        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'L';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

        if(is_null($search))
        {
            //$retrieve_data = $wpdb->get_results( "SELECT * FROM {$this->qtable} WHERE q_deleted = 0", ARRAY_A );
            $retrieve_data = $this->qcrud->get_by( array('q_deleted' => '0'), '=', TRUE );
        }else
        {
            $qfix = $this->quinnCrm->qfix;
            $sql_code = "WHERE {$qfix}id LIKE '%%%s%%'";
            foreach($this->quinnCrm->qfields_show as $k => $v)
            {
                $sql_code .= " OR " .$qfix.$k . " LIKE '%%%s%%' ";
            }
            //echo $sql_code;

            $retrieve_data = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$this->qtable} 
                 {$sql_code} 
                ;", $search, $search, $search, $search, $search, $search, $search, $search ),ARRAY_A);
        }

        
    	
    	//exit( var_dump( $wpdb->last_query ) );

    	return $retrieve_data;
    }



    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $qfix = $this->quinnCrm->qfix;
        
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            $qfix . 'id'    => 'ID'
        );
        // create the columns with the passed in //////////
        foreach($this->quinnCrm->qfields_show as $k => $v)
        {
            $columns[$qfix.$k] = $k;
        }

        $columns[$qfix.'created'] = 'Created';
        $columns[$qfix.'updated'] = 'Updated';

        return $columns;
    }
    /*
    public function get_columns()
    {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'q_id'          => 'ID',
            'q_forename'    => 'Forename',
            'q_surname'     => 'Surname',
            'q_email'       => 'Email',
            'q_number'      => 'Phone Number',
            'q_number_mob'  => 'Mobile Phone Number',
            'q_address'     => 'Address',
            'q_city'        => 'City',
            'q_county'      => 'County',
            'q_postcode'    => 'Postcode',
            'q_job'    	    => 'Job',
            'q_bio'         => 'Bio',
            'q_created' 	=> 'Created',
            'q_updated'     => 'Updated'
        );

        return $columns;
    }*/



    ////////////////////////////////
      /*  $qfix = $this->quinnCrm->qfix;
        $the_fields = array();
        Q_helpers::debug($this->quinnCrm);
        foreach($this->quinnCrm->qfields as $k => $v)
        {
            $the_fields[$qfix.$k] = $k;
        }
        Q_helpers::debug($the_fields);

        foreach($this->quinnCrm->qfields_show as $k => $v)
        {
            unset($the_fields[$qfix.$k]);
        }
        Q_helpers::debug($the_fields);
    */

    public function get_hidden_columns()
    {
        $qfix = $this->quinnCrm->qfix;
        $the_fields = array();
        //Q_helpers::debug($this->quinnCrm);
        foreach($this->quinnCrm->qfields as $k => $v)
        {
            //$the_fields[$qfix.$v] = $v;
        }

        foreach($this->quinnCrm->qfields_show as $k => $v)
        {
            //unset($the_fields[$qfix.$v])
        }

        return $thefields;
        //return array();
    }
    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    /*
    public function get_hidden_columns()
    {
        
        return array('q_bio', 'q_job', 'q_number', 'q_number_mob', 'q_address', 'q_city', 'q_county' ,'q_postcode');
        //return array();
    }*/
    ////////////////////////////////


    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {

        return array(
        	'q_id' 		 => 'ID',
        	'q_forename' => 'Forename',
        	'q_surname'  => 'Surname'
        );
    }


    /**
     * Define the specifc column
     *
     * @param string $item, the specific column
     * @return Array
     */
    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="q_id[]" value="%s" />', $item['q_id']
        );    
    }

    /**
     * Define the specifc column
     *
     * @param string $item, the specific column
     * @return Array
     */
    public function column_q_forename($item) {
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&q_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['q_id']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&q_id=%s" class="table__delete">Delete</a>',$_REQUEST['page'],'delete',$item['q_id']),
                'email'    => sprintf('<a href="'.QCRM_URL.'/partials/email-form.php?page=%s&action=%s&q_id=%s&helper=%s"  class="button button-primary table__email fancybox.ajax">Email</a>',$_REQUEST['page'],'email',$item['q_id'],base64_encode($this->qtable))
	            //'delete'    => sprintf('<a href="?page=%s&action=%s&q_id=%s" onclick="return areYouSure(this)">Delete</a>',$_REQUEST['page'],'delete',$item['q_id'])
	        );

	  return sprintf('%1$s %2$s', $item['q_forename'], $this->row_actions($actions) );
	}


    // Used to display the value of the id column
	public function column_q_created($item)
	{
	    return $item['q_created'];
	}

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'q_id':
			case 'q_forename':
			case 'q_surname':
			case 'q_email':
			case 'q_number':
			case 'q_number_mob':
			case 'q_address':
			case 'q_city':
			case 'q_county':
			case 'q_postcode':
            case 'q_updated':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }


    /**
     * What bulk actions are available to the table
     *
     * @return Array
     */
    public function get_bulk_actions() 
    {
      $actions = array(
        'delete'    => 'Delete',
        'export-all' => __( 'Export All to CSV' , 'visual-form-builder')//,
        //'export-selected' => __( 'Export Selected' , 'visual-form-builder')
      );
      return $actions;
    }


    /**
     * How to process those bulk actions defined in get_bulk_actions()
     *
     * @return Array
     */
    public function process_bulk_action() 
    {
        //print_r($this->current_action());
        
        //Detect when a bulk action is being triggered...
        if( 'delete' === $this->current_action() ) 
        {
            foreach($_GET['q_id'] as $q) 
            {
                delete_this_item($q);
            }
        }

        if( 'export-all' === $this->current_action() ) 
        {
            $this->export_csv('all');
        }

        if( 'export-selected' === $this->current_action() ) 
        {
            //Q_helpers::debug($_POST);
            $this->export_csv($_POST['q_id']);
        }

    }

    /**
     * Delete an item based on id
     *
     * @param string $id, the row to delete in column
     * @return Array
     */
    public function delete_this_item($id)
    {
        $updated = $table->update( array('q_deleted' => '1'), array('q_id' => $id) );
    }

    /**
     * Delete an item based on id
     *
     * @param string $id, the row to delete in column
     * @return Array
     */
    public function export_csv($data = 'all')
    {
        //echo 'I am called export_csv <br/>';
        if($data == 'all')
        {
            //echo 'all has been selected. <br/>';
            $csv_data = $this->qcrud->get_by( array('q_deleted' => '0'), '=', TRUE );
        }
        else
        {
            echo 'selected items for export. <br/>';
            Q_helpers::debug($data);
            $csv_data = $data;
        }
        


        global $wpdb;
        
        $headers = $wpdb->get_results( "SHOW COLUMNS FROM {$this->qtable}", ARRAY_A );
        $headers = Q_helpers::pluck($headers);
        

        $file = fopen(QCRM_PATH .  "csv/users.csv","w+");

        // add the headers /////////////
        fputcsv($file, $headers);
        
        // now loop through data //////
        foreach ($csv_data as $line)
          {
            fputcsv($file, $line);
          }

        fclose($file);
        ?>
        
        <div id="message" class="updated"><p>CSV Updated! please press Download button below</p></div>

        <?php

    }


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        //echo '<br/> a: ';
        //Q_helpers::debug($a);
        //echo '<br/> b: ';
        //Q_helpers::debug($b);
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'booktitle';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

}