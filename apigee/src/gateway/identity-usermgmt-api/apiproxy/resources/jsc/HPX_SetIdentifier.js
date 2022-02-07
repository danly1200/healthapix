try{
var queryParamsList = request.queryParams;
for(var queryParam in queryParamsList){
        if (queryParam.toLowerCase() == "identifier") {
                   var identifierValue = context.getVariable("request.queryparam." + queryParam);
                   context.setVariable("emailIdentifier" , identifierValue);

        }
        if (queryParam.toLowerCase() == "sessionid") {
                   var identifierValue = context.getVariable("request.queryparam." + queryParam);
                   context.setVariable("sessionid" , identifierValue);

        }
}
}catch(e){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}


