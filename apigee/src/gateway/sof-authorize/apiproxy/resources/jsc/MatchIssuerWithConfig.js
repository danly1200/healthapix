try {
	var iss = context.getVariable("jwt.SoF_JW_DecodeOIDCToken.claim.issuer");
	var config_iss = context.getVariable("FHIR_ISSUER");
    var matchFailed = false;
    if(config_iss != iss){
        matchFailed = true;
    }
    context.setVariable("issuermatch.failed", matchFailed);
}catch(e){
    context.setVariable("issuermatch.failed", true);
	context.setVariable("error","Invalid Request");
	context.setVariable("error_description","Error in Processing JWKS");
	context.setVariable("JS_Error", true);
	throw new Error("JS_Error");
}