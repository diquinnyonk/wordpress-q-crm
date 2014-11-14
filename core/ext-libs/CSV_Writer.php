<?php
/**
 * Simple class to properly output CSV data to clients. PHP 5 has a built
 * in method to do the same for writing to files (fputcsv()), but many times
 * going right to the client is beneficial.
 *
 * @author Jon Gales
 */

class CSV_Writer {

    public $data = array();
    public $deliminator;

    /**
     * Loads data and optionally a deliminator. Data is assumed to be an array
     * of associative arrays.
     *
     * @param array $data
     * @param string $deliminator
     */
    function __construct($data, $deliminator = ",")
    {
        if (!is_array($data))
        {
            throw new Exception('CSV_Writer only accepts data as arrays');
        }

        $this->data = $data;
        $this->deliminator = $deliminator;
    }

    private function wrap_with_quotes($data)
    {
        $data = preg_replace('/"(.+)"/', '""$1""', $data);
        return sprintf('"%s"', $data);
    }

    /**
     * Echos the escaped CSV file with chosen delimeter
     *
     * @return void
     */
    public function output()
    {

    	$data_val = '';

    	$filename = $sitename . 'users.' . date( 'Y-m-d-H-i-s' ) . '.csv';

    	header( 'Content-Description: File Transfer' );
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
		header("Pragma: no-cache");
		header("Expires: 0");

	

        foreach ($this->data as $row)
        {
            $quoted_data = array_map(array('CSV_Writer', 'wrap_with_quotes'), $row);
            $data_val .= sprintf("%s\n", implode($this->deliminator, $quoted_data));
            print sprintf("%s\n", implode($this->deliminator, $quoted_data));
        }
        //exit;
        return $data_val;
    }

    /**
     * Sets proper Content-Type header and attachment for the CSV outpu
     *
     * @param string $name
     * @return void
     */
    public function headers($name)
    {
        //header('Content-Type: application/csv');
        //header("Content-disposition: attachment; filename={$name}.csv");

        header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename="' . $name . '.csv"');
 	
		//print_r($this->data);
		//die();
    }
}