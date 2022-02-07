try{
var responseObject={};
var originalResponse = JSON.parse(context.getVariable('responseData.content')); 
var googleToken = "Bearer " + context.getVariable("IAM.access_token");
var targetBaseURI = context.getVariable("targetBaseURI")
if(originalResponse.entry){
    responseObject["id"]=originalResponse.entry[0].resource.id
    responseObject["resourceType"]=originalResponse.entry[0].resource.resourceType
    responseObject["name"]=originalResponse.entry[0].resource.name[0]
    if(!responseObject["name"]){
        responseObject["name"]=originalResponse.entry[0].resource.name
    }
    context.proxyResponse.content = JSON.stringify(responseObject);
    context.setVariable("response.header.Content-Type", "application/json")
    context.removeVariable("response.header.Authorization")
    context.removeVariable("response.header.Host")
    // context.setVariable("response.header.X-Google-Token", googleToken)
    // context.setVariable("response.header.X-Target-Base-URL", targetBaseURI)
    var paginationData = {}
    paginationData.googleToken = googleToken;
    paginationData.targetBaseURI = targetBaseURI;
    context.setVariable("paginationData", JSON.stringify(paginationData))
}else{
    context.setVariable("response.status.code", 404);
    context.setVariable("response.reason.phrase", "Resource not found");

} 

}catch(e){
    context.setVariable('JS_Error', true);
    throw new Error(e.message);
}




