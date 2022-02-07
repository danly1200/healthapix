var consentResponse = response.content.asJSON;
if(consentResponse.total>0){
    var inputScopes = context.getVariable("scopes");
    var consentId = consentResponse.entry[0].resource.id;
    var consentedScopes = consentResponse.entry[0].resource.term[0].text;
    inputScopes = inputScopes.split(" ");
    consentedScopes = JSON.parse(consentedScopes.replace(/ /g,''));
    var newScopes = inputScopes.filter(x => consentedScopes.indexOf(x) == -1);
    var scopeMatched = !newScopes.length;
    response.content = JSON.stringify({valid: scopeMatched, exists: true, id: consentId, new_scopes: newScopes, existing_scopes: consentedScopes});
}else{
    response.content = JSON.stringify({valid: false, exists: false, id: null, new_scopes: [], existing_scopes: []});
}