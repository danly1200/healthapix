var fhirUser = context.getVariable("request.queryparam.fhirUser");
if(fhirUser.split("/")[0] == "Patient"){
    context.setVariable("request.queryparam.patient", fhirUser.replace('Patient/',''));
}
context.setVariable("client_id", context.getVariable("request.queryparam.client_id"));