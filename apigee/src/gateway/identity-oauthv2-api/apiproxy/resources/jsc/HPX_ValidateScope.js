var credentialsJson = context.getVariable("apiProductJSON");
credentials = new String(credentialsJson);
var scope = context.getVariable("scope");
scope = decodeURIComponent(scope)
var error = "";
var receivedScope = "";
var receivedScopes = scope.split(" ");
try{
    jsonObjectCredentials = JSON.parse(credentialsJson);
}
catch(e){
    error="true";
    context.setVariable("error_type", "Invalid_request");
    context.setVariable("error_variable", "scope");
}

if ((error == null || error == "") ){
    jsonArray = jsonObjectCredentials.ApiProduct.Scopes.Scope;
    appScopes = new Array();
    if (jsonArray.constructor == Array) {
        
        appScopes=jsonArray;
    } else {
        if ( jsonArray!= null && jsonArray != ""  && JSON.stringify(jsonArray) != "{}"){
            appScopes.push(jsonArray)
            context.setVariable("jsonArray", JSON.stringify(jsonArray));
        }}
    print("appScopes: "+appScopes.constructor)
    print("receivedScopes: "+receivedScopes.constructor)
    var invalidScopes = [];
    receivedScopes.forEach(function(receivedScope){
        if(appScopes.indexOf(receivedScope) === -1){
            invalidScopes.push(receivedScope);
        }
    });
    if(invalidScopes.length>0){
        context.setVariable("error_type", "invalid_request");
        context.setVariable("error_variable", "scopes: " + invalidScopes.join(", "));
    }
}