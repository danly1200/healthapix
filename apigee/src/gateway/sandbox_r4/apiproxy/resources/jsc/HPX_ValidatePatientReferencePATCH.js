var inputPayload = request.content.asJSON;
var configMaps = context.getVariable("r4.b2c.payload.filter.map");
configMaps = JSON.parse(configMaps);
var contextPatient = context.getVariable("accesstoken.patient_id");
var resourceType = context.getVariable("resourceName");

var maps = configMaps[resourceType];

var isValidReference = false;

var maps = configMaps[resourceType];

if (maps) {
    print("maps found")
    if (Array.isArray(inputPayload) && inputPayload.length > 0) {
        isValidReference = inputPayload.every(function (operation) {
            var mapStatus = maps.every(function (map) {
                if (operation.op === "delete") return false;
                else if (validateReference(map, operation, contextPatient) === 0) return false;
                else return true;
            });
            //mapStatus == false means validation was successful
            //mapStatus == true means validation was not successful
            print("mapStatus: " + mapStatus)
            if (!mapStatus) return false;
            else return true;
        });
    }else{
        isValidReference = true;
    }
}

if (!isValidReference) {
    context.setVariable("btoc.error", true);
    context.setVariable("btoc.message", "Cannot overwrite patient references or delete fields");
}

function validateReference(map, data, contextPatient) {
    switch (map.type) {
        case 'direct':
            var inputLocation = data.path.replace(/\//g, ".").substring(1);
            //remove trailing dot if any
            inputLocation = removeLeadingAndTrailingDots(inputLocation);
            print("inputLocation: " + inputLocation)
            print("mapLocation: " + map.location)
            var actualLocation = map.location.replace(inputLocation, "");
            actualLocation = removeLeadingAndTrailingDots(actualLocation);
            print("actualLocation: " + actualLocation)
            var inputReference = getInputReference(actualLocation, data);
            print("inputReference: " + inputReference)
            if (inputReference) {
                var splittedReference = inputReference.split("/");
                if (splittedReference.length == 2) {
                    return (splittedReference[0] === "Patient" && splittedReference[1] === contextPatient) * 1;
                } else if (splittedReference.length == 1) {
                    return (splittedReference === contextPatient) * 1;
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
            break;
        case 'collection':
            var targetIndex = data.path.match(/\d+/) ? data.path.match(/\d+/)[0] : undefined;
            if(!targetIndex) return -1;
            var splittedReference = data.path.split(targetIndex);
            if (splittedReference.length != 2) return 0;
            inputLocation = splittedReference[0].replace(/\//g, ".").substring(1);
            inputLocation = removeLeadingAndTrailingDots(inputLocation);
            var inputCollectionLocation = splittedReference[1].replace(/\//g, ".").substring(1);
            inputCollectionLocation = removeLeadingAndTrailingDots(inputCollectionLocation);
            //if the part of the path before index is matching with location in the map, it's a concerned element
            var isConcernedElement = !(map.location.replace(inputLocation, "").trim() && map.collectionLocation.replace(inputCollectionLocation), "".trim());
            //if concerned element no patient references can be added, but existing patient references can be overwritten by Location, Practitioner etc.
            if (isConcernedElement) {
                if (data.value.match(/Patient/)) return 0;
                else return 1;
            } else return 1;
            break;
        case 'chained':
            var inputLocation = data.path.replace(/\//g, ".").substring(1);
            //remove trailing dot if any
            inputLocation = removeLeadingAndTrailingDots(inputLocation)
            print("inputLocation: " + inputLocation)
            print("mapLocation: " + map.location)
            var actualLocation = map.location.replace(inputLocation, "");
            actualLocation = removeLeadingAndTrailingDots(actualLocation);
            print("actualLocation: " + actualLocation)
            var inputReference = getInputReference(actualLocation, data);
            print("inputReference: " + inputReference)
            if (inputReference) {
                var fhirStoreUrl = context.getVariable("google_fhirstore_url");
                var googleToken = context.getVariable("IAM.access_token");
                var fhirResourceUrl = "https://healthcare.googleapis.com" + fhirStoreUrl + "/" + inputReference;
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
                        var parentReference = parentMaps.every(function (parentMap) {
                            var reference = jsonPath(data, "$." + map.location.trim());
                            if (reference.length === 1) {
                                var formattedInput = reference[0].split("/");
                                if (formattedInput.length === 1) {
                                    return formattedInput[0] === contextPatient;
                                } else if (formattedInput.length === 2) {
                                    return formattedInput[0] === "Patient" && formattedInput[1] === contextPatient;
                                }
                                else {
                                    return false;
                                }
                            } else {
                                return false;
                            }
                        });
                        if (parentReference) return 1;
                        else return 0;
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return -1;
            }
            break;
        default:
            return 0;
    }
}

function getInputReference(actualLocation, data) {
    if (actualLocation.trim().length > 0) {
        var inputReference = jsonPath(data.value, actualLocation);
        if (inputReference && inputReference.length === 1) {
            return inputReference[0];
        } else {
            return null;
        }
    } else {
        var inputReference = data.value;
        return inputReference;
    }
}

function removeLeadingAndTrailingDots(str) {
    str = str.substring(0, 1) === "." ? str.substring(1) : str;
    str = str.substring(str.length - 1) === "." ? str.substring(0, str.length - 1) : str;
    return str;
}