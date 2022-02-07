var consentResponse = response.content.asJSON;
if(consentResponse.total>0){
    var inputScopes = context.getVariable("scopes");
    var consentId = consentResponse.entry[0].resource.id;
    var consentWindowStart = Date.parse(consentResponse.entry[0].resource.period.start);
    var consentWindowEnd = Date.parse(consentResponse.entry[0].resource.period.end);
    var currentDateTime = Date.now();
    if(consentWindowStart < currentDateTime){
        if(consentWindowEnd > currentDateTime){
            var consentedScopes = consentResponse.entry[0].resource.except[0].class.filter(function(itm){ return itm.system.match(/scope/)});
            inputScopes = inputScopes.split(" ");
            consentedScopes = JSON.parse(consentedScopes[0].code.replace(/ /g,''));
            var newScopes = inputScopes.filter(x => consentedScopes.indexOf(x) == -1);
            var scopeMatched = !newScopes.length;
            response.content = JSON.stringify({valid: scopeMatched, exists: true, id: consentId, new_scopes: newScopes, existing_scopes: consentedScopes});
        }else{
            response.content = JSON.stringify({valid: false, exists: false, id: null, new_scopes: [], existing_scopes: []});
        }
    }else{
        response.content = JSON.stringify({valid: false, exists: false, id: null, new_scopes: [], existing_scopes: []});
    }
}else{
    response.content = JSON.stringify({valid: false, exists: false, id: null, new_scopes: [], existing_scopes: []});
}