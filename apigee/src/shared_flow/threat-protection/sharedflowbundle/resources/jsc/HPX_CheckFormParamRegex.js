/*
	This javascript check for sql or javascript injection throught form params.
*/

// Get requiredd flow variables
try {

    var querystring = context.getVariable('request.formstring');
    var count = context.getVariable('request.formparams.count');
    
    // Regular expression pattern for sql and javascript injection
    var sqlRegex = "[\s]*((delete)|(exec)|(drop\s*table)|(insert)|(shutdown)|(update)|(\bor\b))";
    var jsRegex = "&lt;\s*script\b[^&gt;]*&gt;[^&lt;]+&lt;\s*/\s*script\s*&gt;";

    var sqlPatt = new RegExp(sqlRegex);
    var jsPatt = new RegExp(jsRegex);
    
    // 'threatPresent' flag used to denote if threat is present or not i.e true = present and false = not present
    // if true then Fault_RegexThreat fault will raise. 
    context.setVariable('threatPresent', false);
    if (count > 0) {
        var params = querystring.split("&");
        for (i = 0; i < params.length; i++) {
            var value = params[i].split("=");
            if (sqlPatt.test(value[1]) || jsPatt.test(value[1])) {
                context.setVariable('threatPresent', true);
            }
        }
    }
}
catch (Error) {
    print("Error " + Error);
    context.setVariable('JS_Error', true);
    throw new Error("JS_Error");
}