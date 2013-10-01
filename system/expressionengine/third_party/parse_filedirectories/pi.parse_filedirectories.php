<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Parse File Directories',
	'pi_version' => '1.0',
	'pi_author' => 'Mark Bowen',
	'pi_author_url' => 'http://devot-ee.com/developers/mark-bowen-design',
	'pi_description' => 'Parse {filedir_1} type URLs',
	'pi_usage' => Parse_filedirectories::usage()
);

/**
 * Parsefiledirectories Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Mark Bowen
 * @copyright		Copyright (c) 2011, Mark Bowen
 * @link			http://devot-ee.com/developers/mark-bowen-design
 */

class Parse_filedirectories
{
    public $return_data = '';

    public function Parse_filedirectories()
    {
        $this->EE =& get_instance();
		$url = $this->EE->TMPL->tagdata;
		$file_dir = '';
		$file_name = '';

		// Figure out what the full URL should be
		if (preg_match('/{filedir_([0-9]+)}/', $url, $matches))
			{
				$file_dir = $matches[1];
				$file_name = str_replace($matches[0], '', $url);
			}

		$query = $this->EE->db->query("
										SELECT url
										FROM exp_upload_prefs
										WHERE id = '$file_dir'
										");

		// Output the full URL
		$this->return_data = $query->row('url').$file_name;

    }

// --------------------------------------------------------------------

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */

//  Make sure and use output buffering

	function usage()
	{
		ob_start();
	?>
	
	
	Simple example :
	
	{exp:query sql="
			SELECT field_id_19
			FROM exp_channel_data
			WHERE entry_id = '29'
			"}
	
	<img src="{exp:parse_filedirectories}{field_id_19}{/exp:parse_filedirectories}" />
	
	{/exp:query}
	
	This would output something akin to :
	
	<img src="http://www.example.com/images/uploads/my_file_name.jpg" />
	
	
	<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	// END


}

/* End of file pi.parse_filedirectories.php */ 
/* Location: ./system/expressionengine/third_party/parse_filedirectories/pi.parse_filedirectories.php */