var grantType = context.getVariable("request.queryparam.grant_type");
if(!grantType){
    grantType = context.getVariable("request.formparam.grant_type");
}

var _scopes = context.getVariable("request.queryparam.scope");
if(!_scopes || _scopes === ""){
    _scopes = context.getVariable("request.formparam.scope");
}


if(grantType){
    if(!(grantType === "client_credentials" || grantType === "authorization_code" || grantType === "refresh_token")){
        context.setVariable("invalid", true);
        context.setVariable("error_description", "unsupported grant_type");
    }
    if(grantType === "client_credentials" && (!_scopes || _scopes === "") ){
        context.setVariable("invalid", true);
        context.setVariable("error_description", "scopes are required");
    }
    
}else{
    context.setVariable("invalid", true);
    context.setVariable("error_description", "grant_type is required");
}