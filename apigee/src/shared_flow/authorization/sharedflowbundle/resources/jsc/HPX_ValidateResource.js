var supportedResources = JSON.parse(context.getVariable("resourceList"));
var primaryResource = context.getVariable("resourceName");
var pathSuffix = context.getVariable("proxy.pathsuffix");
var isValidResource = false;
var isGetPage = context.getVariable("request.queryparam._getpages.values.count") == 1;
var _isExportCall = context.getVariable("isExportCall");
var _isExportStatusCall = context.getVariable("isExportStatusCall");

if(pathSuffix === "" && isGetPage) {
    isValidResource = true;
} else if ( _isExportCall || _isExportStatusCall ) {
    isValidResource = true;
} else {
    for(var i = 0; i<supportedResources.length; i++){
        if(supportedResources[i] === primaryResource){
            isValidResource = true;
            break;
        }
    }
} 

context.setVariable("isValidResource", isValidResource);