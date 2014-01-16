<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2013, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine CP Home Page Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Subscription_details extends CP_Controller {

	private $subscription_base_uri;
	private $subscription_base_url;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		//$this->installed_modules = $this->cp->get_installed_modules();
		//$this->allowed_channels = $this->functions->fetch_assigned_channels();

		$this->subscription_base_uri = 'C=subscription_details';
		$this->subscription_base_url = BASE.AMP.$this->subscription_base_uri;

		$this->load->library('api');
		$this->load->model('institution_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Index function
	 *
	 * @return	void
	 */
	public function index()
	{
	
		$vars['institution_name'] = $this->institution_model->get_institution_name();
		
		$this->load->helper('date');
		$datestring = "%D %d%S %M %Y";
		$date_r = $this->institution_model->get_expiry_date();
		
		$vars['expired'] = (time() > $date_r) ? TRUE : FALSE;
		
		$date_f = mdate($datestring, $date_r);
		
		$vars['expiry_date'] = $date_f;
		$vars['student_url'] = $this->institution_model->get_student_url();
		$vars['educator_url'] = $this->institution_model->get_educator_url();
	
		$this->cp->render('content/subscriptions', $vars);
	}
	
}
// END CLASS

/* End of file subscriptions.php */
/* Location: ./system/expressionengine/controllers/cp/subscriptions.php */
