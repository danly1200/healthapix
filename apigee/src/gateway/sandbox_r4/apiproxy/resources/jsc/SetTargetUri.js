var _targetUrl = context.getVariable("targetBaseURI");
var _exportCall = context.getVariable("isExportCall");
var _pathSufix = context.getVariable("proxy.pathsuffix");

var _updatedTargetUrl = "";
if(_exportCall){
  _updatedTargetUrl = _targetUrl + _pathSufix;  
  context.setVariable("request.header.Accept","application/fhir+json");
  context.setVariable("request.header.Content-Type","application/x-www-form-urlencoded");
  context.setVariable("request.header.Prefer","respond-async");
}

context.setVariable("targetBaseURI",_updatedTargetUrl);