try { 
var jwks_uri = context.getVariable("verifyapikey.HPX_VAK_ValidateApiKey.jwks_uri");
var sc_urlpath = "";
var sc_urlhost = "";

var urlarr = jwks_uri.split("//")[1];
var urllength = urlarr.length
var slashindex = urlarr.indexOf("/");
var sc_urlhost = urlarr.substr(0,slashindex);
var sc_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength);
context.setVariable("sc_urlhost",sc_urlhost);
context.setVariable("sc_urlpath",sc_urlpath);
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
