try { 
var issuer_uri = context.getVariable("FHIR_ISSUER");
var sc_smartwellknown_urlpath = "";
var sc_smartwellknown_urlhost = "";

var urlarr = issuer_uri.split("//")[1];
var urllength = urlarr.length
var slashindex = urlarr.indexOf("/");
var sc_smartwellknown_urlhost = urlarr.substr(0,slashindex);
var sc_smartwellknown_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength) + "/.well-known/smart-configuration";
context.setVariable("sc_smartwellknown_urlhost",sc_smartwellknown_urlhost);
context.setVariable("sc_smartwellknown_urlpath",sc_smartwellknown_urlpath);
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
