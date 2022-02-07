try {

//Get ALLOWED_PATH_PREFIX, Get proxy.pathsuffix and just match

var proxyPathSuffix = context.getVariable("proxy.pathsuffix");
var ALLOWED_PATH_PREFIX = context.getVariable("ALLOWED_PATH_PREFIX");

var reqApiVersion = context.getVariable("urirequest.apiVersion");
var reqProject = context.getVariable("urirequest.project");
var reqLocation = context.getVariable("urirequest.location");
var reqDataset = context.getVariable("urirequest.dataset");
var reqFhirstore = context.getVariable("urirequest.fhirStore");

var reqPath = "/"+reqApiVersion+"/projects/"+reqProject+"/locations/"+reqLocation+"/datasets/"+reqDataset+"/fhirStores/"+reqFhirstore;

if(!ALLOWED_PATH_PREFIX || ALLOWED_PATH_PREFIX === ""){
    throw new Error("Config not found - Path prefix");
}

var allowedPrefixes = ALLOWED_PATH_PREFIX.split(",");
var mismatchCount = 0;

print("proxy path suffix - " + proxyPathSuffix);
print("matching - " + reqPath);
allowedPrefixes.forEach(function(allowedPrefix){
    if(reqPath === allowedPrefix){
        print("proxy path suffix matches with configuration.");
    } else {
        print("proxy path suffix does not match with configuration.");
        mismatchCount++;
    }
});
if(allowedPrefixes.length == mismatchCount){
        context.setVariable("invalidFhirStoreInfo", true);
        context.setVariable("error_name", "Invalid request");
        context.setVariable("error_description", "Request path suffix does not match configured values");
}

}catch(Error){
    print("Error " + Error);
    context.setVariable('JS_Error', true);  
    context.setVariable("error_name", "Invalid request");
    context.setVariable("error_description", "Request path suffix does not match configured values");

}