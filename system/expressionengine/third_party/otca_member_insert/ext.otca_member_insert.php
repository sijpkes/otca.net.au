<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * OTCA Member Insert Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Paul Sijpkes
 * @link		
 */

class Otca_member_insert_ext {
	
	public $settings 		= array();
	public $description		= 'Additional fields to be added on member insert for the OTCA site';
	public $docs_url		= '';
	public $name			= 'OTCA Member Fields';
	public $settings_exist	= 'n';
	public $version			= '1.0';
	
	//private $EE;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		//ee() =& get_instance();
		$this->settings = $settings;
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'add_member_institution_id',
			'hook'		=> 'member_member_register',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		ee()->db->insert('extensions', $data);			
		
	}	

	// ----------------------------------------------------------------------
	
	/**
	 * 
	 *
	 * @param 
	 * @return 
	 */
	public function add_member_institution_id($data, $member_id)
	{
		$insert_data = array(
								'member_id' => $member_id,
								'institution_id' => $data['institution_id'],
								'authcode' => $data['authcode'],
								'group_id' => $data['group_type'] === 'student' ? 5 : 6 // 5 student, 6 educator
							);
							
		ee()->db->insert("otca_member_fields",  $insert_data);
	}

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------
}

/* End of file ext.otca_member_insert.php */
/* Location: /system/expressionengine/third_party/otca_member_insert/ext.otca_member_insert.php */