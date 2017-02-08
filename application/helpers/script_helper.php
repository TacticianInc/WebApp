<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// javascript for forgot password
if (!function_exists('forgot_script'))
{
    function forgot_script()
    {
        $script = "$(document).ready(function() {";

        $script = $script."var eml=$('#email');";
        $script = $script."var cpt=$('#captcha');";
        $script = $script."var btn=$('#sbtbutton');";

        $script = $script."var check=function() {";
        $script = $script."var eval=eml.val();";
        $script = $script."var cval=cpt.val();";
        $script = $script."var re=/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;";
        $script = $script."if(eval.length > 3){";
        $script = $script."if(re.test(eval)){";
        $script = $script."if(cval.length > 2){";
        $script = $script."btn.removeAttr('disabled');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."};";

        $script = $script."eml.on('blur keyup', function() {";
        $script = $script."check();";
        $script = $script."});";

        $script = $script."cpt.on('blur keyup', function() {";
        $script = $script."check();";
        $script = $script."});";

        $script = $script."});";

        return $script;
    }
}

// javascript for sign in page
if (!function_exists('signin_script'))
{
    function signin_script()
    {
        $script = "$(document).ready(function() {";

        $script = $script."var eml=$('#email');";
        $script = $script."var psw=$('#password');";
        $script = $script."var btn=$('#sbtbutton');";

        $script = $script."var check=function() {";
        $script = $script."var eval=eml.val();";
        $script = $script."var pval=psw.val();";
        $script = $script."if(eval.length >= 5 && pval.length >= 8){";
        $script = $script."btn.removeAttr('disabled');";
        $script = $script."}";
        $script = $script."};";

        $script = $script."eml.on('blur keyup change', function() {";
        $script = $script."check();";
        $script = $script."});";

        $script = $script."psw.on('blur keyup change', function() {";
        $script = $script."check();";
        $script = $script."});";

        $script = $script."});";

        return $script;
    }
}

// javascript for guest register page
if (!function_exists('register_guest_script'))
{
    function register_quest_script()
    {
        $script = "$(document).ready(function() {";

        $script = $script."var btn=$('#sbtbutton');";

        // validate name
        $script = $script."var name=$('#name');";
        $script = $script."var nameres=$('#nameres');";
        $script = $script."if(name.val().length == 0){";
        $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(name.val().trim().indexOf(' ') != -1){";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."name.on('blur keyup focus', function() {";
        $script = $script."if(name.val().length == 0){";
        $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(name.val().trim().indexOf(' ') != -1){";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // validate title
        $script = $script."var title=$('#title');";
        $script = $script."var titleres=$('#titleres');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."title.on('blur keyup focus', function() {";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate email
        $script = $script."var email=$('#email');";
        $script = $script."var emailres=$('#emailres');";
        $script = $script."var re=/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;";
        $script = $script."if(email.val().length == 0){";
        $script = $script."emailres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(re.test(email.val())){";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."email.on('blur keyup focus', function() {";
        $script = $script."if(email.val().length == 0){";
        $script = $script."emailres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(re.test(email.val())){";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // password strength
        $script = $script."function scorePwd(pass) {";
        $script = $script."var score = 0;";
        $script = $script."if (!pass) {";
        $script = $script."return score;";
        $script = $script."}";
        // award every unique letter until 5 repetitions
        $script = $script."var letters = new Object();";
        $script = $script."for (var i=0; i<pass.length; i++) {";
        $script = $script."letters[pass[i]] = (letters[pass[i]] || 0) + 1;";
        $script = $script."score += 5.0 / letters[pass[i]];";
        $script = $script."}";
        // bonus points for mixing it up
        $script = $script."var variations = {";
        $script = $script."digits: /\d/.test(pass),";
        $script = $script."lower: /[a-z]/.test(pass),";
        $script = $script."upper: /[A-Z]/.test(pass),";
        $script = $script."nonWords: /\W/.test(pass)";
        $script = $script."};";
        $script = $script."variationCount = 0;";
        $script = $script."for (var check in variations) {";
        $script = $script."variationCount += (variations[check] == true) ? 1 : 0;";
        $script = $script."}";
        $script = $script."score += (variationCount - 1) * 10;";
        $script = $script."return parseInt(score);";
        $script = $script."}";
        // controls for password function
        $script = $script."var pass_match=false;";
        $script = $script."var pw=$('#password');";
        $script = $script."var pwres=$('#passres');";
        $script = $script."var cnf=$('#confirm');";
        $script = $script."var cnfres=$('#confres');";
        $script = $script."var pwsc=0;";
        $script = $script."pwres.html('');";
        $script = $script."pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak');";
        $script = $script."pw.on('blur keyup', function() {";
        $script = $script."if(pw.val().length >= 8){";
        $script = $script."pwsc=scorePwd(pw.val());";
        $script = $script."if(pwsc > 60){pwres.html('strong');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_strong');}";
        $script = $script."else if(pwsc > 30 && pwsc <= 60){pwres.html('moderate');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_moderate');}";
        $script = $script."else if(pwsc < 30){pwres.html('weak');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else{";
        $script = $script."pwres.html('Min 8 characters');";
        $script = $script."pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak');";
        $script = $script."}";
        // ensure password and confirm match inside password
        $script = $script."if(cnf.val().length > 0){";
        $script = $script."if(cnf.val() == pw.val()){";
        $script = $script."cnfres.html('match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnfres.html('no match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // ensure password and confirm match
        $script = $script."cnfres.html('');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."cnf.on('blur keyup focus', function() {";
        $script = $script."if(pw.val().length > 0){";
        $script = $script."if(cnf.val().length > 0){";
        $script = $script."if(cnf.val() == pw.val()){";
        $script = $script."cnfres.html('match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnfres.html('no match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."cnfres.html('Password First');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // unlock button
        $script = $script."var score=0;";
        $script = $script."if($('#nameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#titleres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#emailres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#passres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#confres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if(score >=5){btn.prop('disabled', false);}";

        $script = $script."$(document).on('change','input',function() {";
        $script = $script."if($('#nameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#titleres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#emailres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#passres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#confres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if(score >=5){btn.prop('disabled', false);}";
        $script = $script."});";

        $script = $script."});";

        return $script;
    }
}

// javascript for register page
if (!function_exists('register_script'))
{
    function register_script()
    {
        $script = "$(document).ready(function() {";

        $script = $script."var btn=$('#sbtbutton');";

        // filter integer function
        $script = $script."filterInt = function(value){";
        $script = $script."if(/^(\-|\+)?([0-9]+|Infinity)$/.test(value)) {";
        $script = $script."return Number(value);";
        $script = $script."}";
        $script = $script."return NaN;";
        $script = $script."};";

        // state load
        $script = $script."var stinp=document.getElementById('state');";
        $script = $script."var statesel=new Awesomplete(stinp,{minChars:1,maxItems:5});";
        $script = $script."statesel.list=['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];";
        
        // validate name
        $script = $script."var name=$('#name');";
        $script = $script."var nameres=$('#nameres');";
        $script = $script."if(name.val().length == 0){";
        $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(name.val().trim().indexOf(' ') != -1){";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."name.on('blur keyup focus', function() {";
        $script = $script."if(name.val().length == 0){";
        $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(name.val().trim().indexOf(' ') != -1){";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // validate title
        $script = $script."var title=$('#title');";
        $script = $script."var titleres=$('#titleres');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."title.on('blur keyup focus', function() {";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate email
        $script = $script."var email=$('#email');";
        $script = $script."var emailres=$('#emailres');";
        $script = $script."var re=/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;";
        $script = $script."if(email.val().length == 0){";
        $script = $script."emailres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(re.test(email.val())){";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."email.on('blur keyup focus', function() {";
        $script = $script."if(email.val().length == 0){";
        $script = $script."emailres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(re.test(email.val())){";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        
        // validate company
        $script = $script."var company=$('#company');";
        $script = $script."var cmpres=$('#cmpres');";
        $script = $script."if(company.val().length == 0){";
        $script = $script."cmpres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(company.val().length >= 3){";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."company.on('blur keyup focus', function() {";
        $script = $script."if(company.val().length == 0){";
        $script = $script."cmpres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(company.val().length >= 3){";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate street
        $script = $script."var street=$('#street');";
        $script = $script."var streetres=$('#streetres');";
        $script = $script."if(street.val().length == 0){";
        $script = $script."streetres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(street.val().trim().indexOf(' ') != -1){";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."street.on('blur keyup focus', function() {";
        $script = $script."if(street.val().length == 0){";
        $script = $script."streetres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(street.val().trim().indexOf(' ') != -1){";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // load zip codes function
        $script = $script."loadZipData = function(url,city,state,callback){";
        //$script = $script."console.log(url);console.log(city);console.log(state);";
        $script = $script."if(!url||!city||!state){return null;}";
        $script = $script."$.post(url,{c:city,s:state})";
        $script = $script.".done(function(data){";
        $script = $script."return callback(data);";
        $script = $script."});";
        $script = $script."};";

        // validate city
        $script = $script."var city=$('#city');";
        $script = $script."var cityres=$('#cityres');";
        $script = $script."var state=$('#state');";
        $script = $script."var zip=$('#zip');";
        $script = $script."if(city.val().length == 0){";
        $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(city.val().length >= 3){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip'),{ list: zipList });";
        $script = $script."}";
        $script = $script."});";
        $script = $script."cityres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cityres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."city.on('blur keyup focus change', function() {";
        $script = $script."if(city.val().length == 0){";
        $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(city.val().length >= 3){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip'),{ list: zipList });";
        $script = $script."}";
        $script = $script."});";
        $script = $script."cityres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cityres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate state
        $script = $script."if(state.val().length == 0){";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(state.val().length == 2){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip'),{ list: zipList });";
        $script = $script."}";
        $script = $script."});";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."state.on('blur keyup focus change', function() {";
        $script = $script."if(state.val().length == 0){";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(state.val().length == 2){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip'),{ list: zipList });";
        $script = $script."}";
        $script = $script."});";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate zip code
        $script = $script."if(zip.val().length == 0){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."zip.on('blur keyup focus', function() {";
        $script = $script."if(zip.val().length == 0){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."});";

        // password strength
        $script = $script."function scorePwd(pass) {";
        $script = $script."var score = 0;";
        $script = $script."if (!pass) {";
        $script = $script."return score;";
        $script = $script."}";
        // award every unique letter until 5 repetitions
        $script = $script."var letters = new Object();";
        $script = $script."for (var i=0; i<pass.length; i++) {";
        $script = $script."letters[pass[i]] = (letters[pass[i]] || 0) + 1;";
        $script = $script."score += 5.0 / letters[pass[i]];";
        $script = $script."}";
        // bonus points for mixing it up
        $script = $script."var variations = {";
        $script = $script."digits: /\d/.test(pass),";
        $script = $script."lower: /[a-z]/.test(pass),";
        $script = $script."upper: /[A-Z]/.test(pass),";
        $script = $script."nonWords: /\W/.test(pass)";
        $script = $script."};";
        $script = $script."variationCount = 0;";
        $script = $script."for (var check in variations) {";
        $script = $script."variationCount += (variations[check] == true) ? 1 : 0;";
        $script = $script."}";
        $script = $script."score += (variationCount - 1) * 10;";
        $script = $script."return parseInt(score);";
        $script = $script."}";
        // controls for password function
        $script = $script."var pass_match=false;";
        $script = $script."var pw=$('#password');";
        $script = $script."var pwres=$('#passres');";
        $script = $script."var cnf=$('#confirm');";
        $script = $script."var cnfres=$('#confres');";
        $script = $script."var pwsc=0;";
        $script = $script."pwres.html('');";
        $script = $script."pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak');";
        $script = $script."pw.on('blur keyup', function() {";
        $script = $script."if(pw.val().length >= 8){";
        $script = $script."pwsc=scorePwd(pw.val());";
        $script = $script."if(pwsc > 60){pwres.html('strong');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_strong');}";
        $script = $script."else if(pwsc > 30 && pwsc <= 60){pwres.html('moderate');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_moderate');}";
        $script = $script."else if(pwsc < 30){pwres.html('weak');pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else{";
        $script = $script."pwres.html('Min 8 characters');";
        $script = $script."pwres.removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak');";
        $script = $script."}";
        // ensure password and confirm match inside password
        $script = $script."if(cnf.val().length > 0){";
        $script = $script."if(cnf.val() == pw.val()){";
        $script = $script."cnfres.html('match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnfres.html('no match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // ensure password and confirm match
        $script = $script."cnfres.html('');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."cnf.on('blur keyup focus', function() {";
        $script = $script."if(pw.val().length > 0){";
        $script = $script."if(cnf.val().length > 0){";
        $script = $script."if(cnf.val() == pw.val()){";
        $script = $script."cnfres.html('match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnfres.html('no match');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."cnfres.html('Password First');";
        $script = $script."cnfres.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // credit card validate
        $script = $script."var cc_name=$('#cardname');";
        $script = $script."var cc_num=$('#cardnumber');";
        $script = $script."var cc_visa=$('.cc.visa');";
        $script = $script."var cc_mc=$('.cc.mc');";
        $script = $script."var cc_disc=$('.cc.disc');";
        $script = $script."var cc_amex=$('.cc.amex');";
        $script = $script."var cc_type=$('#cc_type');";
        $script = $script."var cc_resp=$('#ccnumres');";
        $script = $script."var ccnm_resp=$('#ccnameres');";

        // validate card name
        $script = $script."if(cc_name.val().length == 0){";
        $script = $script."ccnm_resp.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(cc_name.val().trim().indexOf(' ') != -1){";
        $script = $script."ccnm_resp.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."ccnm_resp.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."cc_name.on('blur keyup focus', function() {";
        $script = $script."if(cc_name.val().length == 0){";
        $script = $script."ccnm_resp.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(cc_name.val().trim().indexOf(' ') != -1){";
        $script = $script."ccnm_resp.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."ccnm_resp.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // validate credit card number
        $script = $script."cc_num.on('blur keyup focus', function() {";
        $script = $script."if(cc_num.val().length == 0){";
        $script = $script."cc_resp.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."cc_num.validateCreditCard(function(result) {";
        $script = $script."if(result && result.card_type){";
        $script = $script."if(cc_num.val().length > 8){";
        $script = $script."if(result.valid && result.length_valid && result.luhn_valid){";
        $script = $script."cc_resp.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cc_resp.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."cc_resp.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}";
        $script = $script."switch(result.card_type.name){";
        $script = $script."case 'visa':cc_type.val('1');cc_visa.removeClass('dk');cc_mc.removeClass('dk').addClass('dk');cc_disc.removeClass('dk').addClass('dk');cc_amex.removeClass('dk').addClass('dk');break;";
        $script = $script."case 'mastercard':cc_type.val('2');cc_visa.removeClass('dk').addClass('dk');cc_mc.removeClass('dk');cc_disc.removeClass('dk').addClass('dk');cc_amex.removeClass('dk').addClass('dk');break;";
        $script = $script."case 'discover':cc_type.val('3');cc_visa.removeClass('dk').addClass('dk');cc_mc.removeClass('dk').addClass('dk');cc_disc.removeClass('dk');cc_amex.removeClass('dk').addClass('dk');break;";
        $script = $script."case 'amex':cc_type.val('4');cc_visa.removeClass('dk').addClass('dk');cc_mc.removeClass('dk').addClass('dk');cc_disc.removeClass('dk').addClass('dk');cc_amex.removeClass('dk');break;";
        $script = $script."default: cc_visa.removeClass('dk').addClass('dk');cc_mc.removeClass('dk').addClass('dk');cc_disc.removeClass('dk').addClass('dk');cc_amex.removeClass('dk').addClass('dk'); break;";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."cc_type.val('');cc_visa.removeClass('dk').addClass('dk');cc_mc.removeClass('dk').addClass('dk');cc_disc.removeClass('dk').addClass('dk');cc_amex.removeClass('dk').addClass('dk');";
        $script = $script."}";
        $script = $script."});";

        // validate expiration date function
        $script = $script."validExpDate = function(m,y){";
        $script = $script."var d=new Date();";
        $script = $script."var mn=(d.getMonth())+1;";
        $script = $script."var yn=d.getFullYear();";
        $script = $script."m=filterInt(m);y=filterInt(y);";
        $script = $script."if(Number.isInteger(m) && Number.isInteger(y)){";
        $script = $script."if(m > 12 || m < 1){";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."if(y > (yn+20) || y < yn){";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."if(y > yn){";
        $script = $script."return true;";
        $script = $script."}else{";
        $script = $script."if((y == yn) && (m > mn)){";
        $script = $script."return true;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        // control code for cc exp date
        $script = $script."var exp_mon=$('#expmonth');";
        $script = $script."var exp_year=$('#expyear');";
        $script = $script."var exp_res=$('#expres');";
        $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."if(exp_mon.length > 1 || exp_year.length > 1){";
            $script = $script."if(validExpDate(exp_mon.val(),exp_year.val())){";
            $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}else{";
            $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}";
        $script = $script."}";
        $script = $script."exp_mon.on('blur keyup focus change', function() {";
        $script = $script."if(validExpDate(exp_mon.val(),exp_year.val())){";
        $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."exp_year.on('blur keyup focus change', function() {";
        $script = $script."if(validExpDate(exp_mon.val(),exp_year.val())){";
        $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."exp_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate card csv
        $script = $script."var cvv=$('#cvv');";
        $script = $script."var cvv_res=$('#cvvres');";
        $script = $script."var cct=$('#cc_type').val();";
        $script = $script."var cvv_val=filterInt(cvv.val());";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."if(cvv.val().length > 0){";
        $script = $script."if(Number.isInteger(cvv_val) == false){";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(cct == 1 || cct == 2 || cct == 3){";
        $script = $script."if(cvv_val > 100 && cvv_val <= 999) {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');} else {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else if(cct == 4){";
        $script = $script."if(cvv_val > 1000 && cvv_val <= 9999) {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');} else {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else{";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."cvv.on('blur keyup focus', function() {";
        $script = $script."var cct=$('#cc_type').val();";
        $script = $script."cvv_val=filterInt(cvv.val());";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."if(cvv.val().length > 0){";
        $script = $script."if(Number.isInteger(cvv_val) == false){";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(cct == 1 || cct == 2 || cct == 3){";
        $script = $script."if(cvv_val > 100 && cvv_val <= 999) {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');} else {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else if(cct == 4){";
        $script = $script."if(cvv_val > 1000 && cvv_val <= 9999) {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');} else {cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');}";
        $script = $script."}else{";
        $script = $script."cvv_res.removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // unlock button
        $script = $script."var score=0;";
        $script = $script."if($('#nameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#titleres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#emailres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cmpres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#streetres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cityres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#state').hasClass('br_strong')){score=score+1}";
        $script = $script."if($('#zip').hasClass('br_strong')){score=score+1}";
        $script = $script."if($('#passres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#confres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#ccnumres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#ccnameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#expres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cvvres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if(score >=14){btn.prop('disabled', false);}";

        $script = $script."$(document).on('change','input',function() {";
        $script = $script."if($('#nameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#titleres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#emailres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cmpres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#streetres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cityres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#state').hasClass('br_strong')){score=score+1}";
        $script = $script."if($('#zip').hasClass('br_strong')){score=score+1}";
        $script = $script."if($('#passres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#confres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#ccnumres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#ccnameres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#expres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if($('#cvvres').hasClass('pw_strong')){score=score+1}";
        $script = $script."if(score >=14){btn.prop('disabled', false);}";
        $script = $script."});";

        $script = $script."});";

        return $script;
    }
}