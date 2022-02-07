try{
    var basepath = context.getVariable("proxy.basepath");
    var slashCount = basepath.split("/").length - 1
    var lastIndexOfSlash = basepath.substring(0, basepath.lastIndexOf("/"));

    var calculatedBasePath = "";
    if (slashCount > 2) {
                calculatedBasePath = lastIndexOfSlash;
    }
    else{
        calculatedBasePath=basepath;
    }
    context.setVariable("calculatedBasePath", calculatedBasePath);

}catch(e){
    print("Error " + e);
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}

