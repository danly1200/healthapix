try{
    var client_id = context.getVariable("client_id")
    if( !client_id || client_id === "" ){
        client_id = context.getVariable("request.queryparam.client_id")
    }
    context.setVariable("client_id",client_id);
}catch(e){
    print("Error " + e);
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}
