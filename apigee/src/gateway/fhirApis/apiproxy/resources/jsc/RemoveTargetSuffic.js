try {
    context.setVariable("target.copy.pathsuffix",false);
    
    var resourcesSplit = context.getVariable("proxy.pathsuffix").split("/");

    var resourceName = "";

    for (i=2;i<resourcesSplit.length;i++){
        resourceName = resourceName + resourcesSplit[i]+"/";
        print(resourceName);
    }
    print(resourceName);
    resourceName = resourceName.substr(0,resourceName.length - 1);
    print(resourceName);
    context.setVariable("resourceName", resourceName);
}catch(e){
    throw new Error("ResourceName could not be calculated");
}
