
 var rid = context.getVariable("request.queryparam.rid");
 
 
 function setErrorVariables(triggerError,code,detail,StatusCode){
    context.setVariable("triggerError",triggerError)
    context.setVariable("code",code)
    context.setVariable("detail",detail)
    context.setVariable("StatusCode",StatusCode)
    } 
 
 
 if(!rid){
    setErrorVariables(true,"Invalid_request","A mandatory query param is invalid/missing",400)
} else {
    context.setVariable("hashed_token", rid);
}