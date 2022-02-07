var existingScopes = JSON.parse(context.getVariable("existing_scopes"));
var newScopes = JSON.parse(context.getVariable("new_scopes"));
var inputScopes = context.getVariable("scope");

var combinedScopes = existingScopes.concat(newScopes);
if(combinedScopes.length > 0){
    context.setVariable("scope", combinedScopes.join(" "));
}