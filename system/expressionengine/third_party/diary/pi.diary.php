<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'EEPortfolio Diary',
	'pi_version' => '1.0.0',
	'pi_author' => 'Paul Sijpkes',
	'pi_author_url' => '',
	'pi_description' => 'Diary entries plugin',
	'pi_usage' => Diary::usage()
);

class Diary {
    
public function __construct() {
    
}
 
public static function usage()
    {
        ob_start();  ?>

The Diary Plugin provides personal diary features.

    <?php
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }    
}