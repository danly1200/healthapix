try {

	var jwksResponse = context.getVariable("jwkskeysetResponse.content");

	if(jwksResponse != null & jwksResponse != ""){
		context.setVariable("publicKeyJwks",jwksResponse);
	}

}catch(e){
	context.setVariable("error","Invalid Request");
	context.setVariable("error_description","Error in Processing JWKS");
	
}