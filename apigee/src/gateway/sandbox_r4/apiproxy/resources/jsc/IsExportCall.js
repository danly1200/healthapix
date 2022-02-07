var _reqVerb = context.getVariable("request.verb");
var _reqPath = context.getVariable("proxy.pathsuffix");
var _reqUpdateMask = context.getVariable("request.queryparam.updateMask.values.count");
var isExportCall = false;
var isExportPatchCall = false;


if(_reqVerb === "PATCH" && !_reqPath && _reqUpdateMask == 1){
    isExportCall = true;
    isExportPatchCall = true;
    
}

if(_reqVerb === "GET" && _reqPath === "/$export"){
    isExportCall = true;
} 

context.setVariable("target.copy.pathsuffix",false);
context.setVariable("isExportCall",isExportCall);
context.setVariable("isExportPatchCall",isExportPatchCall);


