<?php
class Dashboard extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "app/dashboard";
	}
	
	public function index(){
		$this->data['headData']->menu_id = 0;

		$this->load->view('app/dashboard',$this->data);
	}
	
	

	public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        if(empty($data['cpassword']))
            $errorMessage['cpassword'] = "Confirm Password is required.";
        if(!empty($data['new_password']) && !empty($data['cpassword'])):
            if($data['new_password'] != $data['cpassword'])
                $errorMessage['cpassword'] = "Confirm Password and New Password is Not match!.";
        endif;

        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $id = $this->session->userdata('loginId');
			$result =  $this->dashboard->changePassword($id,$data);
			$this->printJson($result);
		endif;
    }
}
?>