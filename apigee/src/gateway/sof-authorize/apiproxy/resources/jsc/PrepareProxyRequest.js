try {

	//Get common claims
	var claimSub =  context.getVariable("CLAIM_SUBJECT");
	var sub = context.getVariable(claimSub);
	
	var claimFhirUser = context.getVariable("CLAIM_FHIRUSER");
	var fhirUser = context.getVariable(claimFhirUser);
	
	var claimEmail = context.getVariable("CLAIM_EMAIL");
	var email = context.getVariable(claimEmail);
	
	var claimProfile = context.getVariable("CLAIM_PROFILE");
	var profile = context.getVariable(claimProfile);
	
	 if( fhirUser && fhirUser !== ""){ 
		sub = fhirUser;//required to check the header size
		context.setVariable("X-Authorization-Subject",fhirUser);
	} else if(profile && profile !== ""){
	    context.setVariable("X-Authorization-Subject",profile);
	} else if(sub && sub !== ""){
	    context.setVariable("X-Authorization-Subject",sub);
	} else if( email && email !== ""){ 
	    sub = email;//required to check the header size
		context.setVariable("X-Authorization-Subject",email);	
	} else {
	    sub = "";
	    context.setVariable("JS_Error",true);
		context.setVariable("error_name", "Insufficient information");
		context.setVariable("error_description","Subject not available");
	}
	context.setVariable("logSubject",sub);
	
	var configIss = context.getVariable("FHIR_ISSUER");
	var iss = "";
	
	var claimScope = context.getVariable("CLAIM_SCOPE");
	
	var claimPatient = context.getVariable("CLAIM_PATIENT");
	var patient = context.getVariable(claimPatient);
	
	if(patient && patient !== ""){
		context.setVariable("X-Authorization-Patient", patient);
	}
	context.setVariable("logPatient",patient);
	
	var configAud = context.getVariable("AUDIENCE");
	var aud = ""
	
	var timeRemaining = 0;
	
	var jti = "";
    
    var isJwtFlow = context.getVariable("isJwtFlow");
    
    if(isJwtFlow === true){
        var claimJti =   context.getVariable("CLAIM_TOKEN_ID");
        jti = context.getVariable(claimJti);
        
        if(jti && jti !== ""){
		context.setVariable("X-Authorization-Token-Id", jti);
	    }
        
        var claimSecondsRemaining = "seconds_remaining"
	    timeRemaining = context.getVariable(claimSecondsRemaining);
	    
	    var claimAud = context.getVariable("audience");
	    
	    var claimIss = context.getVariable("issuer");
	    
	    iss = configIss;//In case of issuer mismatch we will not reach here as there is a check before
	    
    } else {
        var active = context.getVariable("active");
    
        timeRemaining = context.getVariable("expiry");
        
        if(active === false){
	        context.setVariable("JS_Error",true);
            context.setVariable("error_name","Unauthenticated");
	        context.setVariable("error_description","Token expired");
        }
        
        iss = configIss;
        
    }
	

    //Process Scopes	
	var reqScopes = context.getVariable(claimScope);
	if(!reqScopes || reqScopes === ""){
	        context.setVariable("JS_Error",true);
			context.setVariable("error_name","Insufficient information");
			context.setVariable("error_description","Scope cannot be empty");
	} 
	var configScopes = context.getVariable("REMOVE_SCOPES");

	var filteredScopes= [];
	var splitReqScopes = [];
	var splitConfigScopes = [];

	if(reqScopes && reqScopes.indexOf(" ") > 0){
	     splitReqScopes = reqScopes.split(" ");
	} else if(reqScopes && reqScopes.indexOf(",") > 0) {
	     splitReqScopes = reqScopes.split(",");
	} else if(reqScopes && reqScopes.length > 0) {
	    splitReqScopes.push(reqScopes);
	}

	if(configScopes && configScopes.indexOf(" ") > 0) {
	     splitConfigScopes = configScopes.split(" ");
	} else if(configScopes && configScopes.indexOf(",") > 0) {
	     splitConfigScopes = configScopes.split(",");
	} else if(configScopes && configScopes.length > 0) {
	    splitConfigScopes.push(configScopes);
	}

	splitReqScopes.forEach(function(reqScope){
	    if(splitConfigScopes.indexOf(reqScope) === -1){
	        filteredScopes.push(reqScope);
	    }
	});

	//print("reqScopes",splitReqScopes);
	//print("configScopes",splitConfigScopes);
	//print("filteredScopes",filteredScopes);

	if(filteredScopes.length === 0){
	        context.setVariable("JS_Error",true);
			context.setVariable("error_name","Insufficient information");
			context.setVariable("error_description","Scope cannot be empty");
	} 
	
	context.setVariable("X-Authorization-Scope",filteredScopes.toString());
	context.setVariable("logScope",filteredScopes.toString());
	
	//Set X-Authorization-Issuer
	context.setVariable("X-Authorization-Issuer",iss);
	
	//Set X-Client-Id and check X-Client-Secret
	var reqXClientId = context.getVariable("req.X-Client-Id");
    
    var reqXClientSecret = context.getVariable("req.X-Client-Secret"); 
	
	if(context.getVariable("IS_CLIENT_REGISTRATION_ENABLED") === true) { 
	        //Client Id has been checked previously
			if (!reqXClientSecret || reqXClientSecret === "") {
				context.setVariable("warning", "Insufficient information");
				context.setVariable("error_description","Client Secret not provided");
			}
			context.setVariable("X-Client-Id", reqXClientId);
	}
	context.setVariable("logClientId",reqXClientId);
	
	//Check Scope header size
	var MAX_HEADER_SIZE_BYTES = context.getVariable("MAX_HEADER_SIZE_BYTES");
	var scopeLen = byteLength(filteredScopes.toString());
	print("scopeLen is " + scopeLen);
	if(scopeLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
	    context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum scope size exceeded");
	}
	
	var subLen = byteLength(sub);
	print("subLen is " + subLen);
	if( subLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
		context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum Subject size exceeded");
	}
	
	var issLen = byteLength(iss);
	print("issLen is " + issLen);
	if(issLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
		context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum Issuer size exceeded");
	}
	
	var patientLen = byteLength(patient);
	print("patientLen is " + patientLen);
	if(patient && patient !== "" && patientLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
	    context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum Patient size exceeded");
	}
	
	var jtiLen = byteLength(jti);
	print("jtiLen is " + jtiLen);
	if(jti && jti !== "" && jtiLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
	    context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum JTI size exceeded");
	}
	
	var clientSecretLen = byteLength(reqXClientSecret);
	print("clientSecretLen is " + clientSecretLen);
	if(clientSecretLen > MAX_HEADER_SIZE_BYTES){
	    context.setVariable("JS_Error",true);
	    context.setVariable("error_name","Invalid Request");
		context.setVariable("error_description","Maximum ClientSecret size exceeded");
	}
	
	//Set Token expiry duration
	var configTokenCacheTime = context.getVariable("MAX_TOKEN_CACHE_SECONDS")
	var CALCULATED_TOKEN_CACHE_EXPIRY = 0;
	if(timeRemaining > configTokenCacheTime){
		CALCULATED_TOKEN_CACHE_EXPIRY = configTokenCacheTime;
	} else {
		CALCULATED_TOKEN_CACHE_EXPIRY = Math.round(timeRemaining * 0.9);
	}
	context.setVariable("CALCULATED_TOKEN_CACHE_EXPIRY",CALCULATED_TOKEN_CACHE_EXPIRY);

}catch(e){
	context.setVariable("JS_Error",true);
	context.setVariable("error_name","Invalid Request");
	context.setVariable("error_description","Error in Processing SMART context headers");
}

function byteLength(s){
    if(s && s !== ""){
    var unescaped = unescape(encodeURI(s));
    return unescaped.length;
    } else {
        return 0;
    }
}