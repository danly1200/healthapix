var userType = context.getVariable("usertype");
var patientList = context.getVariable("request.queryparam.selected_patients");
var patient_id = "";

if(userType != null & userType == "Patient"){
    
    patient_id = context.getVariable("patient_id");
    
} else if (userType == "Practitioner"  || userType == "RelatedPerson"){
    
    if(patientList != null) {
        if(patientList.search(",") != -1 ){
            patient_id = patientList.split(",")[0];    
        } else {
            patient_id = patientList;    
        }
    }
    
    
}
context.setVariable("patient_id",patient_id);