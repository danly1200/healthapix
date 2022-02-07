var ContentTypeHead = context.getVariable("request.header.Content-Type");
var AuthorizationHead = context.getVariable("request.header.Authorization");
var AcceptHeader = context.getVariable("request.header.Accept");
var token = context.getVariable("request.formparam.token");


function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 
if ((ContentTypeHead === null) || (!ContentTypeHead) || (ContentTypeHead === "") || (ContentTypeHead != "application/x-www-form-urlencoded")) {
    setErrorVariables(true,"Invalid_request","The requested content type is invalid",400)
}
else if ((!AuthorizationHead) || (AuthorizationHead ==="") || (AuthorizationHead === "Basic ") || (AuthorizationHead === null)) {
    setErrorVariables(true,"Unauthorized","Invalid/missing Authorization header",401)
}
else if ((!token) || (token ==="") || (token === null)) {
    setErrorVariables(true,"Invalid_request","Invalid/missing token",400)
}
else if ((AcceptHeader === null) || (!AcceptHeader) || (AcceptHeader === "") || (AcceptHeader != "application/json")) {
    setErrorVariables(true,"Invalid_request","The requested Accept header type is invalid",400)
}
else {
    context.setVariable("triggerError", false);
}
