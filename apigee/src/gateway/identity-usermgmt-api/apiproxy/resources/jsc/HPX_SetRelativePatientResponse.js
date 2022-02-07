var relativePatient=JSON.parse(context.getVariable("relativePatientResponse.content"));
var formattedData = [];
var patientData = {};
patientData.id = relativePatient.id;
patientData.birthDate = relativePatient.birthDate ? relativePatient.birthDate : "";
patientData.gender = relativePatient.gender? relativePatient.gender: "";
patientData.name = relativePatient.name && relativePatient.name[0].given? relativePatient.name[0].given.join(" ") : "";
formattedData.push(patientData);
context.setVariable("response.content", JSON.stringify(formattedData));
context.setVariable("response.header.content-type", "application/json")