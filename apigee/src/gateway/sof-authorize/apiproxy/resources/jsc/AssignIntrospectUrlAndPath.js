try { 
var issuer_uri = context.getVariable("smart.introspectUrl");
var sc_introspect_urlpath = "";
var sc_introspect_urlhost = "";

var urlarr = issuer_uri.split("//")[1];
var urllength = urlarr.length
var slashindex = urlarr.indexOf("/");
var sc_introspect_urlhost = urlarr.substr(0,slashindex);
var sc_introspect_urlpath = urlarr.substr(urlarr.indexOf("/")+1,urllength);
context.setVariable("sc_introspect_urlhost",sc_introspect_urlhost);
context.setVariable("sc_introspect_urlpath",sc_introspect_urlpath);
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
