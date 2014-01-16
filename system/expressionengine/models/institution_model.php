<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
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
 * ExpressionEngine OTCA Institution Model
 *
 * @package		ExpressionEngine
 * @subpackage	Core
 * @category	Model
 * @author		Paul Sijpkes
 * @link		http://ellislab.com
 */
class Institution_model extends CI_Model {
	
	private $institution_id = 0;
	private $institution_name = "";
	private $expiry_date = 0;
	private $educator_reg_uri;
	private $student_reg_uri;
	
	function __construct() {
		$this->_get_institution_details();
	}
	
	/*
	 * 	OTCA Institution functions
	 */
	function get_institution_name() {
	 	return $this->institution_name;
	}
	
	function get_institution_id() {
		return $this->institution_id;
	}
	
	function get_expiry_date() {
		return $this->expiry_date;
	}
	
	function get_student_url() {
		
		return "https://otca.net.au/member/register?T=student".AMP."C=".$this->student_reg_uri;
	}
	
	function get_educator_url() {
		return "https://otca.net.au/member/register?T=educator".AMP."C=".$this->educator_reg_uri;
	}
	// --------------------------------------------------------------------

	function _prep_otca_join_query($ref) {
		if (
		ee() -> session -> userdata('group_id') == 8 ||
		ee() -> session -> userdata('group_id') == 9) {
			$ref -> db -> join("otca_member_fields", "otca_member_fields.member_id = members.member_id");
			$ref -> db -> where("otca_member_fields.institution_id", $this->institution_id);
		}
	}

	private function _get_institution_details() {
		if (
		ee() -> session -> userdata('group_id') == 8 ||
		ee() -> session -> userdata('group_id') == 9) {
			$admin_member_id =   ee() -> session -> userdata('member_id');

			$res =  ee() -> db -> select("institution_id") -> where("member_id", $admin_member_id) -> get("otca_member_fields");

			foreach ($res->result_array() as $row) {
				$this->institution_id = $row['institution_id'];
			}
			
			$res =  ee() -> db -> query("SELECT name, student_uri_hash, educator_uri_hash, expiry_date FROM otca_institutions WHERE id = '$this->institution_id'");
			
			foreach ($res->result_array() as $row) {
				$this->institution_name = $row['name'];
				$this->student_reg_uri = $row['student_uri_hash'];
				$this->educator_reg_uri = $row['educator_uri_hash'];
				$this->expiry_date = $row['expiry_date'];
			}
		}
	}

}

/* End of file member_model.php */
/* Location: ./system/expressionengine/models/member_model.php */
