package main

/*
jwt token is :
    eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiIweDEiLCJuYW1lIjoiSGFtaWQiLCJmYW1pbHlOYW1lIjoiWWFxb3RpIiwibW9iaWxlIjoiMDl4eHh4eHh4eHgiLCJnZW5kZXIiOiJtYWxlIiwiaWF0IjoxNTc5MjgxNzA1fQ.pjn58jAL90Az75nF3pPNgpQzMCUG4qiJWOFc202axdo

Data is :
    {
        "uid":"0x1",
        "name":"Hamid",
        "familyName":"Yaqoti",
        "mobile":"09xxxxxxxxx",
        "gender":"male",
        "iat": 1579281705

    }
Signature is : "mysecret"

*/

import (
	"fmt"
	"log"
	"time"

	"github.com/dgrijalva/jwt-go"
)

func main() {
	// تعریف ساختار توکن
	type Claims struct {
		Uid        string `json:"uid,omitempty"`
		Name       string `json:"name,omitempty"`
		FamilyName string `json:"familyName,omitempty"`
		Mobile     string `json:"mobile,omitempty"`
		Gender     string `json:"gender,omitempty"`
		jwt.StandardClaims
	}
	// ایجاد یک متغیر بر اساس ساختار و تنظیم کردن مقادیر
	claims := &Claims{
		Uid:        "0x1",
		Name:       "Hamid",
		FamilyName: "Yaqoti",
		Mobile:     "09xxxxxxxxx",
		Gender:     "male",
		StandardClaims: jwt.StandardClaims{
			IssuedAt: time.Now().Unix(),
		},
	}
	// تولید توکن با امضاء mysecret با متد HS256
	jwtToken, err := jwt.NewWithClaims(jwt.SigningMethodHS256, claims).SignedString([]byte("mysecret"))
	if err != nil {
		log.Panicln(err)
		return
	}
	// چاپ مقدار jwt تولید شده
	fmt.Println("JWT is :")
	fmt.Println(jwtToken)

	// توکن دکد کردن

	claims2 := &Claims{}

	TokenHandler, err := jwt.ParseWithClaims(jwtToken, claims2, func(token *jwt.Token) (interface{}, error) {
		//از امضا های متفاوت برای دکد کردن استفاده کنید و نتیجه را مشاهده کنید.
		return []byte("mysecret"), nil
	})
	if err != nil {
		log.Panicln(err)
		return
	}
	time.Sleep(3 * time.Second)
	fmt.Printf("this token Signed %.2f Seconds ago\n", time.Now().Sub(time.Unix(claims2.IssuedAt, 0)).Seconds())

	if !TokenHandler.Valid {
		fmt.Println("Invalid Signature")
	}
	fmt.Println(claims2)

}
