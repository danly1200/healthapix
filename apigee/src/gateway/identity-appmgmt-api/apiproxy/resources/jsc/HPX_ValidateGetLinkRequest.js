//var requested_scopes = JAON.parse(context.getVariable("request.content");
var x_api_keyHead = context.getVariable("request.header.x-api-key");
var AcceptHeader = context.getVariable("request.header.Accept");
var requested_scopes = context.proxyRequest.body.asJSON.requested_scopes;
var approved_scopes = context.proxyRequest.body.asJSON.approved_scopes; 
var app_action = context.proxyRequest.body.asJSON.app_action; 
var app_name = context.proxyRequest.body.asJSON.app_name; 
var developer_email = context.proxyRequest.body.asJSON.developer_email; 
var apiProduct = context.proxyRequest.body.asJSON.apiProduct; 
var app_clientId = context.proxyRequest.body.asJSON.app_clientId; 
var app_type = context.proxyRequest.body.asJSON.app_type; 
var request_host = context.getVariable("request.header.Host"); 
context.setVariable("request_host", request_host);

function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 
if ((!x_api_keyHead) || (x_api_keyHead ==="") || (x_api_keyHead === null)) {
    setErrorVariables(true,"Unauthorized","Invalid/missing x-api-key header",401)
}
else if ((app_type === "smart") && ((!requested_scopes) || (!app_name) || (!app_clientId) || (!developer_email) || (!apiProduct) || (!app_action)) || ((app_action != "create") && (app_action != "update"))) {
    setErrorVariables(true,"Invalid_request","A mandatory field, required for the API, is invalid/missing in the payload",400)
}
else if ((app_type === "normal") && ((!app_name) || (!app_clientId) || (!developer_email) || (!apiProduct) || (!app_action)) || (app_action != "create")) {
    setErrorVariables(true,"Invalid_request","A mandatory field, required for the API, is invalid/missing in the payload",400)
}
else if ((AcceptHeader === null) || (!AcceptHeader) || (AcceptHeader === "") || (AcceptHeader != "application/json")) {
    setErrorVariables(true,"Invalid_request","The requested Accept header type is invalid",400)
}
else {
    context.setVariable("triggerError", false);
    context.setVariable("app_name", app_name);
    context.setVariable("developer_email", developer_email);
    context.setVariable("apiProduct", apiProduct);
    context.setVariable("app_action", app_action);
    context.setVariable("app_type", app_type);
   
    if(app_type === "smart"){
        context.setVariable("requested_scopes", requested_scopes);
       if(approved_scopes){
         context.setVariable("approved_scopes", approved_scopes);
       } 
    }
    
}
