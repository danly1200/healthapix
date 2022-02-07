 var hashed_token_value = JSON.parse(context.getVariable("hashed_token_value"));
  var proxyPathsuffix = context.getVariable("proxy.pathsuffix");
  
 if(hashed_token_value){
     if((proxyPathsuffix === '/revoke') && (hashed_token_value.app_action == 'create')){
         context.setVariable("skipCreateRevoke",true)
         context.setVariable("updateAppAction","revoke")
     } else {
         context.setVariable("skipCreateRevoke",false)
     }
     
     if((proxyPathsuffix === '/cancel') && (hashed_token_value.app_action == 'update')){
         context.setVariable("skipUpdateCancel",true)
     } else {
         context.setVariable("skipUpdateCancel",false);
     }
         
     context.setVariable("appName", hashed_token_value.app_name);
     context.setVariable("appId", hashed_token_value.app_id);
     context.setVariable("app_clientId", hashed_token_value.app_clientId);
     context.setVariable("appAction", hashed_token_value.app_action);
     context.setVariable("developerEmail", hashed_token_value.developer_email);
     context.setVariable("apiProduct", hashed_token_value.apiProduct);
     context.setVariable("requestedScopes", hashed_token_value.requested_scopes);
     context.setVariable("approvedScopes", hashed_token_value.approved_scopes);
     context.setVariable("xApiKey", hashed_token_value.x_api_key);
     context.setVariable("appType", hashed_token_value.app_type);
 } else {
     setErrorVariables(true,"Invalid_request","Invalid rid",400)
 }
 

 function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 