try {

	var iss = context.getVariable("jwt.HPX_DJW_DecodeOIDCToken.claim.issuer");
	var sub = context.getVariable("jwt.HPX_DJW_DecodeOIDCToken.claim.subject");
	var clientId = context.getVariable("verifyapikey.HPX_VAK_ValidateApiKey.client_id");
    var matchFailed = false;
    if(clientId != iss || clientId != sub){
        matchFailed = true;
    }
    context.setVariable("match.failed", matchFailed);
}catch(e){
    context.setVariable("match.failed", true);
	context.setVariable("error","Invalid Request");
	context.setVariable("error_description","Error in Processing JWKS");
	context.setVariable("JS_Error", true);
	throw new Error("JS_Error");
}