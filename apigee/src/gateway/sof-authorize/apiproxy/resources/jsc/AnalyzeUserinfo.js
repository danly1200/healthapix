try{
    
    var userInfoResContent = context.getVariable("userInfoResponse.content");
    var userInfoRes = JSON.parse(userInfoResContent);
    context.setVariable("userInfoRes",userInfoRes);
    //print(userInfoRes);
    var claimPatient = context.getVariable("CLAIM_PATIENT");
    var claimScope = context.getVariable("CLAIM_SCOPE");
    var claimFhirUser = context.getVariable("CLAIM_FHIRUSER");
    var claimEmail = context.getVariable("CLAIM_EMAIL");
    var claimSub = context.getVariable("CLAIM_SUBJECT"); 
    var claimTokenId = context.getVariable("CLAIM_TOKEN_ID");
    var claimProfile = context.getVariable("CLAIM_PROFILE");
    var claimAud = "aud";
    
    for (var key in userInfoRes) {
        //print("inside response json");
        if (userInfoRes.hasOwnProperty(key)) {
            //print(key + " - -> " + userInfoRes[key]);
            
            if(key === claimPatient){
                context.setVariable(claimPatient, userInfoRes[key]);
            }
            if(key === claimScope){
                context.setVariable(claimScope, userInfoRes[key]);
            }
            if(key === claimFhirUser){
                context.setVariable(claimFhirUser, userInfoRes[key]);
            }
            if(key === claimEmail){
                context.setVariable(claimEmail, userInfoRes[key]);
            }
            if(key === claimSub){
                context.setVariable(claimSub, userInfoRes[key]);
            }
            if(key === claimTokenId){
                context.setVariable(claimTokenId, userInfoRes[key]);
            }
            if(key === claimAud){
               context.setVariable(claimAud, userInfoRes[key]);
            }
            if(key === claimProfile){
               context.setVariable(claimProfile, userInfoRes[key]);
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
