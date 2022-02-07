//Javascript is required in this case because response type from fhir is not application/json and thus extract policies are not working
try{
    var deviceInfo  = context.getVariable("getDeviceInfoResponse.content");
    context.setVariable("device_id", JSON.parse(deviceInfo).entry[0].resource.id);
}catch(e){
    print(e.message);
    try{
        var newDeviceInfo = context.getVariable("createDeviceResponse.content");
        context.setVariable("device_id", JSON.parse(newDeviceInfo).id);
    }catch(error){
        print(error.message);
        context.setVariable("device_id", null);
    }
}