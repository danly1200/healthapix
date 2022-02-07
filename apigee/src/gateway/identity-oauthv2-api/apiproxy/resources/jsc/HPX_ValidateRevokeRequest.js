var ContentTypeHead = context.getVariable("request.header.Content-Type");
var AuthorizationHead = context.getVariable("request.header.Authorization");
var token = context.getVariable("request.formparam.token");
context.setVariable("Accesstoken", token);

function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 
    
if ((ContentTypeHead === null) || (!ContentTypeHead) || (ContentTypeHead === "") || (ContentTypeHead !== "application/x-www-form-urlencoded")) {

    setErrorVariables(true,"Unsupported_Media_Type","The requested content type is invalid",415)
}
else if ((!AuthorizationHead) || (AuthorizationHead ==="") || (AuthorizationHead === "Basic ") || (AuthorizationHead === null)) {
    setErrorVariables(true,"Unauthorized","Invalid/missing Authorization header",401)
}
else if ((!token) || (token ==="") || (token === null)) {
    setErrorVariables(true,"invalid_request","Invalid/missing token",400)
}
else {
    context.setVariable("triggerError", false);
}