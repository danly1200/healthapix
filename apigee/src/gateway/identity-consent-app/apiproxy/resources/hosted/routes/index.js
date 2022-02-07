/*
 * GET home page.
 */
var Config = require("../config");
var nodemailer = require('nodemailer');
var pkgInfo = require('../package');
const request = require('superagent');

exports.index = function (req, res) {


    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }


    var userid = req.query.userid;

    var requestedDisplay = req.query.display;


    var renderConsentScreen = req.query.renderConsentScreen;
    if (renderConsentScreen == null) {
        renderConsentScreen = true;
    }

    if (renderConsentScreen == "false") {

        res.redirect(req.query.hostName + req.query.basepath + "/redirect/" + req.query.sessionid)
    } else if (userid != null && userid != "") {

        if (req.query.open_id != "true") {

            res.redirect(req.query.hostName + req.query.basepath + "/profile?sessionid=" + req.query.sessionid);

        } else {
            if (req.query.usertype === "Practitioner" || req.query.usertype === "RelatedPerson") {
                handlePatientPick(req, res, redirect_uri, 'start', true);
            } else {
                handleConsent(req, res, redirect_uri, false);
            }
        }

    } else {

        handleIndex(req, res, redirect_uri, "none", "none");

    }



};

exports.redirect = function (req, res) {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    res.send(200);
};

exports.errorflow = function (req, res) {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    // intercept OPTIONS method
    if ('OPTIONS' == req.method) {
        res.send(200);
    }
    handleError(req, res, req.query.redirect_uri, req.param("error"), req.param("description"));


}

exports.login = function (req, res) {


    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }

    // var sess = req.session;
    // requestedDisplay = sess.display;

    if (req.query.renderConsentScreen == "false") {

        res.redirect(req.query.hostName + req.query.basepath + "/redirect/" + req.query.sessionid);
    } else if (req.query.userid == null || req.query.userid == "") {
        handleIndex(req, res, redirect_uri, "true", "none");



    } else {
        // success — user has been logged in
        if (req.query.open_id != "true") {

            res.redirect(req.query.hostName + req.query.basepath + "/profile?sessionid=" + req.query.sessionid);

        } else {
            if (req.query.userType == 'patient') {
                handleConsent(req, res, redirect_uri, true);
            }
            else if (req.query.userType == 'practitioner' || req.query.userType === 'relatedperson') {
                handlePatientPick(req, res, redirect_uri, 'start', true);
            }

        }
    }
};

//logout() handle the logout request
exports.logout = function (req, res) {
    res.send(200);
}

exports.local = function (req, res) {
    var lang = req.body.locale;
    console.log(lang);
    res.cookie("lang", lang, { maxAge: 2629746000, httpOnly: true, path: "/" });   //maxAge in milliseconds, increased from 15mins to 1 month
    res.redirect(req.body.next);
}

exports.create = function (req, res) {

    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }
    var sessionid = req.param("sessionid");

    var error = req.query.error;
    if (error != null) {
        //var sessionid = req.param("sessionid");

        handleRegister(req, res, redirect_uri, "true", error);

        handleError(req, res, req.query.redirect_uri + '?error=true', error, "");

    } else {
        // handlePin(req,res,redirect_uri,"none");  
        //To skip the Pinsubmit flow functionality handlePin() is replaced by call to saveUser. 
        res.redirect(req.query.hostName + req.query.basepath + "/saveUser?sessionid=" + sessionid);
    }
};

exports.reset = function (req, res) {

    // var sess = req.session;
    // requestedDisplay = sess.display;
    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }

    var error = req.query.error;
    var sessionid = req.param("sessionid");
    if (error != null) {

        res.render('recovery', {
            title: 'Forget Password',
            req: req,
            redirectURL: redirect_uri + '?error=refused',
            display: req.query.display,
            logged_in_user_email: "",
            logged_in_user_first_name: "",
            logged_in_user_surname: "",
            logged_in_user_image: "",
            authenticated: false,
            showUserInfoDiv: false,
            sessionid: req.param("sessionid"),
            showErrorMessage: "true",
            error: error,
            basepath: req.query.basepath
        });

    } else {

        // handlePin(req,res,redirect_uri,"none");  //old code with PinSubmit flow
        //To skip the Pinsubmit flow functionality handlePin() is replaced by call to saveUser. 
        res.redirect(req.query.hostName + req.query.basepath + "/saveUser?sessionid=" + sessionid);

    }
}

exports.register = function (req, res) {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    // intercept OPTIONS method
    if ('OPTIONS' == req.method) {
        res.send(200);
    }


    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }

    handleRegister(req, res, redirect_uri, "none", "");
}

exports.recovery = function (req, res) {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    // intercept OPTIONS method
    if ('OPTIONS' == req.method) {
        res.send(200);
    }

    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }


    var sessionid = req.param("sessionid");
    res.render('recovery', {
        title: 'Forget Password',
        authenticated: false,
        req: req,
        redirectURL: redirect_uri + '?error=refused',
        display: req.query.display,
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        showUserInfoDiv: false,
        sessionid: req.param("sessionid"),
        showErrorMessage: "none",
        error: "",
        basepath: req.query.basepath

    });




};

//saveUser() skips the Pinsubmit flow functionality . 

exports.saveUser = function (req, res) {
    var error = req.query.error;
    var redirect_uri = "";

    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }

    if (req.query.renderConsentScreen == "false") {
        res.redirect(req.query.hostName + req.query.basepath + "/redirect/" + req.query.sessionid)
    } else if (error != null) {
        handleError(req, res, redirect_uri + '?error=true', "Error while saving user details", "");
    } else if (req.query.open_id != "true") {
        res.redirect(req.query.hostName + req.query.basepath + "/profile?sessionid=" + req.query.sessionid);
    } else {
        handleConsent(req, res, redirect_uri, false);
    }

};


exports.pinSubmit = function (req, res) {
    var error = req.query.error;
    var redirect_uri = "";
    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }



    if (req.query.renderConsentScreen == "false") {
        res.redirect(req.query.hostName + req.query.basepath + "/redirect/" + req.query.sessionid)
    } else if (error != null || req.query.areMaxTokenTriesExceeded == "true") {

        handleError(req, res, redirect_uri + '?error=true', "Maximum attempts are exceeded", "");


    } else if (req.query.isTokenValidationThrough == "false") {

        handlePin(req, res, redirect_uri, "true");


    } else if (req.query.open_id != "true") {
        res.redirect(req.query.hostName + req.query.basepath + "/profile?sessionid=" + req.query.sessionid);

    } else {
        handleConsent(req, res, redirect_uri, false);

    }

};

exports.msisdnsubmit = function (req, res) {

    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }


    if (req.query.userid == null || req.query.userid == "") {

        handleIndex(req, res, redirect_uri, "none", "true");

    } else {

        handlePin(req, res, redirect_uri, "none");
    }
}


exports.myapps = function (req, res) {

    if (req.query.open_id == "true") {
        //Modified this value to navaigate back from myapps to consent page.
        redirect_uri = req.query.basepath + "/index?sessionid=" + req.query.sessionid;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }
    var applicationsObj = [];
    var consents = req.body.consents;

    if (consents != null) {
        for (var i = 0; i < consents.length; i++) {
            var app = {};
            if (consents[i].status != 'active') {
                continue;
            }
            app.name = consents[i].application_name;
            app.scopes = consents[i].scope;
            app.status = consents[i].status;
            app.consentid = consents[i].consent_id;
            app.last_updated = consents[i].last_updated;
            applicationsObj.push(app);
        }
        var applications = applicationsObj.slice(0);
        applications.sort(function (a, b) {
            return a.last_updated - b.last_updated;
        });
    }

    res.render('myapps', {
        title: "My Applications",
        authenticated: true,
        applications: applications,
        sessionid: req.query.sessionid,
        req: req,
        display: req.query.display || '',
        logged_in_user_email: req.query.email || '',
        logged_in_user_first_name: req.query.first_name,
        logged_in_user_surname: req.query.surname,
        logged_in_user_image: "",
        redirectURL: redirect_uri, //Removed the error query paramter
        showUserInfoDiv: true,
        basepath: req.query.basepath
    });
}

exports.revokeConsent = function (req, res) {
    res.send(200);
}


exports.Profileupdate = function (req, res) {
    res.redirect(req.query.hostName + req.query.basepath + "/profile?sessionid=" + req.query.sessionid);

}

exports.Profile = function (req, res) {
    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/logout" //Modified the redicte url to handle session and logout.
    }
    res.render('myprofile', {
        title: "My Profile",
        authenticated: true,
        username: req.query.email || '',
        first_name: req.query.first_name || '',
        surname: req.query.surname || '',
        email: req.query.email || '',
        msisdn: req.query.msisdn || '',
        house: req.query.house || '',
        locality: req.query.locality || '',
        street: req.query.street || '',
        landmark: req.query.landmark || '',
        postalcode: req.query.postalcode || '',
        city: req.query.city || '',
        state: req.query.state || '',
        country: req.query.country || '',
        sessionid: req.query.sessionid,
        req: req,
        display: req.query.display || '',
        logged_in_user_email: req.query.email || '',
        logged_in_user_first_name: req.query.first_name,
        logged_in_user_surname: req.query.surname,
        logged_in_user_image: "",
        redirectURL: redirect_uri, //Removed the error query paramter
        showUserInfoDiv: true,
        basepath: req.query.basepath
    });
}

exports.profileEdit = function (req, res) {
    res.render('profile_edit', {
        title: 'Profile',
        authenticated: true,
        sessionid: req.query.sessionid,
        username: "",
        email: req.query.email || "",
        surname: req.query.surname || "",
        name: req.query.first_name || "",
        msisdn: req.query.msisdn || "",
        country: req.query.country || "",
        state: req.query.state || "",
        city: req.query.city || "",
        addr1: req.query.house || "",
        addr2: req.query.street || "",
        password: req.query.password,
        req: req,
        display: req.query.display || '',
        logged_in_user_email: req.query.email || '',
        logged_in_user_first_name: req.query.first_name,
        logged_in_user_surname: req.query.surname,
        logged_in_user_image: "",
        redirectURL: redirect_uri + '?error=refused',
        showUserInfoDiv: true,
        basepath: req.query.basepath
    })

}

exports.patients = function (req, res) {
    request.get(pkgInfo.deployment_uri + '/identity-users/v1/patient').query({ page: req.query.page, sessionId: req.query.sessionid }).end((err, resp) => {
        if (err) {
            console.log(err);
            res.status(500).send();
            return
        }
        res.send(resp.body);
        return
    });
}


function handlePatientPick(req, res, redirect_uri, page, isAuthenticated) {
    var requestedDisplay = req.query.display;
    if(req.query.usertype.toLowerCase() === 'relatedperson'){
        request.get(pkgInfo.deployment_uri + '/identity-users/v1/related').query({relative: req.query.patientId, sessionId: req.param("sessionid")}).end((err, resp) => {
            if (err) {
                handleError(req, res, redirect_uri + '?error=true', 'Error while loading patient(s)', 'Something went wrong')
                return
            }
            let patients = resp.body;
            res.render('patients', {
                title: 'Select Patients',
                patients: patients,
                layout: template_layout,
                display: requestedDisplay,
                req: req,
                basepath: req.query.basepath,
                sessionid: req.param("sessionid"),
                redirectURL: redirect_uri,
                showUserInfoDiv: true,
                logged_in_user_email: req.query.useremail,
                logged_in_user_first_name: req.query.firstName || "",
                logged_in_user_surname: "",
                logged_in_user_image: "",
            });
        })
    }else{
        request.get(pkgInfo.deployment_uri + '/identity-users/v1/patient').query({ page: page, sessionId: req.param("sessionid") }).end((err, resp) => {
            if (err) {
                handleError(req, res, redirect_uri + '?error=true', 'Error while loading patients', 'Something went wrong')
                return
            }
            let patients = resp.body;
            res.render('patients', {
                title: 'Select Patients',
                patients: patients,
                layout: template_layout,
                display: requestedDisplay,
                req: req,
                basepath: req.query.basepath,
                sessionid: req.param("sessionid"),
                redirectURL: redirect_uri,
                showUserInfoDiv: true,
                logged_in_user_email: req.query.useremail,
                logged_in_user_first_name: req.query.firstName || "",
                logged_in_user_surname: "",
                logged_in_user_image: "",
            });
        })
    }
}

function handleIndex(req, res, redirect_uri, invalidLogin, invalidMsisdn) {
    var sessionid = req.param("sessionid");
    var requestedDisplay = req.query.display;

    var appName = req.query.appName;

    var socialLogin = true;
    var smsLogin = true;
    var emailLogin = true;

    var authTypes = Config.authTypes;

    if (authTypes && authTypes.length > 0) {
        socialLogin = false;
        smsLogin = false;
        emailLogin = false;
        for (var i = 0; i < authTypes.length; i++) {
            var aType = authTypes[i].toUpperCase();
            if (aType === "SMS") {
                smsLogin = true;
            } else if (aType === "SOCIAL") {
                socialLogin = true;
            } else if (aType === "EMAIL") {
                emailLogin = true;
            }
        }
    }

    res.render('index', {
        title: 'Home',
        req: req,
        layout: template_layout,
        authenticated: false,
        showUserInfoDiv: false,
        sessionid: sessionid,
        redirectURL: redirect_uri + '?error=refused',
        display: requestedDisplay,
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        showInvalidLoginMessage: invalidLogin,
        showInvalidMsisdnMessage: invalidMsisdn,
        activeTab: "normal-login",
        socialLoginVisible: socialLogin,
        smsLoginVisible: smsLogin,
        emailLoginVisible: emailLogin,
        email_to_prefill: "",
        basepath: req.query.basepath,
        google_auth_client_id: pkgInfo.google_auth_client_id,
        google_auth_redirect_uri: pkgInfo.google_auth_redirect_uri
    })
}

function handleConsent(req, res, redirect_uri, isAuthenticated) {

    var user_attribute = "";
    var scopes = req.body.scopes;
    var userid = req.query.userid;

    var requestedDisplay = req.query.display;

    var scope_icon = "";
    var scope_class = "img-circle";
    var app_logo_url = null;
    var userEmail = "";
    var firstName = "";
    var lastName = "";

    if (scopes != null) {
        var scope_array = (scopes.trim()).split(" ")
        if (scope_array.indexOf("profile") != -1) {
            scope_icon = req.query.logged_in_user_image || req.query.basepath + "/ui/images/icons/img_smart.jpg";
            user_attribute = req.query.logged_in_user_first_name;
        } else if (scope_array.indexOf("email") != -1) {
            scope_icon = req.query.logged_in_user_image || req.query.basepath + "/ui/images/icons/img_smart.jpg";
            user_attribute = req.query.logged_in_user_email;
        } else {
            for (var i = 0; i < scope_array.length; i++) {

                if (scope_array[i] == "mobileid" || scope_array[i] == "phone") {
                    user_attribute = "+" + req.query.formatted_user_msisdn;
                    scope_icon = req.query.basepath + "/ui/images/mobile-circle.png";
                    scope_class = "close-img";
                    break;
                }
            }
        }
    }


    var isShowUserDiv = false;
    if (isAuthenticated == true) {
        isShowUserDiv = true;
    }
    if (userid != null && userid != "") {
        userEmail = userid;
        isShowUserDiv = true;
    }

    tempName = req.query.firstName;
    if (tempName != null) {
        firstName = tempName;
    }

    tempName = req.query.lastName;
    if (tempName != null) {
        lastName = tempName;
    }


    var sessionid = req.param("sessionid");
    res.render('consent', {
        title: 'Consent',
        req: req,
        display: requestedDisplay || '',
        logged_in_user_email: userEmail,
        logged_in_user_first_name: firstName,
        logged_in_user_surname: lastName,
        logged_in_user_image: "",
        authenticated: isAuthenticated,
        sessionid: req.param("sessionid"),
        appName: req.query.appName,
        profile_scopes: req.body.scope,
        app_logo_url: "",
        scope_icon: scope_icon,
        scope_class: scope_class,
        showUserInfoDiv: isShowUserDiv,
        user_attribute: user_attribute,
        redirectURL: redirect_uri + '?error=refused',
        basepath: req.query.basepath
    });


}

function handleError(req, res, redirect_uri, error, description) {

    res.render('errorflow', {
        title: 'Error',
        authenticated: false,
        error: error,
        description: description || '',
        req: req,
        display: req.query.display || '',
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        showUserInfoDiv: false,
        redirectURL: redirect_uri + '?error=true',
        basepath: req.query.basepath
    });

}

function handleRegister(req, res, redirect_uri, showError, error) {
    res.render('register', {
        title: 'Register',
        authenticated: false,
        sessionid: req.param("sessionid"),
        showErrorMessage: showError,
        req: req,
        display: req.query.display || '',
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        showUserInfoDiv: false,
        redirectURL: redirect_uri + '?error=refused',
        error: error,
        basepath: req.query.basepath

    });

}

function handlePin(req, res, redirect_uri, showError) {
    res.render('pin', {
        title: 'Pin',
        req: req,
        display: req.query.display || '',
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        authenticated: false,
        showUserInfoDiv: false,
        sessionid: req.param("sessionid"),
        redirectURL: redirect_uri + '?error=refused',
        showErrorMessage: showError,
        basepath: req.query.basepath

    });



}

exports.sendEmailNotification = function (req, res) {
    if (req.query.open_id == "true") {
        redirect_uri = req.query.redirect_uri;
    } else {
        redirect_uri = req.query.basepath + "/index";
    }
    var error = req.query.error;
    if (error != null) {
        res.render('recovery', {
            title: 'Forget Password',
            req: req,
            redirectURL: redirect_uri + '?error=refused',
            display: req.query.display,
            logged_in_user_email: "",
            logged_in_user_first_name: "",
            logged_in_user_surname: "",
            logged_in_user_image: "",
            authenticated: false,
            showUserInfoDiv: false,
            sessionid: req.param("sessionid"),
            showErrorMessage: "true",
            error: error,
            basepath: req.query.basepath
        });
    } else {
        var transporter = nodemailer.createTransport({
            host: pkgInfo.emailHostUrl,
            port: pkgInfo.portNo,
            secure: pkgInfo.secureConnection,
            auth: {
                user: pkgInfo.userName,
                pass: pkgInfo.password
            }
        });
        transporter.sendMail(mailOptions(req), function (error, info) {
            res.redirect(req.query.hostName + req.query.basepath + "/forgetpassword");
        });
    }

};
exports.forgetpwd = function (req, res) {
    res.render('recovery_response', {
        req: req,
        title: "Forget Password",
        display: req.query.display || '',
        redirectURL: req.query.basepath + "/index",
        logged_in_user_email: "",
        logged_in_user_first_name: "",
        logged_in_user_surname: "",
        logged_in_user_image: "",
        authenticated: false,
        showUserInfoDiv: false,
        showErrorMessage: "true",
        basepath: req.query.basepath
    });
}
function mailOptions(req) {
    var maiOption = {
        from: pkgInfo.fromEmail,
        to: req.query.email,
        subject: req.query.subject,
        html: req.query.text
    };
    return maiOption;
}