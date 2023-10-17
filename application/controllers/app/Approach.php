<?php
class Approach extends MY_Controller
{
	private $indexPage = "app/approach";
	private $leadForm = "app/approach_form"; 
	private $followupFrom = "app/followup_form";
	private $leadStatusForm = "app/lead_status_form";
	private $appointmentForm = 'app/appointment_form';
	private $appointmentStatusForm = "app/appointment_status";


	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Approach";
		$this->data['headData']->controller = "app/approach";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'lead']);
	}

	
	public function index($lead_status =0)
	{
        $this->data['lead_status'] = $lead_status;
		$this->data['bottomMenuName'] ='approach';
        $this->data['leadData'] =  $this->leads->getLeadListForApp(['lead_status'=>$lead_status]);//$this->getLeadList($lead_status);;
		$this->load->view($this->indexPage, $this->data);
	}


	public function getLeadList($lead_status = 0)	{
		$result = $this->leads->getLeadListForApp(['lead_status'=>$lead_status]);
		$html = '';
		foreach ($result as $row):
			$row->appointments = '';
			$row->followupDate = '';
			$row->followupNote = '';
			$followupData = $this->leads->getFollowupData(['entry_type' => 1, 'lead_id' => $row->id]);
			if(!empty($followupData)):
				$row->followupDate = formatDate($followupData->appointment_date);
				$row->next_fup_date = formatDate($followupData->next_fup_date);
				$row->followupNote =$followupData->notes;
			endif;

			if ($row->lead_status == 0 || $row->lead_status == 1):
				$appointsData = $this->leads->getAppointments(['entry_type' => 2, 'lead_id' => $row->id, 'status' => 0]);
				if (!empty($appointsData)):
					$apArray = [];
					foreach ($appointsData as $ap):
						$style = "";
						if (date('Y-m-d H:i:s') >= date("Y-m-d H:i:s", strtotime($ap->appointment_date . ' ' . $ap->appointment_time . ' -24 Hours'))):
							$style = 'text-danger';
						endif;

						$appoParam = "{'postData' : {'id' : ".$ap->id."}, 'modal_id' : 'modal-md', 'form_id' : 'appointmentStatus', 'title' : 'Appointment Status','fnsave':'saveAppointmentStatus','fnedit':'appointmentStatus'}";

						$apArray[] = '<a href="javascript:void(0)" class="' . $style . '" onclick="edit('.$appoParam.');">' . formatDate($ap->appointment_date, 'd-m-Y ') . formatDate($ap->appointment_time, 'H:i A') . '</a>';
					endforeach;

					$row->appointments = implode("<hr style='margin-top:0px;margin-bottom:0px'>", $apArray);
				endif;
			endif;
			$followupBtn = '';$appointmentBtn ='';$enqBtn='';$editButton="";$deleteButton="";$leadStatusButton = "";
       
			if(in_array($row->lead_status,[0,4])):
				$followupParam = "{'postData': {'id' : ".$row->id.",'party_id':".$row->party_id.",'sales_executive':".$row->sales_executive.",'entry_type':1}, 'modal_id' : 'modal-lg', 'form_id' : 'followUp', 'title' : 'Follow up', 'fnedit' : 'addFollowup', 'fnsave' : 'saveFollowup','res_function' : 'resFollowup', 'button' : 'close'}";
				$followupBtn = '<a class="dropdown-item leadAction"  onclick="edit('.$followupParam.');" >
									<ion-icon name="clipboard-outline"></ion-icon>  Followup
								</a>';

				$appointmentParam = "{'postData': {'id' : ".$row->id.",'party_id':".$row->party_id.",'entry_type':2}, 'modal_id' : 'modal-lg', 'form_id' : 'appointment', 'title' : 'Appointments', 'fnedit' : 'addAppointment', 'fnsave' : 'saveAppointment','res_function' : 'resAppointments', 'button' : 'close'}";
				$appointmentBtn = '<a class="dropdown-item leadAction"  onclick="edit('.$appointmentParam.');" >
									<ion-icon name="calendar-outline"></ion-icon>  Appointment
								</a>';
			endif;

			if($row->lead_status == 0 && empty($row->enq_id)):      
				$editParam = "{'postData' : {'id' : ".$row->id."}, 'modal_id' : 'modal-xl', 'form_id' : 'editLead', 'title' : 'Update Approach'}";
			
				$editButton = '<a class="dropdown-item leadAction"  onclick="edit('.$editParam.');" >
									<ion-icon name="pencil-outline"></ion-icon>  Edit
								</a>';

				$leadParam = "{'postData' : {'id' : ".$row->id."}, 'modal_id' : 'modal-md', 'form_id' : 'approachStatus', 'title' : 'Update Approach Status','fnedit':'approachStatus','fnsave':'saveApproachStatus'}";
				$leadStatusButton = '<a class="dropdown-item leadAction"  onclick="edit('.$leadParam.');">
											<ion-icon name="close-outline"></ion-icon>  Reject Approach
									</a>';
			endif;

			if(in_array($row->lead_status,[0,4])):
				$postData = ['party_id'=>$row->party_id,'lead_id'=>$row->id];
				$encodedData = urlencode(base64_encode(json_encode($postData)));
				$enqBtn = '<a class="dropdown-item leadAction"  href="'.base_url('salesEnquiry/create/'.$encodedData).'">
								<ion-icon name="help-circle-outline"></ion-icon> Carete Enquiry
							</a>';
			endif;

			$html.='<li>
						<div class="listItem item transition "  data-category="transition">
							<div class="in ">
								<div>
									<a href="#" class="text-bold-500 text-dark">'.$row->party_name.'</a>
									<div class="text-small  text-secondary \"><label class="text-bold">Approach No : </label> '.sprintf("%04d",$row->lead_no).' | <label class="text-bold">Approach Date : </label>'.formatDate($row->lead_date).'</div>
									<div class="text-small text-secondary "> <label class="text-bold">Lead From : </label> '.$row->lead_from.' | <label class="text-bold">Sales Executive : </label> '.$row->emp_name.'</div>
									<div class="text-small text-secondary "> <label class="text-bold">Followup Date: </label> '.$row->followupDate.' | <label class="text-bold">Next Followup : </label> '.$row->next_fup_date.'</div>
								</div>
								<div class="text-end">
									<div class="card-button dropdown">
										<button type="button" class="btn btn-link btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
											<ion-icon name="ellipsis-horizontal" role="img" class="md hydrated" aria-label="ellipsis horizontal"></ion-icon>
										</button>
										<div class="dropdown-menu dropdown-menu-end" style="">
											'.$enqBtn.$appointmentBtn.$followupBtn.$leadStatusButton.$editButton.$deleteButton.'
										</div>
									</div>
								</div>
							</div>
						</div>
					</li>';
		endforeach;
		return $html;
	}
    
	public function addLead(){
        $this->data['entry_type'] = $this->data['entryData']->id;
		$this->data['categoryList'] = $this->itemCategory->getCategoryList(['ref_id'=>0,'final_category'=>1]);
        $this->data['salesExecutives'] = $this->employee->getEmployeeList();
		$this->load->view($this->leadForm, $this->data);
	}

	public function edit(){
		$id = $this->input->post('id');
		$leadData = $this->leads->getLead($id);
		$this->data['dataRow'] = $leadData;
		$this->load->view($this->leadForm, $this->data);
	}

	public function addFollowup(){
        $data = $this->input->post();
		$this->data['lead_id'] = $data['id'];
		$this->data['entry_type'] = $data['entry_type'];
		$this->data['party_id'] = $data['party_id'];
		$this->data['sales_executive'] = $data['sales_executive'];
		$this->data['salesExecutives'] = $this->employee->getEmployeeList();
		
		$this->load->view($this->followupFrom, $this->data);
    }

    public function followupListHtml($data=array())	{
        $data = $this->input->post();
		$appintmentData = $this->leads->getAppointments($data);
		$html = '';
		if (!empty($appintmentData)):
			$i = 1;
			foreach ($appintmentData as $row):
				$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Followup','fndelete':'deleteApproachTans','res_function':'resTrashFollowup','controller':'lead','controller':'lead'}";

				$deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>';
				$html.='<tr>
					<td clas="text-center">'.$i++.'</td>
					<td clas="text-center">'.formatDate($row->appointment_date,'d-m-Y ').'</td>
					<td>'.$this->appointmentMode[$row->mode].'</td>
					<td>'.$row->executive_name.'</td>
					<!-- <td>'.$row->contact_person.'</td> -->
					<td>'.$row->notes.'</td>
					<td >'.$deleteBtn.'</td>
				</tr>';
			endforeach;
		else:
			$html = '<tr><th colspan="7" class="text-center">No data available.</th></tr>';
		endif;

		$this->printJson(['status'=>1,"tbodyData"=>$html]);
	}

	public function addAppointment(){
        $data = $this->input->post();
		$data['entry_type'] = 2;
		$this->data['lead_id'] = $data['id'];
		$this->data['appointmentMode'] = $this->appointmentMode;
		$this->load->view($this->appointmentForm, $this->data);
    }

    public function appointmentListHtml($data=array())	{
		$data = $this->input->post();
        $this->leads->getAppointments($data);
		$appointmentData = $this->leads->getAppointments($data);
		$html = '';
		if (!empty($appointmentData)):
			$i = 1;
			foreach ($appointmentData as $row):
				$deleteBtn = '';
				if (empty($row->status)):
					$deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Appointment','fndelete':'deleteApproachTans','res_function':'resTrashAppointment','controller':'lead'}";

					$deleteBtn = '<button type="button" onclick="trash('.$deleteParam.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>';
				endif;

				$html .= '<tr>
					<td clas="text-center">' . $i++ . '</td>
					<td clas="text-center">' . formatDate($row->appointment_date, 'd-m-Y ') . formatDate($row->appointment_time, 'H:i A') . '</td>
					<td>' . $this->appointmentMode[$row->mode] . '</td>
					<td>' . $row->contact_person . '</td>
					<td>' . $row->purpose . '</td>
					<td clas="text-center">' . $deleteBtn . '</td>
				</tr>';
			endforeach;
		else:
			$html = '<tr><th colspan="6" class="text-center">No data available.</th></tr>';
		endif;
		$this->printJson(['status'=>1,"tbodyData"=>$html]);
	}

	public function approachStatus(){
		$data = $this->input->post();
		$this->data['lead_id'] = $data['id'];
		$this->data['entry_type'] = (!empty($data['entry_type']))?$data['entry_type']:0;
		$this->load->view($this->leadStatusForm,$this->data);
	}

}
?>