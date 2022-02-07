try{
    
    var contextResVar = context.getVariable("introspectionResponse.content");
    var instrospectResponse = JSON.parse(contextResVar);
    context.setVariable("instrospectResponse",instrospectResponse);
    //print(instrospectResponse);
    var claimPatient = context.getVariable("CLAIM_PATIENT");
    var claimScope = context.getVariable("CLAIM_SCOPE");
    var claimFhirUser = context.getVariable("CLAIM_FHIRUSER");
    var claimEmail = context.getVariable("CLAIM_EMAIL");
    var claimSub = context.getVariable("CLAIM_SUBJECT"); 
    var claimTokenId = context.getVariable("CLAIM_TOKEN_ID");
    var claimProfile = context.getVariable("CLAIM_PROFILE");
    var claimAud = "aud";
    
    for (var key in instrospectResponse) {
        //print("inside response json");
        if (instrospectResponse.hasOwnProperty(key)) {
            //print(key + " - -> " + instrospectResponse[key]);
            
            if(key === claimPatient){
                context.setVariable(claimPatient, instrospectResponse[key]);
            }
            if(key === claimScope){
                context.setVariable(claimScope, instrospectResponse[key]);
            }
            if(key === claimFhirUser){
                context.setVariable(claimFhirUser, instrospectResponse[key]);
            }
            if(key === claimEmail){
                context.setVariable(claimEmail, instrospectResponse[key]);
            }
            if(key === claimSub){
                context.setVariable(claimSub, instrospectResponse[key]);
            }
            if(key === claimTokenId){
                context.setVariable(claimTokenId, instrospectResponse[key]);
            }
            if(key === claimAud){
               context.setVariable(claimAud, instrospectResponse[key]);
            }
            if(key === claimProfile){
               context.setVariable(claimProfile, instrospectResponse[key]);
            }
            if(key === "active"){
               context.setVariable("active", instrospectResponse[key]);
            }
            if(key === "expires_in"){
               context.setVariable("expiry", instrospectResponse[key]);
            }
        }
    }
    
    var sub = context.getVariable(claimSub);
    var fhirUser = context.getVariable(claimFhirUser);
    var email = context.getVariable(claimEmail);
    var scope = context.getVariable(claimScope);
    var profile = context.getVariable(claimProfile);
    
    if(( (!sub || sub === "") && (!fhirUser || fhirUser === "") && (!email || email === "") && (!profile || profile === "")) || (!scope || scope === "") ){
        context.setVariable('insufficientData', true);
    }
    
}catch(Error){
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}