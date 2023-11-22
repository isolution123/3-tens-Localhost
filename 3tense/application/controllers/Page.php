<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller {



 function __construct()

    {

        parent :: __construct();

        $this->load->library('session');

        $this->load->helper('form');

        $this->load->helper('url');

        $this->load->helper('html');

        $this->load->library('form_validation');

        $this->load->library('Custom_exception');

        $this->load->library('Mail', 'mail');

        //load adminUsers model

        $this->load->model('Users_model');

        $this->load->model('Common_model','common');

        $this->load->model('Reminders_model','reminder');

        $this->load->model('Scripts_model', 'script');

    }



    public function index()

    {



        if($this->session->userdata('user_id') && $this->session->userdata('broker_id')) {

            redirect('broker/Dashboard');

        } else {

            $data['title']='3tense';

            //$this->load->view('broker/user/login', $data);
            $this->load->view('client/Client_login', $data);

        }

    }



    public function logout()

    {

        $this->session->unset_userdata('user_id');

        redirect('broker/Users');

    }



    public function login()

    {

        $this->form_validation->set_rules('username', 'Username', 'trim|required');

        $this->form_validation->set_rules('password', 'Password', 'trim|required');



        if($this->form_validation->run() == FALSE)

        {

            //validation fails

            $this->load->view('users/index');

        }

        else

        {

            //validation succeeds

            if ($this->input->post('btn_login') == "Login")

            {

                //check if username and password is correct

                $username = $this->input->post('username');

                $pwd = $this->input->post('password');

                $result = $this->Users_model->userAuth($username, $pwd);



                if($result)

                {

                    foreach($result as $res)

                    {

                        $broker_id = $res->broker_id;

                        if($res->broker_id == null)

                            $broker_id = $res->id;

                        $sess_user = array(

                            'user_id'=>$res->id,

                            'name'=>$res->name,

                            'username'=>$res->username,

                            'broker_id'=>$broker_id

                        );

                        $this->session->set_userdata($sess_user);

                    }

                    redirect('broker/Dashboard');

                }

                else

                {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-center">Invalid username and/or password!</div>');

                    redirect('broker/users/index');

                }

            }

        }

    }



    //Salmaan - 3/3/16

    //Last import report

    function last_imports() {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        //data to pass to header view like page title, css, js

        $header['title']='Last Imports';

        $header['css'] = array(

            'assets/users/plugins/datatables/css/jquery.dataTables.min.css'

        );

        $header['js'] = array(

            'assets/users/plugins/form-parsley/parsley.min.js',

            'assets/users/plugins/bootbox/bootbox.min.js',

            'assets/users/plugins/datatables/js/jquery.dataTables.min.js',

            'assets/users/js/dataTables.js'

        );

        //load views

        $this->load->view('broker/common/header', $header);

        $this->load->view('broker/user/last_imports');

        $this->load->view('broker/common/notif');

        $this->load->view('broker/common/footer');

    }



    //gets all last imports of admin & current broker from database

    public function ajax_list_last_imports()

    {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $brokerID = $this->session->userdata('broker_id');

        $list = $this->common->get_last_imports("li.broker_id = '".$brokerID."' or li.broker_id is null");



        $data = array();

        $num = 10;

        if(isset ($_POST['start']))

            $num = $_POST['start'];

        foreach($list as $record)

        {

            $num++;

            $row = array();

            $row['import_type']=$record->import_type;

            $row['last_import_date']=$record->last_import_date;

            $row['file_name']=$record->file_name;

            if(!empty($record->name)) {

                $row['name']=$record->name;

            } else {

                $row['name']='SYSTEM/ADMINISTRATOR';

            }



            $data[] = $row;

        }

        $output = array(

            "draw"=>1,

            //"recordsTotal"=>$this->family->count_all($brokerID),

            //"recordsFiltered"=>$this->family->count_filtered(),

            "data"=>$data

        );

        //output to json format

        echo json_encode($output);

    }





    /* USERS functions Start */

    // Manage users under a broker

    function manage_users() {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        if($this->session->userdata('user_id') == $this->session->userdata('broker_id')) {

            //data to pass to header view like page title, css, js

            $header['title']='Manage Users';

            $header['css'] = array(

                'assets/users/plugins/datatables/css/jquery.dataTables.min.css',

                'assets/users/plugins/form-select2/select2.css'

            );

            $header['js'] = array(

                'assets/users/plugins/form-parsley/parsley.min.js',

                'assets/users/plugins/bootbox/bootbox.min.js',

                'assets/users/plugins/datatables/js/jquery.dataTables.min.js',

                'assets/users/js/dataTables.js',

                'assets/users/plugins/form-select2/select2.min.js',

            );

            //load views

            $this->load->view('broker/common/header', $header);

            $this->load->view('broker/user/manage_users');

            $this->load->view('broker/common/notif');

            $this->load->view('broker/common/footer');

        } else {

            $data['heading'] = 'Oops looks like you are lost';

            $data['message'] = 'You might have come to this page accidentally, or you might have been trying to access something which you should not. So please go back.';

            $this->load->view('errors/html/error_404', $data);

        }

    }



    //gets all users of current broker from database

    public function ajax_list_users()

    {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $brokerID = $this->session->userdata('broker_id');

        $list = $this->Users_model->get_users("broker_id = '".$brokerID."'");



        $data = array();

        $num = 10;

        if(isset ($_POST['start']))

            $num = $_POST['start'];

        foreach($list as $user)

        {

            $num++;

            $row = array();

            $row['name']=$user->name;

            $row['mobile']=$user->mobile;

            $row['email_id']=$user->email_id;

            $row['username']=$user->username;

            if($user->status == '1') {

                $row['status']='Active';

            } else {

                $row['status']='Inactive';

            }

            $row['last_login']=$user->last_login;



            $row['add_info']=$user->add_info;



            //add html for action edit

            $row['action'] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit"

                    onclick="edit_user('."'".$user->id."'".')">

                    <i class="fa fa-pencil"> Edit</i></a>';



            $data[] = $row;

        }

        $output = array(

            "draw"=>1,

            //"recordsTotal"=>$this->family->count_all($brokerID),

            //"recordsFiltered"=>$this->family->count_filtered(),

            "data"=>$data

        );

        //output to json format

        echo json_encode($output);

    }



    // function to add new user - called by ajax

    public function add_user() {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $broker_id = $this->session->userdata('broker_id');

        $user_data = $this->input->post();

        unset($user_data['id']); //remove the ID part, it will be added in Users_model(generated by brokerID() func)

        unset($user_data['password2']); //remove the confirm password value

        $user_data['password'] = sha1($user_data['password']);

        $user_data['broker_id'] = $broker_id;



        $condition = "`username` = '".$user_data['username']."'";



        $isDuplicate = $this->common->check_duplicate('username','users',$condition);



        if(!$isDuplicate) {

            try

            {

                $admin_condition = "`id` = '".$broker_id."'";

                $admin = $this->Users_model->get_users($admin_condition);

                if(!empty($admin)) {

                    $admin_id = $admin[0]->admin_id;

                    $user_data['admin_id'] = $admin_id;

                    $inserted = $this->Users_model->add_user($user_data);

                    if($inserted && !is_array($inserted)) {

                        $success = array(

                            "type" => "success",

                            "title" => "New User added!",

                            "text" => "User `".$user_data['name']."` added successfully.",

                            "id" => $inserted

                        );



                        //send mail to new user

                        $subject = "Welcome to 3tense Portfolio Management System";

                        $body = "<p>Congratulations! You have been added as a user of 3tense Portfolio Management System.</p>";

                        $body .= "<p>You can login to your account by clicking <a href='".base_url('broker')."'>this link</a> using the following credentials: </p>";

                        $body .= "<p>Username: <span style='font-weight: bold;'>".$user_data['username']."</span></p>";

                        $body .= "<p>Password: <span style='font-weight: bold;'>".$user_data['password']."</span></p>";



                        $result = $this->mail->send_mail_common($user_data['email_id'], $subject, $body, $broker_id);

                        //echo json_encode(var_dump($result));

                        if($result == "success") {

                            $response = array(

                                "title" => "Email sent to user!",

                                "text" => "An email with the login info has been sent to the user successfully",

                                "type" => "success"

                            );

                        } else {

                            $response = array(

                                "title" => "Could not send email to user!",

                                "text" => $result.'  Please give the login credentials to the user for him/her to get access.',

                                "type" => "error"

                            );

                        }

                        $data[] = $success; $data[] = $response;

                        echo json_encode($data);



                    } else {

                        throw new Custom_exception();

                    }

                } else {

                    $error = array(

                        "type" => "error",

                        "title" => "Could not add User!",

                        "text" => "Unable to determine Admin ID. Please try adding again."

                    );

                    echo json_encode($error);

                }

            }

            catch(Custom_exception $e)

            {

                //display custom message

                $response = array(

                    "title" => "Could not add user!",

                    "text" => $e->errorMessage($inserted['code']),

                    "type" => "error"

                );

                echo json_encode($response);

            }

        } else {

            $error = array(

                "type" => "error",

                "title" => "Username already exists!",

                "text" => "The username you have entered already exists. Please change the username of the User."

            );

            echo json_encode($error);

        }

    }



    //get user by id

    public function edit_user()

    {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $brokerID = $this->session->userdata('broker_id');

        $data = $this->Users_model->get_users("id = '".$this->input->post('id')."'");

        echo json_encode($data[0]);

    }



    public function update_user()

    {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $broker_id = $this->session->userdata('broker_id');

        $user_data = $_POST;

        if(isset($user_data['password'])) {

            unset($user_data['password']);

        }

        if(isset($user_data['password2'])) {

            unset($user_data['password2']);

        }

        $user_data['broker_id'] = $broker_id;

        $condition = "`username` = '".$user_data['username']."' AND `id` <> '".$user_data['id']."'";



        $isDuplicate = $this->common->check_duplicate('username','users',$condition);



        if(!$isDuplicate) {

            try

            {

                $updated = $this->Users_model->update_user($user_data, "id = '".$user_data['id']."'");

                if($updated && !is_array($updated)) {

                    $success = array(

                        "type" => "success",

                        "title" => "User details updated!",

                        "text" => "Details of selected user updated successfully.",

                        "id" => $updated

                    );

                    echo json_encode($success);

                } else {

                    throw new Custom_exception();

                }

            }

            catch(Custom_exception $e)

            {

                //display custom message

                $response = array(

                    "title" => "Could not update user details!",

                    "text" => $e->errorMessage($updated['code']),

                    "type" => "error"

                );

                echo json_encode($response);

            }

        } else {

            $error = array(

                "type" => "error",

                "title" => "Username already exists!",

                "text" => "The username you have entered already exists. Please change the username of the User."

            );

            echo json_encode($error);

        }

    }



    //end of day function - sends completed reminders email to broker

    function eod() {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $broker_id = $this->session->userdata('broker_id');



        $conditions = '(reminder_type <> "Shares Negative Balance" AND reminder_status = "Complete" AND reminder_date IS NOT NULL

                            AND DATEDIFF(reminder_date,CURDATE()) = 0 AND client_name IS NOT NULL AND broker_id = "'.$broker_id.'")

                        OR (reminder_status = "Complete" AND DATEDIFF(completed_on,CURDATE()) = 0 AND completed_on IS NOT NULL

                            AND broker_id = "'.$broker_id.'")';

        $data = $this->reminder->get_complete_reminders($conditions);

        $emailBroker = $this->script->broker_email($broker_id);

        $result = $this->mail->send_mail($emailBroker->email_id, $data, $broker_id, 'Broker');

        //echo json_encode(var_dump($result));

        if($result == "success") {

            $response = array(

                "title" => "Email sent to broker!",

                "text" => "Complete Reminders Email has been sent successfully",

                "type" => "success"

            );

        } else {

            $response = array(

                "title" => "Could not send Complete Reminders email!",

                "text" => $result,

                "type" => "error"

            );

        }

        echo json_encode($response);

    }



    //function to view/edit broker/user profile

    function profile() {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $brokerID = $this->session->userdata('broker_id');

        $userID = $this->session->userdata('user_id');

        $header['title']='Your Profile';

        $header['css'] = array(

            'assets/users/plugins/form-select2/select2.css'

        );

        $header['js'] = array(

            'assets/users/plugins/form-parsley/parsley.min.js',

            'assets/users/demo/demo-formvalidation.js',

            'assets/users/plugins/form-validation/jquery.validate.min.js',

            'assets/users/plugins/form-select2/select2.min.js',

            'assets/users/plugins/form-datepicker/js/bootstrap-datepicker.js',

            'assets/users/plugins/form-jasnyupload/fileinput.js',

            'assets/users/plugins/form-inputmask/jquery.inputmask.bundle.min.js',

            'assets/users/plugins/bootbox/bootbox.min.js',

            'assets/users/js/common.js'

        );



        $info = $this->Users_model->get_users(array('id'=>$userID));

        $data = array();

        if($info) {

            $data['info'] = $info[0];

            if((glob("uploads/brokers/".$brokerID."/*.*"))) {

                $data['info']->image = glob("uploads/brokers/".$brokerID."/*.*")[0];

            }

        } else {

            $response = array(

                "title" => "Could not fetch profile data!",

                "text" => "Your profile details could not be fetched. Please try again."

            );

            $this->session->set_userdata('error', $response);

        }



        $this->load->view('broker/common/header', $header);

        $this->load->view('broker/user/profile', $data);

        $this->load->view('broker/common/notif');

        $this->load->view('broker/common/footer');

    }



    public function update_profile()

    {

        //check if user is logged in by checking his/her session data

        //if user is not logged redirect to login

        if(empty($this->session->userdata['user_id']))

        {

            redirect('broker');

        }



        $broker_id = $this->session->userdata('broker_id');

        $user_data = $_POST;

        //check if password fields are properly filled

        if((empty($user_data['old_password']) && empty($user_data['password']) && empty($user_data['password2'])) ||

            (!empty($user_data['old_password']) && !empty($user_data['password']) && !empty($user_data['password2']))) {



            $user_data['broker_id'] = $broker_id;

            if(!empty($user_data['password'])) {

                if($user_data['password'] != $user_data['password2']) {

                    $response = array(

                        "title" => "Could not update profile!",

                        "text" => "Your new passwords do not match. Please enter the same password in New Password & Confirm Password fields.",

                        "type" => "error"

                    );

                    echo json_encode($response);

                    die();

                } else {

                    //unset old password & confirm password, and set the new password with hash

                    unset($user_data['old_password']);

                    unset($user_data['password2']);

                    $user_data['password'] = sha1($user_data['password']);

                }

            } else {

                unset($user_data['old_password']);

                unset($user_data['password']);

                unset($user_data['password2']);

            }



            //check if username is present, if yes check if its duplicate. Else not duplicate and proceed.

            if(isset($user_data['username'])) {

                $condition = "`username` = '".$user_data['username']."' AND `id` <> '".$user_data['id']."'";

                $isDuplicate = $this->common->check_duplicate('username','users',$condition);

            } else {

                $isDuplicate = false;

            }



            if(!$isDuplicate) {

                //check if user is broker, then process logo image, else don't

                if($user_data['id'] == $broker_id) {

                    // report image part start

                    // get existing photo, if exists

                    if((glob("uploads/brokers/".$broker_id."/*.*"))) {

                        $photoFilename = basename(glob("uploads/brokers/".$broker_id."/*.*")[0]);

                    } else {

                        $photoFilename = '';

                    }



                    $photoUploaded = true; // photo assumed to be uploaded by default

                    if (isset($_POST['image']) && $_POST['image'] == '') {

                        // Delete file

                        unlink(glob("uploads/brokers/".$broker_id."/*.*")[0]);

                    } elseif ($_FILES['image']['error'] == 0)  {

                        // delete file if present, @unlink is used to suppress error (if file did not exist, and we try to unlink)

                        @unlink(glob("uploads/brokers/".$broker_id."/*.*")[0]);

                        // Save uploaded file

                        $photoUploaded = $this->uploadPhoto();

                    } else {

                        // photo is the same, so don't do anything

                    }



                    if($photoUploaded !== true) {

                        echo json_encode($photoUploaded);

                        die();

                    }

                    //report image part End, data update part start

                    //now we can unset image element from $user_data

                    unset($user_data['image']);

                }



                try

                {

                    $updated = $this->Users_model->update_user($user_data, "id = '".$user_data['id']."'");

                    if($updated && !is_array($updated)) {

                        $success = array(

                            "type" => "success",

                            "title" => "Profile details updated!",

                            "text" => "Details of your profile updated successfully.",

                            "id" => $updated

                        );

                        echo json_encode($success);

                    } else {

                        throw new Custom_exception();

                    }

                }

                catch(Custom_exception $e)

                {

                    //display custom message

                    $response = array(

                        "title" => "Could not update profile!",

                        "text" => $e->errorMessage($updated['code']),

                        "type" => "error"

                    );

                    echo json_encode($response);

                }

            } else {

                $error = array(

                    "type" => "error",

                    "title" => "Username already exists!",

                    "text" => "The username you have entered already exists. Please change the username of the User."

                );

                echo json_encode($error);

            }



        } else {

            $response = array(

                "title" => "Could not update profile!",

                "text" => "Please check/correct your password fields properly and try saving again",

                "type" => "error"

            );

            echo json_encode($response);

        }

    }



    public function uploadPhoto() {

        if ($_FILES["image"]["error"] > 0)

        {

            // if there is error in file uploading

            $error = array(

                "title" => "Error uploading photo!",

                "text" => "Failed to upload! Return Code: " . $_FILES["image"]["error"],

                "type" => "error"

            );

            return $error;

        }

        else

        {

            $broker_id = $this->session->userdata('broker_id');

            $path = "uploads/brokers/".$broker_id;



            // check if file already exit in "uploads/clients/clientID" folder.

            if (file_exists($path."/".$_FILES["image"]["name"]))

            {

                $error = array(

                    "title" => "Already exists!",

                    "text" => "Filename '" . $_FILES["image"]["name"] . "' already exists.",

                    "type" => "error"

                );

                return $error;

            }

            else

            {   // create client directory if not exists

                if (!is_dir($path)) {

                    mkdir($path, 0777, true);

                }

                //move_uploaded_file function will upload your image.

                if(move_uploaded_file($_FILES["image"]["tmp_name"], $path."/".$_FILES["image"]["name"]))

                {

                    // If file has uploaded successfully, return TRUE

                    return true;

                }

            }

        }

    }

}

?>