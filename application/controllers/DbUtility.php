<?php
class DbUtility extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    /* 
    *   Created BY : Milan Chauhan
    *   Created AT : 18-08-2023
    *   Required Data : password in query param
    *   Note : Export Sql file from live
    */
    public function exportDBfile(){
        if($this->uri->segment(3) == "Nbt-".date("dmY")):
            $NAME=$this->db->database;
            $SQL_NAME = $NAME."_".date("d_m_Y_H_i_s").'.sql';
            $this->load->dbutil();
            $prefs = [
                'format' => 'zip',
                'filename' => $SQL_NAME
            ];
            $backup =& $this->dbutil->backup($prefs);    
            $db_name = $NAME."_".date("d_m_Y_H_i_s").'.zip';    
            $save = 'assets/db/'.$db_name;
            $this->load->helper('file');
            write_file($save, $backup);
            $this->load->helper('download');
            force_download($db_name, $backup); 
        else:
            $this->load->view('page-403');
        endif;
    }

    /* 
    *   Created BY : Milan Chauhan
    *   Created AT : 18-08-2023
    *   Required Data : password
    *   Note : Return SQL Querys from live Database
    */
    public function syncLiveDB(){
        $postData = json_decode(file_get_contents('php://input'), true);
        print_r($postData);exit;
        if($postData['password'] == "Nbt-".date("dmY")):
            $NAME=$this->db->database;
            $SQL_NAME = $NAME."_".date("d_m_Y_H_i_s").'.sql';
            $this->load->dbutil();
            $prefs = [
                'format' => 'text',
                'filename' => $SQL_NAME,
                'newline' => "\r\n"
            ];
            $backup_temp = $this->dbutil->backup($prefs);
            $backup =& $backup_temp;
            print_r($backup);exit;
            print json_encode(['status'=>1,'message'=>"",'db_query'=>$backup]);exit;
        else:
            print json_encode(['status'=>0,'message'=>"Invalid Password.",'db_query'=>""]);exit;
        endif;        
    }

    /* 
    *   Created BY : Milan Chauhan
    *   Created AT : 18-08-2023
    *   Post Data : password
    *   Note : Get SQL Querys from live Database and Import in Local Database
    */
    public function syncDbQuery(){
        if($_SERVER['HTTP_HOST'] == 'localhost'):
            $data = $this->input->post();

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://admix.scubeerp.in/dbUtility/syncLiveDB",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);
            curl_close($curl);
            print_r($response);exit;
            if($error):
                print json_encode(['status'=>0,'message'=>'Somthing went wrong. cURL Error #: '. $error]);exit;
            else:
                $response = json_decode($response);	
                if($response->status == 0):
                    print json_encode(['status'=>0,'message'=>'Somthing went wrong. Error #: '. $response->message]);exit;
                else:
                    if(!empty($response->db_query)):
                        $this->db->query($response->db_query);
                        print json_encode(['status'=>1,'message'=>'Database sync successfully.']);exit;
                    else:
                        print json_encode(['status'=>0,'message'=>'Somthing went wrong. Error #: SQL QUERY not found.']);exit;
                    endif;
                endif;
            endif;   
        else:
            print json_encode(['status'=>0,'message'=>'Something went wrong. Error #: you cant sync. because you are in live project.']);exit;
        endif;
    }

    public function dbForm(){
        $this->load->view("db_form");
    }
}
?>