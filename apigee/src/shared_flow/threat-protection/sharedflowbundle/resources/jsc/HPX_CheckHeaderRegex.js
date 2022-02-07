/*
	This javascript check for sql or javascript injection throught headers.
*/

// Get requiredd flow variables
try{
    var headers = context.getVariable("request.headers.names").toString();
    headers = headers.slice(1, -1).split(', ');
    
    var sqlRegex = "[\s]*((delete)|(exec)|(drop\s*table)|(insert)|(shutdown)|(update)|(\bor\b))";
    var jsRegex = "&lt;\s*script\b[^&gt;]*&gt;[^&lt;]+&lt;\s*/\s*script\s*&gt;";
    
    var sqlPatt = new RegExp(sqlRegex);
    var jsPatt = new RegExp(jsRegex);
    
    var threatPresent = false;
    
    headers.forEach(function (header){
        headerValue = context.getVariable("request.header."+header+".values").toString().slice(1,-1);
        if (sqlPatt.test(headerValue) || jsPatt.test(headerValue)) {
            threatPresent = true;
        }
    });
    
    context.setVariable('threatPresent', threatPresent);
}catch(Error){
    print("Error " + Error);
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}