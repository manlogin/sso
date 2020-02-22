using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using manlogin_sso.Models;
using System.IdentityModel.Tokens.Jwt;
using Microsoft.IdentityModel.Tokens;
using System.Security.Claims;
using System.Text;
using System.Net.Http;
using Newtonsoft.Json;

namespace manlogin_sso.Controllers
{
    public class HomeController : Controller
    {
        private readonly ILogger<HomeController> _logger;

        public HomeController(ILogger<HomeController> logger)
        {
            _logger = logger;
        }

        /*
		اطلاعات زیر رو باید با توجه به نرم افزار که در سایت من لاگین ساخته اید، تکمیل نمایید

		برای افزودن نرم افزار جدید به این آدرس مراجعه نمایید:
		https://manlogin.com/panel#/developers/apps
		*/

        private const string site_url 				    = "https://localhost:5001/Home/Index/"; //آدرس صفحه ای را که میخواهید بعد از بازگشت از اس اس او به آن ارجاع داده شوید، وارد کنید
		private const string manLogin_sso_uid 		    ="0x9c43"; //شناسه یکتا
		private const string manLogin_sso_publicKey 	="76f5a0101f5828c9c791e48c33222476750ff037d7f1200ce8642c3289596c44"; //کلید عمومی
		private const string manLogin_sso_S2SToken 	    ="b35f268d41d79a949291559eda51f305e9ad5486cd697355aa75bac8872a6823"; //کلید ارتباط سرور به سرور
		private const string manLogin_sso_token 		="a6df5eca70a71d7f38354b05ce1536b828109a490bcd8c4ddbf79ed0457eae88"; //کلید ساختن هش ریکوئست

        public IActionResult Index()
        {

            //اطلاعات پایه ای که از یه کاربر در  ویو نمایش داده خواهد شد
            var name		 	= "مهمان";
            var familyName 	    = "";
            var sso_user_uid 	= "";
            var mobile			= "";

            var jwt = new JwtSecurityTokenHandler();
            ViewData["login"]       = false; //وضعبت لاگین کاربر
            ViewData["callbackUrl"] = site_url;
            ViewData["hash"]        = ""; // برای اطلاعات بیشتر در این مورد به داکیومنت مراجعه شود
            

            string ticket = HttpContext.Request.Query["ticket"].ToString(); // وقتی که کاربر از sso  برگشته باشد
            if (ticket != "")
            {
                ViewData["login"]       = true;

                var key = Encoding.UTF8.GetBytes(manLogin_sso_publicKey);
                var validations = new TokenValidationParameters
                {
                    ValidateIssuerSigningKey = true,
                    IssuerSigningKey = new SymmetricSecurityKey(key),
                    ValidateIssuer = false,
                    ValidateAudience = false
                };
                var jsonToken = jwt.ValidateToken(ticket, validations, out var tokenSecure); // تیکت برگشتی از اس اس او رو  اعتبار سنجی میکنه
                
                sso_user_uid = jsonToken.Claims.First(claim => claim.Type == "uid").Value;
                mobile       = jsonToken.Claims.First(claim => claim.Type == "mobile").Value;
                if (sso_user_uid !="")
                {
                    var url        = "https://manlogin.com/api/person/"+sso_user_uid+"/data";

                    // var requestMessage = new HttpRequestMessage(HttpMethod.Get, url)
                    // requestMessage.Headers.Authorization = new AuthenticationHeaderValue("Bearer", manLogin_sso_uid);

                    var client = new HttpClient();
                    client.DefaultRequestHeaders.Add("X-App", manLogin_sso_uid);
                    client.DefaultRequestHeaders.Add("X-S2SToken", manLogin_sso_S2SToken);
                    var res = client.GetAsync(url); // اطلاعات تکمیلی کاربر رو از sso  دریافت می کند
                    dynamic response = res.Result.Content.ReadAsStringAsync().Result;
                    var result = JsonConvert.DeserializeObject(response);
                    if (result["Code"] == "200")
                    {
                        name        = result["Data"]["name"];
                        familyName  = result["Data"]["familyName"];
                    }
                }
            }else{
                var signinKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(manLogin_sso_token));

                var now = DateTime.UtcNow;
                var tokenDescriptor = new SecurityTokenDescriptor
                {
                    Expires = now.AddMinutes(Convert.ToInt32(20)),
                    SigningCredentials = new SigningCredentials(
                        signinKey, 
                        SecurityAlgorithms.HmacSha256Signature)
                };
                var stoken = jwt.CreateToken(tokenDescriptor);
                var token = jwt.WriteToken(stoken); // generate jwt
                ViewData["hash"]        = token; 
            }

            ViewData["name"]        = name;
            ViewData["familyName"]  = familyName;
            ViewData["sso_user_uid"]= sso_user_uid;
            ViewData["mobile"]      = mobile;
            
            return View();
        }

        public IActionResult Privacy()
        {
            return View();
        }

        [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
        public IActionResult Error()
        {
            return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
        }
    }
}
