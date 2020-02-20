<?php
	/*
	 اطلاعات زیر رو باید با توجه به نرم افزار که در سایت من لاگین ساخته اید، تکمیل نمایید

	 برای افزودن نرم افزار جدید به این آدرس مراجعه نمایید:
	 https://manlogin.com/panel#/developers/apps
	*/
	require('core/jwt.php');
	
	$site_url 				= "http://localhost/sso/_example/php/php%20pure/"; //آدرس صفحه ای را که میخواهید بعد از بازگشت از اس اس او به آن ارجاع داده شوید، وارد کنید

	$manLogin_sso_uid 		="0x9c43"; //شناسه یکتا

	$manLogin_sso_publicKey ="76f5a0101f5828c9c791e48c33222476750ff037d7f1200ce8642c3289596c44"; //کلید عمومی

	$manLogin_sso_S2SToken 	="b35f268d41d79a949291559eda51f305e9ad5486cd697355aa75bac8872a6823"; //کلید ارتباط سرور به سرور

	$manLogin_sso_token 	="a6df5eca70a71d7f38354b05ce1536b828109a490bcd8c4ddbf79ed0457eae88"; //کلید ساختن هش ریکوئست


	$jwt = new JWT();
	$hash = $jwt->encode( array('exp'=>time()+360 ),$manLogin_sso_token );; // توکن تولید شده ایست برای ولید کردن ریکوئست مورد استفاده قرار میگیرد

	// اطلاعات کاربر
	$name		 	= "مهمان";
	$familyName 	= "";
	$sso_user_uid 	= "";
	$mobile			= "";

	if (isset($_GET['ticket'])) {
		$callBackData = $jwt->decode($_GET['ticket'],$manLogin_sso_publicKey); // decode ticket
		if (is_object($callBackData) && array_key_exists("mobile",$callBackData)){
			$sso_user_uid 	= $callBackData->uid;
			$mobile			= $callBackData->mobile;

			if ($sso_user_uid !=""){

				$opts = [
					"http" => [
						"method" => "GET",
						"header" => "X-App: $manLogin_sso_uid\r\n" .
							"X-S2SToken: $manLogin_sso_S2SToken\r\n"
					]
				];
				$context 	= stream_context_create($opts);
				$response 	= file_get_contents("https://manlogin.com/api/person/$sso_user_uid/data", false, $context);
				$g_response = json_decode($response, true );
				if(is_array($g_response)){
					if($g_response["Code"] == 200 && is_array($g_response["Data"])){
						$name 		= $g_response["Data"]["name"];
						$familyName = $g_response["Data"]["familyName"];
					}
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title>ManLogin SSO - PHP Sample Code</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body dir="rtl">
	<div class="contact1">
		<div class="container-contact1">
			<div class="contact1-pic js-tilt" data-tilt>
				<img src="images/manlogin.svg" alt="IMG">
			</div>

			<div id="myFormID" class="contact1-form validate-form">
				<span class="contact1-form-title">
					<?php
						if($mobile !=""){
							echo "کاربر گرامی ".$name." ".$familyName." خوش آمدید";
							echo "<br>";
							echo "شماره موبایل شما: ".$mobile;
						}else{
							echo $name." خوش آمدید. <br><br> برای ورود روی لینک زیر کلیک کنید:";
						}
					?>
				</span>
				<?php if($mobile !=""){ ?>
					<div>
						<hr style='margin: 10px;' />
						<div style='display: flex;justify-content: center;'>
							<a href='<?=$site_url;?>'>
								<img title='Login By ManLogin' src='images/logout.svg'>
							</a>
						</div>
						<hr style='margin: 10px;' />
					</div>
				<?php }else{?>
					<div>
					<hr style='margin: 10px;' />
					<div style='display: flex;justify-content: center;'>
						<a href='https://manlogin.com/cas/login?service=<?=$site_url;?>&hash=<?=$hash;?>'>
							<img title='Login By ManLogin' src='images/login-by-manlogin-sso.svg'>
						</a>
					</div>
					<hr style='margin: 10px;' />
				</div>
				<?php } ?>
			</div>
		</div>




		<!--===============================================================================================-->
		<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/bootstrap/js/popper.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/select2/select2.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/tilt/tilt.jquery.min.js"></script>
		<script>
			$('.js-tilt').tilt({
				scale: 1.1
			})
		</script>
		<!--===============================================================================================-->
		<script src="js/main.js"></script>
</body>

</html>