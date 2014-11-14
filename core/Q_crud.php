<?php

//echo 'included in the ajaz <br/>';
/**
 * Base class for CRUD operations
 *
 * @package Quinn
 * @subpackage Q_Crud
 * @since 1.0.0
 * @access private
 */

/*
class Q_Crud {

}*/

/**
 * Abstract class which has helper functions to get data from the database
 */
abstract class Base_Custom_Data
{
    /**
     * The current table name
     *
     * @var boolean
     */
    public $tableName = false;




    /**
     * Constructor for the database class to inject the table name
     *
     * @param String $tableName - The current table name
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function retrieve($retreive)
    {
        //echo $retreive;
        return $this->$retreive;
    }

    /**
     * Insert data into the current data
     *
     * @param  array  $data - Data to enter into the database table
     *
     * @return InsertQuery Object
     */
    public function insert(array $data)
    {
        global $wpdb;

        if(empty($data))
        {
            return false;
        }

        $wpdb->insert($this->tableName, $data);

        return $wpdb->insert_id;
    }

    /**
     * Get all from the selected table
     *
     * @param  String $orderBy - Order by column name
     *
     * @return Table result
     */
    public function get_all( $orderBy = NULL )
    {
        global $wpdb;

        $sql = 'SELECT * FROM `'.$this->tableName.'`';

        if(!empty($orderBy))
        {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        $all = $wpdb->get_results($sql);

        return $all;
    }

    /**
     * Get a value by a condition
     *
     * @param  Array $conditionValue - A key value pair of the conditions you want to search on
     * @param  String $condition - A string value for the condition of the query default to equals
     *
     * @return Table result
     */
    public function get_by(array $conditionValue, $condition = '=', $returnSingleRow = FALSE)
    {
        global $wpdb;

        try
        {
            $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE ';

            $conditionCounter = 1;
            foreach ($conditionValue as $field => $value)
            {
                if($conditionCounter > 1)
                {
                    $sql .= ' AND ';
                }

                switch(strtolower($condition))
                {
                    case 'in':
                        if(!is_array($value))
                        {
                            throw new Exception("Values for IN query must be an array.", 1);
                        }

                        $sql .= $wpdb->prepare('`%s` IN (%s)', $field, implode(',', $value));
                        break;

                    default:
                        $sql .= $wpdb->prepare('`'.$field.'` '.$condition.' %s', $value);
                        break;
                }

                $conditionCounter++;
            }

            $result = $wpdb->get_results($sql,ARRAY_A);

            // As this will always return an array of results if you only want to return one record make $returnSingleRow TRUE
            if(count($result) == 1 && $returnSingleRow)
            {
                $result = $result[0];
            }

            return $result;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }

    /**
     * Update a table record in the database
     *
     * @param  array  $data           - Array of data to be updated
     * @param  array  $conditionValue - Key value pair for the where clause of the query
     *
     * @return Updated object
     */
    public function update(array $data, array $conditionValue)
    {
        global $wpdb;
        
        if(empty($data))
        {
            return false;
        }

        $updated = $wpdb->update( $this->tableName, $data, $conditionValue);
        
        if( $updated === false) {
            // ERROR!
            echo '<p>DB insert error - '.$wpdb->print_error().'</p>';
            Q_helpers::debug($wpdb);
        }
        //exit();
        return $updated;
    }

    /**
     * Delete row on the database table
     *
     * @param  array  $conditionValue - Key value pair for the where clause of the query
     *
     * @return Int - Num rows deleted
     */
    public function delete(array $conditionValue)
    {
        global $wpdb;

        $deleted = $wpdb->delete( $this->tableName, $conditionValue );

        return $deleted;
    }
}
class Q_Crud extends Base_Custom_Data
{
    public function __construct($tableName)
    {
         parent::__construct($tableName);
    }
}
