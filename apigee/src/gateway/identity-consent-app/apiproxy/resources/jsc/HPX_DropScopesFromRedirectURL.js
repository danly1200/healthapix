try{
    var redirectUrl = context.getVariable("response.header.Location");
    var formattedUrl = redirectUrl.split("?")[1];
    formattedUrl = formattedUrl.split("&");
    var newUrl = [];
    formattedUrl.forEach(function(item){
        if(item.split("=")[0] !== "scope"){
            newUrl.push(item);
        }
    });
    
    newUrl = redirectUrl.split("?")[0]+'?'+newUrl.join("&");
    context.setVariable("response.header.Location", newUrl);
}catch(e){
    
}