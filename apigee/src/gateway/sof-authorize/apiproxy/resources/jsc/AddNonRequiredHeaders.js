headerXClientId = context.getVariable("X-Client-Id");
headerPatient = context.getVariable("X-Authorization-Patient");
headerXTokenId = context.getVariable("X-Authorization-Token-Id");
 
if(headerXClientId && headerXClientId !== ""){
    context.setVariable("request.header.X-Client-Id",headerXClientId);
}

if(headerPatient && headerPatient !== ""){
    context.setVariable("request.header.X-Authorization-Patient",headerPatient);
}

if(headerXTokenId && headerXTokenId !== ""){
    context.setVariable("request.header.X-Authorization-Token-Id",headerXTokenId);
}