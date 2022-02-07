var inputPayload = request.content.asJSON;
var configMaps = context.getVariable("dstu2.b2c.payload.filter.map");
configMaps = JSON.parse(configMaps);
var contextPatient = context.getVariable("accesstoken.patient_id");
var resourceType = context.getVariable("resourceName");

var maps = configMaps[resourceType];

var isValidReference = false;

if (maps) {
    print("Map found")
    maps.every(function (map) {
        try {
            isValidReference = validateReference(map, inputPayload, contextPatient);
        } catch (e) {
            print(e.message)
        }
        if (isValidReference) {
            return false;
        }
        return true;
    });
}

if (!isValidReference) {
    context.setVariable("btoc.error", true);
    context.setVariable("btoc.message", "Could not detect a reference to the patient in context");
}

function validateReference(map, data, contextPatient) {
    var valid = false;
    switch (map.type) {
        case 'direct':
            //directly accessible inside the payload
            print("In direct case.")
            var reference = jsonPath(data, "$." + map.location.trim());
            if (reference.length === 1) {
                var formattedInput = reference[0].split("/");
                if (formattedInput.length === 1) {
                    valid = formattedInput[0] === contextPatient;
                } else if (formattedInput.length === 2) {
                    valid = formattedInput[0] === "Patient" && formattedInput[1] === contextPatient;
                }
                else {
                    valid = false;
                    print("Invalid as direct, indirect or type did not match")
                }
            } else {
                print("Json path was not resolved")
                valid = false;
            }
            break;
        case 'collection':
            //directly accessible inside the payload but an array
            print("In collection case.");
            var references = jsonPath(data, "$." + map.location.trim());
            if (references.length === 1) {
                references = references[0];
                if (references.length > 0) {
                    references.every(function (reference) {
                        input = jsonPath(reference, "$." + map.collectionLocation.trim());
                        if (input.length === 1) {
                            var formattedInput = input[0].split("/");
                            if (formattedInput.length === 2) {
                                valid = formattedInput[0] === "Patient" && formattedInput[1] === contextPatient;
                                if (valid) {
                                    return false;
                                } else {
                                    return true;
                                }
                            } else {
                                valid = false;
                                print("Invalid as direct, indirect or type did not match")
                            }
                        } else {
                            valid = false;
                            print("Json path was not resolved")
                        }
                    });
                }
                else {
                    valid = false;
                    print("Empty array")
                }
            } else {
                valid = false;
                print("Json path was not resolved")
            }
            break;
        case 'chained':
            //cannot be accessed directly inside payload. should callout to parent resource and validate
            print("In chained case.")
            reference = jsonPath(data, "$." + map.location.trim());
            if (reference.length === 1) {
                var fhirStoreUrl = context.getVariable("google_fhirstore_url");
                var googleToken = context.getVariable("IAM.access_token");
                var fhirResourceUrl = "https://healthcare.googleapis.com" + fhirStoreUrl + "/" + reference[0];
                print(fhirResourceUrl);
                var fhirRequestHeaders = {
                    'Authorization': 'Bearer ' + googleToken,
                    'Accept': 'application/fhir+json'
                };
                var fhirRequest = new Request(fhirResourceUrl, "GET", fhirRequestHeaders);
                var fhirResponse = httpClient.send(fhirRequest);
                fhirResponse.waitForComplete();
                if (fhirResponse.isSuccess()) {
                    var parentResponse = JSON.parse(fhirResponse.getResponse().content);
                    var parentResourceType = parentResponse.resourceType;
                    var parentMaps = configMaps[parentResourceType];
                    if (parentMaps) {
                        parentMaps.every(function (parentMap) {
                            valid = validateReference(parentMap, parentResponse, contextPatient);
                            if (valid) {
                                return false;
                            }
                            return true;
                        });
                    } else {
                        valid = false;
                    }
                } else {
                    valid = false;
                }
            }
            break;
        case 'nestedcollection':
            //directly accessible inside the payload but inside an array in another array
            //To be implemented
            valid = false;
            break;
        case 'collectionchained':
            //cannot be accessed directly. should callout to related resources in an array
            //To be implemented
            valid = false;
            break;
        default:
            print("Unknown reference type in map")
            valid = false;
    }
    return valid;
}