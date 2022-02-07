try {
var audienceUri = "https://"+context.getVariable("request.header.host")+context.getVariable("proxy.basepath")+context.getVariable("proxy.pathsuffix");
context.setVariable("audienceUri", audienceUri);
var jwks_from_config = context.getVariable("verifyapikey.HPX_VAK_ValidateApiKey.jwks_uri");
var jwks_from_jku = context.getVariable("jwt.HPX_DJW_DecodeOIDCToken.header.jku");    
if(jwks_from_config != jwks_from_jku){
    context.setVariable("jwks_uri", jwks_from_jku);
}
}catch(Error){
    context.setVariable("JS_Error",true);
    throw new Error("JS_Error");
}