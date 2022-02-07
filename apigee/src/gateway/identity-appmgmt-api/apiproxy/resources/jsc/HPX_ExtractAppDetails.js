 var AppDetails = JSON.parse(context.getVariable("GetAppDetailsCalloutResponse.content"));
 var GetAppDetailsCalloutResponseStatusCode = context.getVariable("GetAppDetailsCalloutResponse.status.code")
 var requestedScopes = context.getVariable("requestedScopes");
 var approvedScopes = context.getVariable("GetAppDetailsCalloutResponse.approvedScopes");
 var proxyPathsuffix = context.getVariable("proxy.pathsuffix");
 var appAction = context.getVariable("appAction");
 var attributes = AppDetails.attributes;
 var updateAppAction = '';
 var reqArrayScopes = new Array()
 var userScopes = '';
 var patientScopes = '';
 var standardScopes = '';
 var _appType = context.getVariable("appType");
 if(!_appType || _appType === ""){
    attributes.forEach((arrObj) => {
        if(arrObj.name === "fhir_app_type"){
            _appType = arrObj.value;
        }
    });
 }
 
if(((GetAppDetailsCalloutResponseStatusCode === 200) && (proxyPathsuffix === "/approve")) && ((appAction === "create") || (appAction === "update")) && ( _appType === "smart" || _appType === "confidential" || _appType === "public" )){
     
     reqArrayScopes = requestedScopes.split(" ");
     
     for(i=0; i < reqArrayScopes.length; i++){
         
         if(reqArrayScopes[i].startsWith('user/')){
             userScopes += reqArrayScopes[i]+" "
         } else if(reqArrayScopes[i].startsWith('patient/')){
             patientScopes += reqArrayScopes[i]+" "
         } else {
             standardScopes += reqArrayScopes[i]+" "
         }
     }
    //print(JSON.stringify(attributes))
    objIndex = attributes.findIndex((obj => obj.name == "approved_scopes"));
    attributes[objIndex].value = requestedScopes;
    //attributes.push("approved_scopes",requestedScopes);
    
    objIndex = attributes.findIndex((obj => obj.name == "requested_scopes"));
    attributes[objIndex].value = "NA";
    
    if((userScopes) && (userScopes !== null) && (userScopes !== '')){
        objIndex = attributes.findIndex((obj => obj.name == "user_scopes"));
    attributes[objIndex].value = userScopes;
    }
    
    if((patientScopes) && (patientScopes !== null) && (patientScopes !== '')){
        objIndex = attributes.findIndex((obj => obj.name == "patient_scopes"));
    attributes[objIndex].value = patientScopes;
    }
    
    if((standardScopes) && (standardScopes !== null) && (standardScopes !== '')){
        objIndex = attributes.findIndex((obj => obj.name == "standard_scopes"));
    attributes[objIndex].value = standardScopes;
    }
    
    
    context.setVariable("updateAppAction","approve")
} if(((GetAppDetailsCalloutResponseStatusCode === 200) && (proxyPathsuffix === "/approve")) && ((appAction === "create") || (appAction === "update")) && _appType === "normal"){
    context.setVariable("updateAppAction","approve")
} else if((GetAppDetailsCalloutResponseStatusCode === 200) && (proxyPathsuffix === "/revoke") && (appAction === "update")){
    print(JSON.stringify(attributes))
    objIndex = attributes.findIndex((obj => obj.name == "approved_scopes"));
    print(objIndex)
    attributes[objIndex].value = "NA";
    
    objIndex = attributes.findIndex((obj => obj.name == "requested_scopes"));
    attributes[objIndex].value = requestedScopes;
    context.setVariable("updateAppAction","revoke")
} else if(GetAppDetailsCalloutResponseStatusCode !== 200) {
    setErrorVariables(true,"Unexpected_Error","The server encountered an internal error and was unable to complete your request",500)
}

context.setVariable("updateAppAttribute",JSON.stringify(attributes));

function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 

//print(JSON.stringify(attributes))

