<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
	{
        parent::__construct();
		$this->load->helper('jwt');
		/*
		اطلاعات زیر رو باید با توجه به نرم افزار که در سایت من لاگین ساخته اید، تکمیل نمایید

		برای افزودن نرم افزار جدید به این آدرس مراجعه نمایید:
		https://manlogin.com/panel#/developers/apps
		*/

		$this->site_url 				= base_url("index.php/welcome/callback"); //آدرس صفحه ای را که میخواهید بعد از بازگشت از اس اس او به آن ارجاع داده شوید، وارد کنید
		$this->manLogin_sso_uid 		="0x9c43"; //شناسه یکتا
		$this->manLogin_sso_publicKey 	="76f5a0101f5828c9c791e48c33222476750ff037d7f1200ce8642c3289596c44"; //کلید عمومی
		$this->manLogin_sso_S2SToken 	="b35f268d41d79a949291559eda51f305e9ad5486cd697355aa75bac8872a6823"; //کلید ارتباط سرور به سرور
		$this->manLogin_sso_token 		="a6df5eca70a71d7f38354b05ce1536b828109a490bcd8c4ddbf79ed0457eae88"; //کلید ساختن هش ریکوئست
	}

	public function index()
	{

		$data["site_url"] 				= $this->site_url;
		$data["manLogin_sso_uid"] 		= $this->manLogin_sso_uid;
		$data["manLogin_sso_publicKey"] = $this->manLogin_sso_publicKey;
		$data["manLogin_sso_S2SToken"] 	= $this->manLogin_sso_S2SToken;
		$data["manLogin_sso_token"] 	= $this->manLogin_sso_token;

		$data["hash"] = JWT::encode( array('exp'=>time()+360 ),$this->manLogin_sso_token );; // توکن تولید شده ایست برای ولید کردن ریکوئست مورد استفاده قرار میگیرد

		// اطلاعات کاربر
		$data["name"]		 	= "مهمان";
		$data["familyName"] 	= "";
		$data["sso_user_uid"] 	= "";
		$data["mobile"]			= "";
		$this->load->view('welcome_message',$data);
	}
	public function callback()
	{
		if (isset($_GET['ticket'])) {

			$data["site_url"] 				= $this->site_url;
			// اطلاعات کاربر
			$data["name"]		 	= "مهمان";
			$data["familyName"] 	= "";
			$data["sso_user_uid"] 	= "";
			$data["mobile"]			= "";

			
			$callBackData = JWT::decode($_GET['ticket'],$this->manLogin_sso_publicKey); // decode ticket
			if (is_object($callBackData) && array_key_exists("mobile",$callBackData)){
				$data["sso_user_uid"] 	= $callBackData->uid;
				$data["mobile"]			= $callBackData->mobile;
	
				if ($data["sso_user_uid"] !=""){
	
					$opts = [
						"http" => [
							"method" => "GET",
							"header" => "X-App: $this->manLogin_sso_uid\r\n" .
								"X-S2SToken: $this->manLogin_sso_S2SToken\r\n"
						]
					];
					$context 	= stream_context_create($opts);
					$response 	= file_get_contents("https://manlogin.com/api/person/".$data["sso_user_uid"]."/data", false, $context);
					$g_response = json_decode($response, true );
					if(is_array($g_response)){
						if($g_response["Code"] == 200 && is_array($g_response["Data"])){
							$data["name"] 		= $g_response["Data"]["name"];
							$data["familyName"] = $g_response["Data"]["familyName"];
						}
					}
				}
			}
			$this->load->view('welcome_message',$data);
		}else{
			redirect(base_url());
		}		
	}
}
