<?php
$id = $this->EE->input->get('id');
$emails = $this->EE->input->get('emails');
$emails = preg_replace( '/\s+/', '', $emails);

$email_array = explode(',', $emails);

$userEmail = $this->EE->session->userdata('email');
$userScreenName = $this->EE->session->userdata('screen_name');

$this->EE->load->library('email');
$this->EE->load->helper('text');

$message = "Dear OT Practitioner,\n\nYou have been sent this email because one of your supervising Occupational Therapist has requested that you verify the evidence they have provided to the OTCA website. Please review the attached documentation provided by ".$userScreenName." and then click the link below to verify that the evidence is valid and fulfils the following criteria. -- criteria to be listed here --";

foreach($email_array as $sEmail)
{
    $this->EE->email->initialize();
    $this->EE->email->from($userEmail);
    $this->EE->email->to($sEmail);
    $this->EE->email->subject("OTCA - Validation required by you for evidence provided by: ".$userScreenName);
    $this->EE->email->message(entities_to_ascii($message));
    $this->EE->email->Send();
}

if($id) {
	$query = $this->EE->db->query("INSERT INTO otca_evidence (`entry_id`) VALUES ('".$id."')");
}

echo '{"message" : "<p>An email has been sent to these email addresses: "'.$emails.'"</p> <p>Your evidence will not appear on the site until the email recipients have verified these emails.</p>"}';
?>