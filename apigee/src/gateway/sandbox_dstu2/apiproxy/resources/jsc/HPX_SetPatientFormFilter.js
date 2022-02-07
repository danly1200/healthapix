try {
	var resource = context.getVariable("resourceName");
	var validScopes = context.getVariable("validScopes");
	var validUserScopes = context.getVariable("validUserScopes");
	var validPatientScopes = context.getVariable("validPatientScopes");
	var resourceId = context.getVariable("resourceId");
	var fhirUserString = context.getVariable("accesstoken.fhirUser");
	var fhirUser = fhirUserString.split("/")[0];
	var patientId = context.getVariable("accesstoken.patient_id");
	if(patientId === "" && fhirUser == "Patient"){
	   patientId = fhirUserString.split("/")[1];
	}
	var allowedScopes = context.getVariable("accesstoken.scope");
	context.setVariable("fhirUser", fhirUser);
	var patientResource = JSON.parse(context
			.getVariable("dstu2.b2b.path.filter.map"))[resource];

	var validUserScopesLength = validUserScopes.length;
	var validUserScopeFound = false;
	var index = 0;
	for (; index < validUserScopesLength; index++) {
		if (allowedScopes.indexOf(validUserScopes[index]) != -1) {
			validUserScopeFound = true;
			break;
		}
	}

	var validPatientScopesLength = validPatientScopes.length;
	var validPatientScopeFound = false;
	var index = 0;
	for (; index < validPatientScopesLength; index++) {
		if (allowedScopes.indexOf(validPatientScopes[index]) != -1) {
			validPatientScopeFound = true;
			break;
		}
	}

	if (patientResource) {

		if (validPatientScopeFound) {
			if (patientId) {
				context.setVariable("request.formparam." + patientResource,
						patientId);
				//context.setVariable("request.queryparam._id", resourceId);
				context.setVariable("modifiedPathSuffix", "/" + resource);
			} else {
				throw "Patient ID is not available in the context";
			}
		} else if (validUserScopeFound) {
			if (patientId) {
				context.setVariable("request.queryparam." + patientResource,
						patientId);
				//context.setVariable("request.queryparam._id", resourceId);
				context.setVariable("modifiedPathSuffix", "/" + resource);
			} else {
				context.setVariable("modifiedPathSuffix", "/" + resource);
			}
		} else {
			throw "Insufficient scopes for given operation.";
		}

	} else {

		if (validUserScopeFound) {
				context.setVariable("modifiedPathSuffix", "/" + resource);
		} else {
			throw "Insufficient scopes for given operation.";
		}

	}

} catch (e) {
	context.setVariable("btoc.error", true);
	context.setVariable("btoc.message", e);
}