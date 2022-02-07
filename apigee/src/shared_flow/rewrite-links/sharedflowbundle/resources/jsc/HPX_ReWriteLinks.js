try{
    var responseData = response.content;
    
    // Extract required flow variables to form 'targetPath' url
    var targetHost = context.getVariable("target.host");
    var targetBasePath = context.getVariable("targetBaseURI");
    var targetPort = context.getVariable("target.port");
    var targetPath = "";
    var targetSSLEnabled = context.getVariable("target.ssl.enabled");
    var http = "";
    
    if (targetPort != "80" && targetPort != "443")
        targetHost = targetHost + ":" + targetPort;
    
    if (targetSSLEnabled || targetPort == "443")
        http = "https://";
    else
        http = "http://";
        
    targetPath = http+targetHost+targetBasePath;
    
    // Extract required flow variables to form 'proxyPath' url
    var proxyHost = context.getVariable("caller.host");
    var proxyPort = context.getVariable("caller.port");
    var proxyProto = context.getVariable("caller.protocol");
    var proxyBasePath = context.getVariable("proxy.basepath");
    var callerIp = context.getVariable("proxy.client.ip");
    var isSecureProxy = context.getVariable("virtualhost.ssl.enabled");
    var changeProxyHost = true;
    
    if(callerIp === "127.0.0.1" && isSecureProxy){
        proxyProto = "https";
        changeProxyHost = false;
    }
    
    if(proxyPort != "80" && proxyPort != "443" && changeProxyHost)
        proxyHost=proxyHost+":"+proxyPort;
    
    var proxyPath = proxyProto+"://"+proxyHost+proxyBasePath;
    
    var targetPaths=new RegExp(targetPath, 'g');
    if(context.getVariable("proxy.pathsuffix") === "/metadata"){
        parsedResponse = JSON.parse(responseData);
        parsedResponse.url = proxyPath;
        responseData = JSON.stringify(parsedResponse);
    }
    responseData = responseData.replace(targetPaths, proxyPath);
    context.setVariable("response.content", responseData);
    // TODO: In GHC we need to look for Location header 
    
    var contentLocation = context.getVariable("response.header.Content-Location");
    if(contentLocation!=="" && contentLocation!==null)
    {
      contentLocation = contentLocation.replace(targetPath, proxyPath);
      context.setVariable("response.header.Content-Location", contentLocation);
    }
    
    var contentLocation = context.getVariable("response.header.Location");
    if(contentLocation!=="" && contentLocation!==null)
    {
      contentLocation = contentLocation.replace(targetPath, proxyPath);
      context.setVariable("response.header.Location", contentLocation);
    }
    
}catch(Error){
    print("Error " + Error);
    context.setVariable('JS_Error', true);  
    throw new Error("JS_Error");
}