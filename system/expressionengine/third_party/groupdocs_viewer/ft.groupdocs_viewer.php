<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Groupdocs_viewer_ft Class
 *
 * @package			ExpressionEngine
 * @category		Fieldtype
 * @author			GroupDocs Teem
 * @copyright		
 * @link			http://groupdocs.com/
 */
 
class Groupdocs_viewer_ft extends EE_Fieldtype {
	
	var $info = array(
		'name'		=> 'Embedded Groupdocs Viewer',
		'version'	=> '1.0.0'
	);
	
	var $prefix = 'groupdocs_viewer_';
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish
	 * This handles both displaying default values from Channel Fields > Edit Field OR Global Settings
	 * And reading saved data from channel entry. $data contains entry saved data with existing entry 
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		$this->EE->load->helper('form');
		
		$input = form_input(array(
			'name' => $this->field_name,
			'value' => $data,
			'type' => 'text',
			'size' => '64',
			'maxlength' => '64',
			'class' => 'embed_code'
		));
				
		return $input;
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field contents
	 * @return	replacement text
	 *
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		$iframe = "<iframe src='https://apps.groupdocs.com/document-viewer/embed/$data' frameborder='0' width='600' height='700'></iframe>";

		return $iframe;
	}

/* END class */
}

/* End of file ft.groupdocs_viewer.php */
/* Location: ./system/expressionengine/third_party/groupdocs_viewer/ft.groupdocs_viewer.php */
