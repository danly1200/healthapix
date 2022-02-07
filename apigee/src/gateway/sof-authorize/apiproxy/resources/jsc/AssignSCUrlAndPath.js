try { 
var issuer_uri = context.getVariable("FHIR_ISSUER");
var sc_wellknown_urlpath = "";
var sc_wellknown_urlhost = "";

var urlarr = issuer_uri.split("//")[1];
var urllength = urlarr.length
var slashindex = urlarr.indexOf("/");
var sc_wellknown_urlhost = urlarr.substr(0,slashindex);
var sc_wellknown_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength) + "/.well-known/openid-configuration";
context.setVariable("sc_wellknown_urlhost",sc_wellknown_urlhost);
context.setVariable("sc_wellknown_urlpath",sc_wellknown_urlpath);
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
