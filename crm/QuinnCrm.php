<?php
class QuinnCrm {

    /**
     * qtable
     * table name
     *
     * @access private
     * @var string
     */
	private $qtable = 'quinncrm';


    /**
     * qfix
     * prefix for all column names
     *
     * @access private
     * @var string
     */
    public $qfix = 'q_';

    /**
     * qfields
     * Array of fields to create table
     *
     * @access public
     * @var array
     */
    public $qfields = array(); 

    /**
     * qfields_show
     * Array of fields to show, in table list
     *
     * @access protected
     * @var array
     */
    public $qfields_show = array();    

    /**
     * Instance
     * Array of instances, for each instantion of the class
     *
     * @access protected
     * @var array
     */
	public static $instance = array(); 


    
	public function init($qtable = null, $qfix = null, $qfields = null, $qfields_show = null) 
	{
        if(!is_null($qtable))
        {
            $this->qtable = $qtable;
        }

        if(!is_null($qfix))
        {
            $this->qfix = $qfix . '_';
        }

        if(!is_null($qfields))
        {
            $this->qfields = $qfields;
        }

        if(!is_null($qfields_show))
        {
            $this->qfields_show = $qfields_show;
        }
        
        // these hooks dont work unless in plugin file
        //register_deactivation_hook( __FILE__ , array( &$this, 'q_delete_tables' ) );
        //register_activation_hook( __FILE__ , array( &$this, 'q_create_plugin_tables' ) );
        //echo 'I run to here?';
        add_action( 'admin_menu', array($this, 'add_menu_crm_table_page' ));
        add_action( 'plugins_loaded', array($this, 'add_scripts' ));
    }
	

    /**
     * Create singleton instance
     *
     * @access public
     * @param  [string] qtable    the table name to create
     * @param  [string] qfix      the prefix for fields
     * @param  [array]  qfields   the extra fields
     *
     * @return  instance of singleton class
     */
	public static function get_instance($qtable = null, $qfix = null, $qfields = null, $qfields_show = null) 
	{
        $c = get_called_class();
        //echo 'called: ' . $c ;
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->init($qtable, $qfix, $qfields, $qfields_show);
        }
        //print_r(self::$instance[$c]);
        return self::$instance[$c];
    }


    public function q_delete_tables()
    {
		global $wpdb;
		
		//$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$this->qtable}" );
	}

    public function create_table_sql(){

        global $wpdb;

        $table_name = $wpdb->prefix . $this->qtable;

        $sql = "
        CREATE TABLE $table_name (
              {$this->qfix}id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        ";

        if( !empty($this->qfields) )
        {
              foreach($this->qfields as $k => $v)
              {
                switch($v){
                    
                    case 'varchar':
                        $sql .= $this->qfix . $k . " VARCHAR(75) NOT NULL, ";
                    break;
                    case 'text':
                        $sql .= $this->qfix . $k . " TEXT NULL, ";
                    break;                        
                    case 'int':
                        $sql .= $this->qfix . $k . " unsigned NOT NULL, ";
                    break;
                    case 'bigint':
                        $sql .= $this->qfix . $k . " unsigned NOT NULL, ";
                    break;
                    case 'datetime':
                        $sql .= $this->qfix . $k . " datetime NOT NULL DEFAULT '0000-00-00 00:00:00',";
                    break;
                    default:
                        $sql .= '';    
                        Q_helpers::debug($v);
                }
              }

        }


        $sql .= "{$this->qfix}created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              {$this->qfix}updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              {$this->qfix}deleted CHAR(7) NOT NULL DEFAULT '0',
              PRIMARY KEY ({$this->qfix}id)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        return $sql;
    }


	public function q_create_plugin_tables()
	{
	  //echo '<br/>q_create_plugin_tables called?<br/>';   
        $sql = $this->create_table_sql();

        //echo $sql; exit();

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );

	}

	
	/**
     * Menu item will allow us to load the page to display the table
     */
    public function add_menu_crm_table_page()
    {
        add_menu_page( 'CRM', 'CRM', 'manage_options', 'p-crm-table.php', array($this, 'crm_table_page'), 'dashicons-admin-users' );
    	add_submenu_page( "p-crm-table.php", "View Users", "View Users", "manage_options", "p-crm-table.php", array($this, 'crm_table_page'));
    	add_submenu_page( "p-crm-table.php", "Add A new User", "Add A new User", "manage_options", "p-crm-table.php&action=new", array($this, 'crm_table_page'));
    }


    /**
     * add in styles and scripts
     *
     * @return Void
     */
    public function add_scripts()
    {
        wp_register_style( 'crm-style', QCRM_URL . '/assets/css/style.css' );

    	wp_register_style( 'crm-fancy-style', QCRM_URL . '/js/fancy/jquery.fancybox.css' );
    	wp_register_script( 'crm-fancy', QCRM_URL . '/js/fancy/jquery.fancybox.js', array('jquery'),'' );
        //wp_register_script( 'crm-validate', QCRM_URL . 'js/validate.min.js', array('jquery'),'' );
        wp_register_script( 'crm-validate', QCRM_URL . 'js/jquery.validate.min.js', array('jquery'),'' );
    	wp_register_script( 'quinncrm-js', QCRM_URL . '/js/quinn-crm.js', array('jquery'),'' );

    	wp_enqueue_style(  'crm-style');
        wp_enqueue_style(  'crm-fancy-style');
    	wp_enqueue_script( 'crm-fancy');
        wp_enqueue_script( 'crm-validate');
    	wp_enqueue_script( 'quinncrm-js');

    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function crm_table_page()
    {
    	//echo '<pre>';
    	//print_r($_GET);
    	//echo '</pre>';


        global $wpdb;

        $table = new Q_Crud( "{$wpdb->prefix}{$this->qtable}" );

    	if($_GET['action'] == 'new' || $_GET['action'] == 'edit') :

    		include( dirname(__FILE__)  . '/quinn-crm-form.php');

    	else :	

            if($_GET['action'] == 'delete')
            {

                //$deleted = $table->delete( array('q_id' => $_GET['q_id']) );
                $updated = $table->update( array('q_deleted' => '1'), array('q_id' => $_GET['q_id']) );
                //exit( var_dump( $wpdb->last_query ) );
                if($updated == 1)
                {
                    ?>
                    <div id="message" class="updated"><p>Deleted!</p></div>
                    <?php
                }
               
            }
            
            $quinncrm = new Qlist_Table($table, $this);
            $quinncrm->prepare_items($table);

            ?>
            <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2>Example List Table Page</h2>
                <form action="" method="post">
                    <?php $quinncrm->search_box('search', 'search_id'); ?>
                    <?php $quinncrm->display(); ?>
                </form>
            </div> 
            <?php 
        endif;       

    }
	
}