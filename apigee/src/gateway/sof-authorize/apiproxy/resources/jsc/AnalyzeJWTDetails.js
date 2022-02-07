 try {

	//Remove Scopes
	
	//test values
	//var reqScopes = "openid ga4gh_passport_v1 patient/*.read patient/*.write";
	//var configScopes = "openid,profile,email";
	
	var logicExecuted = "";
	var rsExecuted = context.getVariable("jwt.SoF_VerifyRSOIDCToken.valid");
	var esExecuted = context.getVariable("jwt.SoF_VerifyESOIDCToken.valid");
	
	if( rsExecuted === "true"){
		logicExecuted = "jwt.SoF_VerifyRSOIDCToken.";
	}
	
	if( esExecuted === "true"){
		logicExecuted = "jwt.SoF_VerifyESOIDCToken.";
	}

	//Process patient, sub, iss, aud, jti, fhirUser, email from JWT token	
	var claimStr = "claim.";
	var claimScope = context.getVariable("CLAIM_SCOPE");
	var reqScopes = context.getVariable(logicExecuted + claimStr + claimScope);
	context.setVariable(claimScope,reqScopes);

	var claimPatient = context.getVariable("CLAIM_PATIENT");
	var patient = context.getVariable(logicExecuted + claimStr  + claimPatient);
	context.setVariable(claimPatient,patient);
	
	var claimSub =  context.getVariable("CLAIM_SUBJECT");
	var sub = context.getVariable(logicExecuted + claimStr  + claimSub);
	context.setVariable(claimSub, sub);
		
	var claimIss = context.getVariable(logicExecuted + claimStr  + "issuer");
	context.setVariable("issuer", claimIss);
	
	var claimAud = context.getVariable(logicExecuted + claimStr  + "audience");
	context.setVariable("audience", claimAud);
	
	var claimJti =   context.getVariable("CLAIM_TOKEN_ID");
	var jti = context.getVariable(logicExecuted + claimStr  + claimJti);
	context.setVariable(claimJti, jti);
	
	
	var claimFhirUser = context.getVariable("CLAIM_FHIRUSER");
	var fhirUser = context.getVariable(logicExecuted + claimStr  + claimFhirUser);
	context.setVariable(claimFhirUser, fhirUser);
	
	var claimEmail = context.getVariable("CLAIM_EMAIL");
	var email = context.getVariable(logicExecuted + claimStr  + claimEmail);
	context.setVariable(claimEmail, email);
	
	var claimSecondsRemaining = "seconds_remaining"
	var timeRemaining = context.getVariable(logicExecuted + claimSecondsRemaining);
	context.setVariable(claimSecondsRemaining, timeRemaining);

}catch(e){
	context.setVariable("error","Invalid Request");
	context.setVariable("error_description","Error in Processing JWKS");
	
}