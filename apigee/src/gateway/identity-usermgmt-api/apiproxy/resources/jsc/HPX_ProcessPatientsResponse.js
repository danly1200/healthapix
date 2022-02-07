var responseData = context.getVariable("patientsResponse.content");
responseData = JSON.parse(responseData);
var formattedData = [];
if(responseData.entry.length !== 0){
    responseData.entry.forEach(function(patient){
        var patientData = {};
        patientData.id = patient.resource.id;
        patientData.birthDate = patientData.birthDate ? patient.resource.birthDate : "";
        patientData.gender = patient.resource.gender? patient.resource.gender: "";
        patientData.name = patient.resource.name && patient.resource.name[0].given? patient.resource.name[0].given.join(" ") : "";
        formattedData.push(patientData);
    });
    
}

var currentPageURI = context.getVariable("servicecallout.requesturi");
currentPageURI = currentPageURI? currentPageURI.substring(currentPageURI.lastIndexOf('/')+1): "";
var nextPageURI = '';
var previousPageURI = context.getVariable("hpx.pagination.currentPageURI") || '';
var googleToken = context.getVariable("hpx.pagination.googleToken");
var targetBaseURI = context.getVariable("hpx.pagination.targetBaseURI");

responseData.link.forEach(function(link){
    if(link.relation === "next"){
        nextPageURI = link.url.substring(link.url.lastIndexOf('/')+1);
    }
});

var paginationData = {};
paginationData.currentPageURI = currentPageURI;
paginationData.nextPageURI = nextPageURI;
paginationData.previousPageURI = previousPageURI;
paginationData.googleToken = googleToken;
paginationData.targetBaseURI = targetBaseURI;

context.setVariable("paginationData", JSON.stringify(paginationData));
context.setVariable("sessionid", context.getVariable("request.queryparam.sessionId"));
context.setVariable("response.content", JSON.stringify(formattedData));
context.setVariable("response.header.Content-Type", "application/json");