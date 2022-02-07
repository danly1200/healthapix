try { 
var userinfo_uri = context.getVariable("userInfoUrl");
var sc_userInfo_urlpath = "";
var sc_userInfo_urlhost = "";

//var urlarr = userinfo_uri.split("//")[1];
//var urllength = urlarr.length
//var slashindex = urlarr.indexOf("/");
//var sc_userInfo_urlhost = urlarr.substr(0,slashindex);
//var sc_userInfo_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength);
//context.setVariable("sc_userInfo_urlhost",sc_userInfo_urlhost);
//context.setVariable("sc_userInfo_urlpath",sc_userInfo_urlpath);

//Following var for temp mocking remove this and uncomment when done
context.setVariable("sc_userInfo_urlhost","personas-craigv-dot-hcls-data-connect-demo.appspot.com");
context.setVariable("sc_userInfo_urlpath","oidc/userinfo");
context.setVariable("req.Authorization","Bearer eyJhbGciOiJSUzM4NCIsImtpZCI6InRlc3RrZXlzLXBlcnNvbmEtYnJva2VyIiwidHlwIjoiSldUIn0.eyJleHAiOjE2MTI5ODAwMjgsImp0aSI6InRva2VuLWlkLW5jaV9yZXNlYXJjaGVyIiwiaWF0IjoxNjEyOTcwMDI4LCJpc3MiOiJodHRwczovL3BlcnNvbmFzLWNyYWlndi1kb3QtaGNscy1kYXRhLWNvbm5lY3QtZGVtby5hcHBzcG90LmNvbS9vaWRjIiwic3ViIjoibmNpX3Jlc2VhcmNoZXIiLCJzY29wZSI6Im9wZW5pZCBnYTRnaF9wYXNzcG9ydF92MSIsImlkZW50aXRpZXMiOnsibmNpX3Jlc2VhcmNoZXJAbmNpLm5paC5nb3YiOlsiSUMiLCJEQU0iXX19.DNdAyPHbV0rzl-E_O9Kbs_gEmIYPwgnOAj64X1vSrVLjwXpbm6IJWtd9YUAyojMqeMoNOHLfuCk16Vs1vL9K5KOBz1qnos5ndhFe34wPwKg2AmOh_mqyCxK4LRQpMvzwFK2K-E9vFdiSuNCmh10Mf6yrjs9ktb3QrjrDmr0AbG0");

}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
