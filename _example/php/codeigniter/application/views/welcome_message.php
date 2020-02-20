<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Welcome to Manlogin SSO </title>

	<style type="text/css">
		::selection {
			background-color: #E13300;
			color: white;
		}

		::-moz-selection {
			background-color: #E13300;
			color: white;
		}

		body {
			background-color: #fff;
			margin: 40px;
			font: 13px/20px normal Helvetica, Arial, sans-serif;
			color: #4F5155;
		}

		a {
			color: #003399;
			background-color: transparent;
			font-weight: normal;
		}

		h1 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 19px;
			font-weight: normal;
			margin: 0 0 14px 0;
			padding: 14px 15px 10px 15px;
		}

		code {
			font-family: Consolas, Monaco, Courier New, Courier, monospace;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}

		#body {
			margin: 0 15px 0 15px;
		}

		p.footer {
			text-align: right;
			font-size: 11px;
			border-top: 1px solid #D0D0D0;
			line-height: 32px;
			padding: 0 10px 0 10px;
			margin: 20px 0 0 0;
		}

		#container {
			margin: 10px;
			border: 1px solid #D0D0D0;
			box-shadow: 0 0 8px #D0D0D0;
		}

		.submit-btn {
			color: white;
			background-color: lightslategray;
			border-radius: 25px;
			padding-right: 30px;
			padding-left: 30px;
			padding-top: 8px;
			padding-bottom: 8px;
		}

		.logo{
			max-width: 95px;
		}

	</style>
</head>

<body dir="rtl">

	<div id="container">
		<h1>Welcome to CodeIgniter!</h1>

		<div id="body">
			<div style='display: flex;justify-content: center;'>
				<img class="logo" src="<?=base_url("images/manlogin.svg");?>" alt="IMG">
			</div>
			<div style='display: flex;justify-content: center;'>
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
			</div>
			<div>
				<?php if($mobile !=""){ ?>
				<div>
					<hr style='margin: 10px;' />
					<div style='display: flex;justify-content: center;'>
						<a href='<?=$site_url;?>'>
							<img title='Login By ManLogin' src='<?=base_url("images/logout.svg");?>'>
						</a>
					</div>
					<hr style='margin: 10px;' />
				</div>
				<?php }else{?>
				<div>
					<hr style='margin: 10px;' />
					<div style='display: flex;justify-content: center;'>
						<a href='https://manlogin.com/cas/login?service=<?=$site_url;?>&hash=<?=$hash;?>'>
							<img title='Login By ManLogin' src='<?=base_url("images/login-by-manlogin-sso.svg");?>'>
						</a>
					</div>
					<hr style='margin: 10px;' />
				</div>
				<?php } ?>
			</div>
		</div>
		<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds.
			<?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
		</p>

	</div>
</body>

</html>
