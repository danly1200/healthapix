var id = context.getVariable("id");
var resourceType = context.getVariable("resourceType");
var displayName = JSON.parse(context.getVariable("name"));

context.setVariable("hpx.user.id", id);
context.setVariable("hpx.user.type", resourceType);
context.setVariable("hpx.user.name", displayName.given ? displayName.given.join(" ") : "Name unknown");
context.setVariable("request.queryparam.userType", resourceType.toLowerCase());
context.setVariable("request.queryparam.usertype", resourceType.toLowerCase());
context.setVariable("request.queryparam.firstName", displayName.given ? displayName.given.join(" ") : "Name unknown");

var fhirUser = "";

if(resourceType !== null && resourceType !== "")
{
    if(resourceType.toLowerCase() == "practitioner") {
	fhirUser = "Practitioner/"+id;
	context.setVariable("fhirUser", fhirUser);
    }
    
    if(resourceType.toLowerCase() == "relatedperson") {
    	fhirUser = "RelatedPerson/"+id;
    	context.setVariable("fhirUser", fhirUser);
    }
    
    if(resourceType.toLowerCase() == "patient") {
	fhirUser = "Patient/"+id;
	patienIDs = id;
	context.setVariable("patient_id", id);
	context.setVariable("fhirUser", fhirUser);
    }
    
}