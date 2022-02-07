var relative = JSON.parse(context.getVariable("relatedPatientResponse.content"));
context.setVariable("relative.patient.id", relative.patient.reference);