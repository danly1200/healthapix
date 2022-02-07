try {
    var verb = context.getVariable("request.verb");
    var primaryResource = context.getVariable("resourceName");
    var isSearch = context.getVariable("proxy.pathsuffix").split("/")[2] == "_search";
    var validScopeFound = false;
    var action = ".read";
    if (verb !== "GET" && !isSearch) var action = ".write";
    var scope = context.getVariable('scope');
    if(!scope || scope === ""){
       scope = context.getVariable("accesstoken.scope"); 
    }
    var scopes = [];
    if(scope && scope.indexOf(" ") > 0){
        scopes = scope.split(" ");
    } else if(scope && scope.indexOf(",") > 0){
        scopes = scope.split(",");
    }else if(scope){
        scopes = [scope];
    }
    
    var validScopes = ["patient/" + primaryResource + action, "user/" + primaryResource + action, "system/" + primaryResource + action, "patient/" + primaryResource + ".*", "user/" + primaryResource + ".*", "system/" + primaryResource + ".*", "patient/*" + action, "user/*" + action, "system/*" + action, "user/*.*", "patient/*.*", "system/*.*"];
    
    var validUserScopes = [ "user/" + primaryResource + action, "user/" + primaryResource + ".*", "user/*" + action, "user/*.*"];
    var validPatientScopes = [ "patient/" + primaryResource + action, "patient/" + primaryResource + ".*", "patient/*" + action, "patient/*.*"];
    var validSystemScopes = [ "system/" + primaryResource + action, "system/" + primaryResource + ".*", "system/*" + action, "system/*.*"];
        
    context.setVariable("validScopes",validScopes);
    context.setVariable("validUserScopes",validUserScopes);
    context.setVariable("validPatientScopes",validPatientScopes);
    context.setVariable("validSystemScopes",validSystemScopes);
    
    var validScopesLength = validScopes.length;
    var index = 0;
    for (; index < validScopesLength; index++) {
        if (scopes.indexOf(validScopes[index]) != -1) {
            validScopeFound = true;
            break;
        }
    }
    context.setVariable("validScopeFound", validScopeFound);
    print("verb:",verb);
    print("primaryResource:",primaryResource);
    print("isSearch:",isSearch);
    print("action:",action);
    print("scopes:",scopes);
    print("validScopes:",validScopes);
    print("validScopeFound:",validScopeFound);
    print("validUserScopes:",validUserScopes);
    print("validPatientScopes:",validPatientScopes);
    print("validSystemScopes:",validSystemScopes);
    
} catch (Error) {
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}