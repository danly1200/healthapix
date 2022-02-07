try{
    var metadataResponse = JSON.parse(context.getVariable("metadataResponse.content")).rest[0].resource;
    var resourceArray = [];
    
    metadataResponse.forEach(resource => {
        resourceArray.push(resource.type);
    });
    
    context.setVariable("resourceList", JSON.stringify(resourceArray));
}catch(e){
    print("Error " + e);
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}