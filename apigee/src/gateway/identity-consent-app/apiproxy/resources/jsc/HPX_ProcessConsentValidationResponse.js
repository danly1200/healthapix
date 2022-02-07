var isValid = context.getVariable("consent.valid");
var isExisting = context.getVariable("consent.exists");
var consentId = context.getVariable("consent.id");
var newScopes = JSON.parse(context.getVariable("consent.new_scopes"));
var existingScopes = context.getVariable("consent.existing_scopes");
var inputScopes = context.getVariable("scope");

context.setVariable("renderConsentScreen", !isValid);
if(isExisting){
    context.setVariable("newScopes", newScopes.join(" "));
}else{
    context.setVariable("newScopes", inputScopes);
    context.setVariable("consent.new_scopes", JSON.stringify(inputScopes.split(" ")));
}