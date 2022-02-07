 var verb = context.getVariable("request.verb");


var scope = null;
var grant_type = null;
var client_assertion_type = null;
var client_assertion = null;

if (verb != "POST") {
	context.setVariable("error_type", "bad_request");
	context.setVariable("error_variable", "response_type");
}

if (verb == "POST") {
	scope = context.getVariable("request.formparam.scope");
	grant_type = context.getVariable("request.formparam.grant_type");
	client_assertion_type = context.getVariable("request.formparam.client_assertion_type");
	client_assertion = context.getVariable("request.formparam.client_assertion");

}

if(isEmptyOrNull(scope)){
	context.setVariable("error_type", "bad_request");
	context.setVariable("error_variable", "Invalid scopes");
}else if(!systemScopesOnly(scope)){
	context.setVariable("error_type", "bad_request");
	context.setVariable("error_variable", "Invalid scopes");	
}else if(isEmptyOrNull(grant_type)){
	context.setVariable("error_type", "bad_request");
	context.setVariable("error_variable", "Invalid grant_type");
}else if(grant_type != "client_credentials"){
	context.setVariable("error_type", "bad_request");
	context.setVariable("error_variable", "Invalid grant_type");
}else if(isEmptyOrNull(client_assertion_type)){
	context.setVariable("error_type", "Invalid_request");
	context.setVariable("error_variable", "Invalid client_assertion_type");
}else if(client_assertion_type != "urn:ietf:params:oauth:client-assertion-type:jwt-bearer"){
	context.setVariable("error_type", "Invalid_request");
	context.setVariable("error_variable", "Invalid client_assertion_type");
}else if(isEmptyOrNull(client_assertion) ){
	context.setVariable("error_type", "Invalid_request");
	context.setVariable("error_variable", "Invalid client_assertion");
}
else {
	context.setVariable("scope", scope);
	context.setVariable("grant_type", grant_type);
	context.setVariable("client_assertion_type", client_assertion_type);
	context.setVariable("client_assertion", client_assertion);
}

function isEmptyOrNull(element){
	
if ((element == null) ||(element ==""))	
	return true;
else 
	return false;
}

function systemScopesOnly(receivedScopes){
	
	var onlySystemScopes = true;
	
	var scope  = [];
	
	scope = receivedScopes.split(" ");
	 
	var scopeSize = scope.length;
	
	for(i=0;i<scope.length;i++){
		
		var _scope = scope[i].toUpperCase();
		
		var splitScope = _scope.split("/");
		
		if(splitScope[0] != "SYSTEM"){
			
			onlySystemScopes = false;
			
			break;
		}
		
	}
	
	return onlySystemScopes;
	
}