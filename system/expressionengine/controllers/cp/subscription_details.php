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
	private $datestring = "%D %d%S %M %Y";
	
	private $manager_per_page = 10;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->subscription_base_uri = 'C=subscription_details';
		$this->subscription_base_url = BASE.AMP.$this->subscription_base_uri;
		
		$this->load->helper('date');
		$this->load->library('api');
		$this->load->model('institution_model');
	}

	// --------------------------------------------------------------------
	public function index() {
		
		if($this->cp->allowed_group('can_manage_subscriptions')) {
			$this->manage();
		} else if($this->cp->allowed_group('can_view_institutions_subscription')) {
			$this->show();
		} else {
			show_error(lang('unauthorized_access'));
		}
	}
	/**
	 * Show subscription details for institution administrator
	 *
	 * @return	void
	 */
	public function show()
	{
		if ( ! $this->cp->allowed_group('can_view_institutions_subscription'))
		{
			show_error(lang('unauthorized_access'));
		}
		
		$vars['institution_name'] = $this->institution_model->get_institution_name();
		
		$date_r = $this->institution_model->get_expiry_date();
		
		$vars['expired'] = time() > $date_r;
		
		$date_f = ee()->localize->format_date($this->datestring, $date_r);
		
		$vars['expiry_date'] = $date_f;
		$vars['student_url'] = $this->institution_model->get_student_url();
		$vars['educator_url'] = $this->institution_model->get_educator_url();
		$vars['lecturer_url'] = $this->institution_model->get_lecturer_url();
		$this->view->cp_page_title = $this->institution_model->get_institution_name() . " " .lang('subscription_info');
		
		$this->cp->render('content/subscriptions', $vars);
	}
	
	public function manage()
	{
		if ( ! $this->cp->allowed_group('can_manage_subscriptions'))
		{
			show_error(lang('unauthorized_access'));
		}
		
		$this->load->library('table');
		
		$columns = array(
			'id' => array(),
			'name' => array(),
			'expiry_date' => array(),
			'student_uri_hash' => array(),
			'educator_uri_hash' => array(),
			'lecturer_uri_hash' => array(),
			'_check'		=> array(
				'header' => form_checkbox('select_all', 'true', FALSE, 'class="toggle_all"'),
				'sort' => FALSE
			)
		);
		
		$this->table->set_base_url('C=subscription_details'.AMP.'M=manage');
		$this->table->set_columns($columns);
		
		$state = array('sort' => array('expiry_date' => 'asc'));
		
		$vars = $this->table->datasource('_load_subscriptions', $state);
		
		$vars['institution_action_options'] = array('renew' => lang('renew_subscription'), 'cancel' => lang('cancel_subscription'), 'delete' => lang('delete_selected'));
		$vars['delete_button_label'] = lang('submit');
		$vars['add_button_label'] = lang('add');
		$this->view->cp_page_title = lang('manage_subscriptions');
		$this->cp->render('content/subscription_management', $vars);
	}
	
	public function institution_confirm()
	{
		if ( ! $this->cp->allowed_group('can_manage_subscriptions'))
		{
			show_error(lang('unauthorized_access'));
		}
		
		if ($this->input->post('action') == 'renew')
		{
			$this->renew_subscriptions_confirm();
		}
		else if ($this->input->post('action') == 'cancel') {
			$this->cancel_subscriptions_confirm();
		} 
		else if ($this->input->post('action') == 'add') {
			$this->add_subscription_confirm();
		} 
		else
		{
			$this->institution_delete_confirm();
		}
	}
	
	public function institutions_add() {
		if ( ! $this->cp->allowed_group('can_manage_subscriptions') )
		{
			show_error(lang('unauthorized_access'));
		}

		if ( ! $this->input->post('add_name') )
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		$this->load->model('institution_model');
		
		$this->institution_model->add_institution($this->input->post('add_name'));
		
		$cp_message = lang('institution_added');

		$this->session->set_flashdata('message_success', $cp_message);
		$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
	}
	
	public function institutions_delete() {
		if ( ! $this->cp->allowed_group('can_manage_subscriptions') )
		{
			show_error(lang('unauthorized_access'));
		}

		if ( ! $this->input->post('delete') OR ! is_array($this->input->post('delete')))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		$this->load->model('institution_model');
		
		$institution_ids = array();
		
		foreach ($this->input->post('delete') as $key => $val)
		{		
			if ($val != '')
			{
				$institution_ids[] = $this->db->escape_str($val);
			}		
		}
		
		$this->institution_model->delete_institutions($institution_ids);
		
		$cp_message = (count($institution_ids) == 1) ? lang('institution_deleted') :
										lang('institutions_deleted');

		$this->session->set_flashdata('message_success', $cp_message);
		$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
	}
	
	public function institutions_renew() {
		if ( ! $this->cp->allowed_group('can_manage_subscriptions') )
		{
			show_error(lang('unauthorized_access'));
		}

		if ( ! $this->input->post('renew') OR ! is_array($this->input->post('renew')))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		$this->load->model('institution_model');
		
		$data = array();
		
		foreach ($this->input->post('renew') as $key => $val)
		{		
			if ($val != '')
			{
				$data[] = array('id' => $this->db->escape_str($val),
								'expiry_date' => strtotime('+1 year', time()) ,
								'educator_hash' => ee()->functions->random('alpha', 20),
								'student_hash' => ee()->functions->random('alpha', 20),
								'lecturer_hash' => ee()->functions->random('alpha', 20)
								);
			}		
		}
		
		$this->institution_model->renew_institutions($data);
		
		$cp_message = (count($data) == 1) ? lang('institution_renewed') :
										lang('institutions_renewed');

		$this->session->set_flashdata('message_success', $cp_message);
		$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
	}
	
	public function institutions_cancel() {
		if ( ! $this->cp->allowed_group('can_manage_subscriptions') )
		{
			show_error(lang('unauthorized_access'));
		}

		if ( ! $this->input->post('cancel') OR ! is_array($this->input->post('cancel')))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		$this->load->model('institution_model');
		
		$institution_ids = array();
		
		foreach ($this->input->post('cancel') as $key => $val)
		{		
			if ($val != '')
			{
				$institution_ids[] = $this->db->escape_str($val);
			}		
		}
		
		$this->institution_model->cancel_institutions($institution_ids);
		
		$cp_message = (count($institution_ids) == 1) ? lang('institution_cancelled') :
										lang('institutions_cancelled');

		$this->session->set_flashdata('message_success', $cp_message);
		$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
	}
	
	private function add_subscription_confirm() {
		$this->view->cp_page_title = lang('add_institution');
		//$vars = array();
		
		$this->cp->render('institutions/add_new');
	}
	
	private function renew_subscriptions_confirm() {
		if ( ! isset($_POST['toggle']))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		if ( ! is_array($_POST['toggle']) OR count($_POST['toggle']) == 0)
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}
		
		$renew = array();  
		
		foreach ($this->input->post('toggle') as $key => $val)
		{
			// Don't allow owner to be renewed
			if (1 == $val)
			{
				show_error(lang('can_not_renew_owner'));
			}

			$renew[] = $val;
		}
		
		$vars['renew'] = $renew;
		
		$this->view->cp_page_title = lang('renew_institutions');
		
		$this->cp->render('institutions/renew_confirm', $vars);
	}
	
	private function cancel_subscriptions_confirm() {
		if ( ! isset($_POST['toggle']))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		if ( ! is_array($_POST['toggle']) OR count($_POST['toggle']) == 0)
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}
		
		$cancel = array();  
		
		foreach ($this->input->post('toggle') as $key => $val)
		{
			// Don't allow owner to be renewed
			if (1 == $val)
			{
				show_error(lang('can_not_cancel_owner'));
			}

			$cancel[] = $val;
		}
		
		$vars['cancel'] = $cancel;
		
		$this->view->cp_page_title = lang('cancel_institutions');
		
		$this->cp->render('institutions/cancel_confirm', $vars);
	}
	
	private function institution_delete_confirm() {
		
		if ( ! isset($_POST['toggle']))
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}

		if ( ! is_array($_POST['toggle']) OR count($_POST['toggle']) == 0)
		{
			$this->functions->redirect(BASE.AMP.'C=subscription_details'.AMP.'M=manage');
		}
		
		$damned = array();  // 'damned' is from Member module, sort of makes sense
		
		foreach ($this->input->post('toggle') as $key => $val)
		{
			// Don't allow UoN to be deleted
			if (1 == $val)
			{
				show_error(lang('can_not_delete_owner'));
			}

			$damned[] = $val;
		}
		
		$vars['damned'] = $damned;
		
		$this->view->cp_page_title = lang('delete_institutions');
		
		$this->cp->render('institutions/delete_confirm', $vars);
	}
	
	function _load_subscriptions($initial_state) {
		
		$order_by = "";
		$comma = '';
		$i = 0;
		
		foreach($initial_state['sort'] as $ob => $direction) {
			if($i > 0) $comma = ',';
			$order_by .= "$comma $ob $direction";
			$i = $i + 1;
		}
		
		$sql = "SELECT id, name, expiry_date, student_uri_hash, educator_uri_hash, lecturer_uri_hash FROM otca_institutions ORDER BY $order_by LIMIT $initial_state[offset], $this->manager_per_page";
		
		$result = ee()->db->query($sql);
		$offset = $initial_state['offset'];
		$institutions = $result->result_array();
		$rows = array();
		
		while ($institution = array_shift($institutions))
		{
			$rows[] = array(
				'id' => $institution['id'],
				'name' => $institution['name'],
				'expiry_date' => mdate($this->datestring, $institution['expiry_date']),
				'student_uri_hash' => $institution['student_uri_hash'],
				'educator_uri_hash' => $institution['educator_uri_hash'],
				'lecturer_uri_hash' => $institution['lecturer_uri_hash'],
				'_check'	=> '<input class="toggle" type="checkbox" name="toggle[]" value="'.$institution['id'].'" />'
			);
		}
			
		return array(
    		'rows' => $rows,
    		'pagination' => array(
        		'per_page'   => $this->manager_per_page,
        		'total_rows' => $this->institution_model->count_institutions()
			)
		);
	}
	
}
// END CLASS

/* End of file subscription_details.php */
/* Location: ./system/expressionengine/controllers/cp/subscription_details.php */
