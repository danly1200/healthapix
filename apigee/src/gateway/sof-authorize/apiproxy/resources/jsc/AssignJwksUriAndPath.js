try { 
var url = context.getVariable("idpJwks.url");
var sc_jwks_urlpath = "";
var sc_jwks_urlhost = "";

var urlarr = url.split("//")[1];
var urllength = urlarr.length
var slashindex = urlarr.indexOf("/");
var sc_jwks_urlhost = urlarr.substr(0,slashindex);
var sc_jwks_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength);
context.setVariable("sc_jwks_urlhost",sc_jwks_urlhost);
context.setVariable("sc_jwks_urlpath",sc_jwks_urlpath);
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
