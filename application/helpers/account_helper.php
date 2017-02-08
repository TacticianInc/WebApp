<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");

if (!function_exists("edit_script"))
{
    function edit_script($plans, $user_id, $company_id)
    {
        $script = "$(document).ready(function() {";

        // hash change
        $script = $script."var pnlProfile=$('#pnlProfile');";
        $script = $script."var pnlCollab=$('#pnlCollab');";
        $script = $script."var pnlAgency=$('#pnlAgency');";
        $script = $script."var tabProfile=$('#tabProfile');";
        $script = $script."var tabCollab=$('#tabCollab');";
        $script = $script."var tabAgency=$('#tabAgency');";
        // show hide tab panels
        $script = $script."var showProfile = function() {";
        $script = $script."tabProfile.removeClass('active').addClass('active');";
        $script = $script."tabCollab.removeClass('active');";
        $script = $script."tabAgency.removeClass('active');";
        $script = $script."pnlProfile.show(); pnlCollab.hide(); pnlAgency.hide();";
        $script = $script."};";
        $script = $script."var showCollab = function() {";
        $script = $script."tabProfile.removeClass('active');";
        $script = $script."tabCollab.removeClass('active').addClass('active');";
        $script = $script."tabAgency.removeClass('active');";
        $script = $script."pnlProfile.hide(); pnlCollab.show(); pnlAgency.hide();";
        $script = $script."};";
        $script = $script."var showAgency = function() {";
        $script = $script."tabProfile.removeClass('active');";
        $script = $script."tabCollab.removeClass('active');";
        $script = $script."tabAgency.removeClass('active').addClass('active');";
        $script = $script."pnlProfile.hide(); pnlCollab.hide(); pnlAgency.show();";
        $script = $script."};";
        // hash change function
        $script = $script."var hash_change = function(hash) {";
        $script = $script."if (hash) {";
        $script = $script."switch(hash){";
        $script = $script."case '#profile': showProfile();  break;";
        $script = $script."case '#collaborators': showCollab(); break;";
        $script = $script."case '#agency': showAgency(); break;";
        $script = $script."default: showProfile(); break;";
        $script = $script."}";
        $script = $script."} else {";
        $script = $script."showProfile();";
        $script = $script."}";
        $script = $script."};";
        // use to set default
        $script = $script."hash_change(window.location.hash);";
        // hash change event
        $script = $script."$(window).on('hashchange', function() {";
        $script = $script."var hash = window.location.hash;";
        $script = $script."hash_change(window.location.hash);";
        $script = $script."});";

        $script = $script."filterInt = function(value){";
        $script = $script."if(/^(\-|\+)?([0-9]+|Infinity)$/.test(value)) {";
        $script = $script."return Number(value);";
        $script = $script."}";
        $script = $script."return NaN;";
        $script = $script."};";

        // post edit data
        $script = $script."var postData = function(url,data,btn,callback) {";
        $script = $script."var btntxt = btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."$.post(url, data)";
        $script = $script.".done(function(ret){";
        $script = $script."return callback(JSON.parse(ret));";
        $script = $script."})";
        $script = $script.".fail(function(err) {";
        $script = $script."return callback(JSON.parse('{\"result\":false,\"msg\":\"'+err.responseText+'\"}'));";
        $script = $script."})";
        $script = $script.".always(function() {";
        $script = $script."btn.removeAttr('disabled').html(btntxt);"; // enable button
        $script = $script."});";
        $script = $script."};";

        // upload image
        $script = $script."var upload = function(form,url,btn,msg,img1,img2) {";
        $script = $script."form.on('submit',(function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');"; // disable button
        $script = $script."msg.removeClass('alert-danger').html('').hide();";
        $script = $script."$.ajax({";
        $script = $script."url: url,";
        $script = $script."type: 'POST',";
        $script = $script."data:  new FormData(this),";
        $script = $script."contentType: false,";
        $script = $script."cache: false,";
        $script = $script."processData:false,";
        $script = $script."success: function(data){";
        $script = $script."var res = JSON.parse(data);";
        $script = $script."if(res.url){";
        $script = $script."if(img1){img1.attr('src',res.url);}";
        $script = $script."if(img2){img2.attr('src',res.url);}";
        $script = $script."}";
        $script = $script."btn.removeAttr('disabled').html('Upload');"; // enable button
        $script = $script."$('#modalwin').find('.close').click();";
        $script = $script."},";
        $script = $script."error: function(err){";
        $script = $script."msg.addClass('alert-danger').html(err.responseText).show();";
        $script = $script."btn.removeAttr('disabled').html('Upload');"; // enable button 
        $script = $script."}";
        $script = $script."});";
        $script = $script."}));";
        $script = $script."};";

        // preview image
        $script = $script."function readURL(input,elem) {";
        $script = $script."if (input.files && input.files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."elem.attr('src', e.target.result);";
        $script = $script."};";
        $script = $script."reader.readAsDataURL(input.files[0]);";
        $script = $script."}";
        $script = $script."}";
        // show modal
        $script = $script."var buildModal = function(title,body,footer){";
        $script = $script."var ret_value='<div id=\"modalwin\" class=\"modal\">'";
        $script = $script."+'<div class=\"modal-content\">'";
        $script = $script."+'<div class=\"modal-header\">'";
        $script = $script."+'<span class=\"close\">×</span>'";
        $script = $script."+title";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"modal-body\">'";
        $script = $script."+body";
        $script = $script."+'</div>';";
        $script = $script."if (footer) {";
        $script = $script."ret_value +='<div class=\"modal-footer\">'";
        $script = $script."+footer";
        $script = $script."+'</div>';";
        $script = $script."}";
        $script = $script."ret_value +='</div>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        $script = $script."var openModal = function(title,body,footer){";
        $script = $script."var mdlCont = $('#modal_container');";
        $script = $script."mdlCont.html(buildModal(title,body,footer));";
        $script = $script."var modal = $('#modalwin');";
        $script = $script."modal.show();";
        $script = $script."var close = $('.close');";
        $script = $script."close.click(function(){modal.hide();});";
        $script = $script."var btclose = $('#btnCloseWin');";
        $script = $script."btclose.click(function(){modal.hide();});";
        $script = $script."};";

        $script = $script."var buildAlertPopUp = function(title,msg){";
        $script = $script."var ret_value='<div id=\"modalwin\" class=\"modal\">'";
        $script = $script."+'<div class=\"modal-content\">'";
        $script = $script."+'<div class=\"modal-header\">'";
        $script = $script."+'<span class=\"close\">×</span>'";
        $script = $script."+title";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"modal-body\">'";
        $script = $script."+'<div class=\"pull-left text-danger\" style=\"font-size:24px;\">'";
        $script = $script."+'<span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div style=\"display:inline;margin-left:20px;\">'";
        $script = $script."+msg";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"modal-footer\">'";
        $script = $script."+'<button class=\"btn btn-danger\" id=\"btnAltOk\">Ok</button>'";
        $script = $script."+'&nbsp;<button class=\"btn btn-default\" id=\"btnCloseWin\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        $script = $script."var openAlertPopUp = function(title,msg){";
        $script = $script."var mdlCont = $('#modal_container');";
        $script = $script."mdlCont.html(buildAlertPopUp(title,msg));";
        $script = $script."var modal = $('#modalwin');";
        $script = $script."modal.show();";
        $script = $script."var close = $('.close');";
        $script = $script."close.click(function(){modal.hide();});";
        $script = $script."var btclose = $('#btnCloseWin');";
        $script = $script."btclose.click(function(){modal.hide();});";
        $script = $script."};";

        // edit company image
        $script = $script."var buildChgCompImg = function() {";
        $script = $script."var ret_value='<div class=\"image_box_holder\">'";
        $script = $script."+'<div class=\"image_box_logo\">'";
        $script = $script."+'<img id=\"prevImgAgency\" class=\"img-thumbnail\" src=\"\" style=\"height:128px;\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<form id=\"frmAgencyImg\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgAgency\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" id=\"agencyimgid\" name=\"agencyimgid\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file and press upload.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleAgency\" id=\"fleAgency\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"submit\" id=\"btnSaveAgImage\" class=\"btn btn-success\">Upload</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";

        // change company image
        $script = $script."var chgCompImg = $('#aChangeCompImg');";
        $script = $script."chgCompImg.on('click', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."openModal('Agency Image',buildChgCompImg());";
        $script = $script."$('#altMsgAgency').hide();";
        $script = $script."var old_source=$('#imgAgency').attr('src');";
        $script = $script."$('#prevImgAgency').attr('src',old_source);";
        $script = $script."$('#fleAgency').change(function(){";
        $script = $script."readURL(this,$('#prevImgAgency'));";
        // upload set to ajax
        $script = $script."upload($('#frmAgencyImg'),'".site_url("edit/agency_image")."',$('#btnSaveAgImage'),$('#altMsgAgency'),$('#imgAgency'))";
        $script = $script."});";
        $script = $script."});";

        // edit profile image
        $script = $script."var buildChgProfImg = function() {";
        $script = $script."var ret_value='<div class=\"image_box_holder\">'";
        $script = $script."+'<div class=\"image_box\">'";
        $script = $script."+'<img id=\"prevImgProfile\" class=\"img-thumbnail\" src=\"\" style=\"width:48px;height:48px;\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<form id=\"frmProfImg\" name=\"frmProfImg\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgProf\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" id=\"profimgid\" name=\"profimgid\" value=\"".md5($user_id)."\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file and press upload.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleProfile\" id=\"fleProfile\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"submit\" id=\"btnSavePrfImg\" class=\"btn btn-success\">Upload</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";

        // change profile image
        $script = $script."var chgProfImg = $('#aChangeProfImg');";
        $script = $script."chgProfImg.on('click', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."openModal('Profile Image',buildChgProfImg());";
        $script = $script."$('#altMsgProf').hide();";
        $script = $script."var old_source=$('#imgProfile').attr('src');";
        $script = $script."$('#prevImgProfile').attr('src',old_source);";
        $script = $script."$('#fleProfile').change(function(){";
        $script = $script."readURL(this,$('#prevImgProfile'));";
        // upload set to ajax
        $script = $script."upload($('#frmProfImg'),'".site_url("edit/profile_image")."',$('#btnSavePrfImg'),$('#altMsgProf'),$('#imgProfile'),$('#img-prof-thumb'))";
        $script = $script."});";
        $script = $script."});";

        // build invite collaborators
        $script = $script."var buildInviteTeam = function() {";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Enter a name and email to invite.</p>'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgInvite\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"inv_comp_id\" id=\"inv_comp_id\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_inv\" id=\"name_inv\" value=\"\" placeholder=\"First and Last Name\">'";
        $script = $script."+'<span id=\"nameres_inv\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"email\" class=\"col-sm-2 control-label\">Email</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"email\" class=\"form-control\" name=\"email_inv\" id=\"email_inv\" value=\"\" placeholder=\"Email\">'";
        $script = $script."+'<span id=\"emailres_inv\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnInviteNew\" class=\"btn btn-success\">Invite</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";

        // invite collaborators
        $script = $script."var btnInvite = $('#btnInvite');";
        $script = $script."btnInvite.on('click',function(){";
        $script = $script."openModal('Invite Collaborators', buildInviteTeam());";
        $script = $script."$('#altMsgInvite').hide();";
        $script = $script."var name=$('#name_inv');";
        $script = $script."var nameres=$('#nameres_inv');";
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
        $script = $script."var email=$('#email_inv');";
        $script = $script."var emailres=$('#emailres_inv');";
        $script = $script."var re=/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;";
        $script = $script."email.on('blur keyup focus', function() {";
        $script = $script."if(email.val().length == 0){";
        $script = $script."emailres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(re.test(email.val())){";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."emailres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        // handle upload of edit data
        $script = $script."$('#btnInviteNew').on('click', function() {";
        $script = $script."$('#altMsgInvite').removeClass('alert-danger').html('').hide();";
        $script = $script."var validNme = nameres.hasClass('pw_strong');";
        $script = $script."var validEml = emailres.hasClass('pw_strong');";
        $script = $script."if (validNme && validEml) {";
        $script = $script."var cid = $('#inv_comp_id').val();";
        $script = $script."var data = {name:name.val(), email:email.val(), comp_id:cid};";
        $script = $script."postData('".site_url("edit/invite_user")."',data,$('#btnInviteNew'),function(res){";
        $script = $script."if(res.result){";
        // close window
        $script = $script."$('#modalwin').find('.close').click();";
        // add item to table tblInvites
        $script = $script."invDate = new Date();";
        $script = $script."var day = invDate.getDate();";
        $script = $script."var month = invDate.getMonth()+1;";
        $script = $script."var year = invDate.getFullYear();";
        $script = $script."if (day < 10) {";
        $script = $script."day=0+String(day)"; // add leading 0
        $script = $script."}";
        $script = $script."if (month < 10) {";
        $script = $script."month=0+String(month)"; // add leading 0
        $script = $script."}";
        $script = $script."var dispDate = month+'/'+day+'/'+year;";
        $script = $script."var row='<tr>'";
        $script = $script."+'<td><button class=\"btn btn-primary btn-sm reinvite\" data=\"'+name.val()+','+email.val()+'\" disabled><span class=\"glyphicon glyphicon-envelope\" aria-hidden=\"true\"></span></button></td>'";
        $script = $script."+'<td>'+name.val()+'</td>'";
        $script = $script."+'<td>'+email.val()+'</td>'";
        $script = $script."+'<td class=\"tdcount\">1</td>'";
        $script = $script."+'<td class=\"tddate\">'+dispDate+'</td>'";
        $script = $script."+'</tr>';";
        $script = $script."$('#tbInvitedBody').prepend(row)";
        $script = $script."} else {";
        $script = $script."$('#altMsgInvite').addClass('alert-danger').html(res.msg).show();";
        $script = $script."}";
        $script = $script."});";
        // handle error
        $script = $script."} else {";
        $script = $script."$('#altMsgInvite').addClass('alert-danger').html('Valid name and email required.').show();";
        $script = $script."}";
        $script = $script."});";
        // end upload
        $script = $script."});";

        // re-invite collaborators
        $script = $script."$('.reinvite').on('click',function(){";
        $script = $script."openModal('Reinvite Collaborator', buildInviteTeam());";
        $script = $script."$('#altMsgInvite').hide();";
        $script = $script."var table_row = $(this).parent('td').parent('tr');";
        $script = $script."var count_cell = table_row.find('.tdcount');";
        $script = $script."var date_cell = table_row.find('.tddate');";
        $script = $script."var nmem = $(this).attr('data');";
        $script = $script."var elems = nmem.split(',');";
        $script = $script."var name=$('#name_inv');";
        $script = $script."var nameres=$('#nameres_inv');";
        $script = $script."var email=$('#email_inv');";
        $script = $script."var emailres=$('#emailres_inv');";
        $script = $script."if(elems[0]){name.val(elems[0]);nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');}";
        $script = $script."if(elems[1]){email.val(elems[1]);emailres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');}";
        // disable text boxes
        $script = $script."name.attr('disabled', 'disabled');";
        $script = $script."email.attr('disabled', 'disabled');";
        // handle upload
        $script = $script."$('#btnInviteNew').on('click', function() {";
        $script = $script."$('#altMsgInvite').removeClass('alert-danger').html('').hide();";
        $script = $script."var validNme = nameres.hasClass('pw_strong');";
        $script = $script."var validEml = emailres.hasClass('pw_strong');";
        $script = $script."if (validNme && validEml) {";
        $script = $script."var cid = $('#inv_comp_id').val();";
        $script = $script."var data = {name:name.val(), email:email.val(), comp_id:cid};";
        $script = $script."postData('".site_url("edit/invite_user")."',data,$('#btnInviteNew'),function(res){";
        $script = $script."if(res.result){";
        // close window
        $script = $script."$('#modalwin').find('.close').click();";
        // update count in table count_cell 
        $script = $script."var oldcount=parseInt(count_cell.html());";
        $script = $script."var newcount=oldcount+1;";
        $script = $script."count_cell.html(newcount);";
        // update date in table date_cell
        $script = $script."invDate = new Date();";
        $script = $script."var day = invDate.getDate();";
        $script = $script."var month = invDate.getMonth()+1;";
        $script = $script."var year = invDate.getFullYear();";
        $script = $script."if (day < 10) {";
        $script = $script."day=0+String(day)"; // add leading 0
        $script = $script."}";
        $script = $script."if (month < 10) {";
        $script = $script."month=0+String(month)"; // add leading 0
        $script = $script."}";
        $script = $script."var dispDate = month+'/'+day+'/'+year;";
        $script = $script."date_cell.html(dispDate);";
        $script = $script."} else {";
        $script = $script."$('#altMsgInvite').addClass('alert-danger').html(res.msg).show();";
        $script = $script."}";
        $script = $script."});";
        // handle error
        $script = $script."} else {";
        $script = $script."$('#altMsgInvite').addClass('alert-danger').html('Valid name and email required.').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";

        // remove user
        $script = $script."var remUser = $('.usr_remove');";
        $script = $script."remUser.on('click', function(){";
        $script = $script."var usr = $(this).attr('data');";
        $script = $script."var uparts = usr.split(',');";
        $script = $script."var uid=0; var uname='user';";
        $script = $script."if (uparts.length > 0) {";
        $script = $script."uid=uparts[0];uname=uparts[1];";
        $script = $script."}";
        $script = $script."openAlertPopUp('Remove User','<span id=\"alert_text\">This will remove '+uname+'.&nbsp;Are You Sure?</span>');";
        $script = $script."var okbtn = $('#btnAltOk');";
        $script = $script."okbtn.on('click', function() {";
        $script = $script."$('#alert_text').removeClass('text-danger').html('Removing '+uname+' from agency...');";
        $script = $script."var data = {user_id:uid};";
        $script = $script."postData('".site_url("edit/remove_user")."',data,$('#btnAltOk'),function(res){";
        $script = $script."if(res.result){";
        $script = $script."$('#row_'+uid).remove();";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."} else {";
        $script = $script."$('#alert_text').addClass('text-danger').html('Error: '+res.msg);";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";

        // edit user role
        $script = $script."var showRoleEdit = function(name){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgRole\" role=\"alert\"></div>'";
        $script = $script."+'<p>Select a new role for '+name+'.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<div class=\"radio\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"radio\" name=\"rdoRole\" id=\"rdoRoleAdmin\" value=\"1\">Admin'";
        $script = $script."+'</label>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"radio\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"radio\" name=\"rdoRole\" id=\"rdoRoleCollab\" value=\"0\">Collaborator'";
        $script = $script."+'</label>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnChangeRole\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // open modal
        $script = $script."var edtUser = $('.usr_edit');";
        $script = $script."edtUser.on('click', function(){";
        $script = $script."var me = $(this);";
        $script = $script."var usr = $(this).attr('data');";
        $script = $script."var uparts = usr.split(',');";
        $script = $script."var uid=0; var uname='user'; var role=0;";
        $script = $script."if (uparts.length > 0) {";
        $script = $script."uid=uparts[0];uname=uparts[1];role=uparts[2];";
        $script = $script."}";
        // open modal
        $script = $script."openModal('Change Role', showRoleEdit(uname));";
        $script = $script."if (role==0) {";
        $script = $script."$('#rdoRoleCollab').prop('checked', true);";
        $script = $script."$('#rdoRoleAdmin').prop('checked', false);";
        $script = $script."}else{";
        $script = $script."$('#rdoRoleCollab').prop('checked', false);";
        $script = $script."$('#rdoRoleAdmin').prop('checked', true);";
        $script = $script."}";
        $script = $script."$('#altMsgRole').hide();";
        $script = $script."$('#btnChangeRole').on('click', function(){";
        $script = $script."$('#altMsgRole').removeClass('alert-danger').html('').hide();";
        $script = $script."var selrole = $('input[name=rdoRole]:checked').val();"; // get selected radio
        $script = $script."var data = {user_id:uid,is_admin:selrole};";
        $script = $script."postData('".site_url("edit/edit_user_role")."',data,$('#btnChangeRole'),function(res){";
        $script = $script."if(res.result){";
        // update table with new role
        $script = $script."if (selrole > 0){";
        $script = $script."$('#role_'+uid).html('<strong>Admin</strong>');";
        $script = $script."}else{";
        $script = $script."$('#role_'+uid).html('<strong>Collaborator</strong>');";
        $script = $script."}";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."me.attr('data',uid+','+uname+','+selrole);";
        $script = $script."} else {";
        $script = $script."$('#altMsgRole').addClass('alert-danger').html(res.msg).show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";

        // edit company
        $script = $script."var showCompEdit = function(name,street,city,state,zip){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgComp\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"edt_comp_id\" id=\"edt_comp_id\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"company\" class=\"col-sm-2 control-label\">Agency</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"company\" id=\"company\" value=\"'+name+'\" placeholder=\"Agency Name\" required>'";
        $script = $script."+'<span id=\"cmpres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street\" id=\"street\" value=\"'+street+'\" placeholder=\"Street Address\">'";
        $script = $script."+'<span id=\"streetres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city\" id=\"city\" value=\"'+city+'\" placeholder=\"City\">'";
        $script = $script."+'<span id=\"cityres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<label for=\"state\" class=\"col-sm-2 control-label\">State</label>'";
        $script = $script."+'<div class=\"col-sm-2\">'";
        $script = $script."+'<input type=\"text\" size=\"2\" class=\"form-control\" name=\"state\" value=\"'+state+'\" id=\"state\" placeholder=\"State\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"zip\" class=\"col-sm-2 control-label\">Zipcode</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"zip\" value=\"'+zip+'\" id=\"zip\" placeholder=\"5 digit Zipcode\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveCompany\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // global variables
        $script = $script."var cname=$('#hdCompName');";
        $script = $script."var cstreet=$('#hdStreet');";
        $script = $script."var ccity=$('#hdCity');";
        $script = $script."var cstate=$('#hdState');";
        $script = $script."var czip=$('#hdZip');";
        $script = $script."var compHtml='';";
        $script = $script."var edtPanl=$('#edtCompany');";
        $script = $script."var edtC=$('#btnEditCompany');";
        // switch between edit and view
        $script = $script."edtC.on('click', function() {";
        $script = $script."if (edtC.html() == 'Edit'){";
        $script = $script."compHtml = edtPanl.html();";
        $script = $script."edtC.addClass('btn-warning').removeClass('btn-default').html('Cancel');";
        $script = $script."edtPanl.html(showCompEdit(cname.val(),cstreet.val(),ccity.val(),cstate.val(),czip.val()));";
        $script = $script."$('#altMsgComp').removeClass('alert-danger').html('').hide();";
            // state load
            $script = $script."var stinp=document.getElementById('state');";
            $script = $script."var statesel=new Awesomplete(stinp,{minChars:1,maxItems:5});";
            $script = $script."statesel.list=['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];";
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
            $script = $script."cityres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}else{";
            $script = $script."cityres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}";
            $script = $script."if(state.val().length == 0){";
            $script = $script."state.removeClass('br_strong').removeClass('br_weak');";
            $script = $script."}else if(state.val().length == 2){";
            $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
            $script = $script."}else{";
            $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
            $script = $script."}";
            $script = $script."if(zip.val().length == 0){";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
            $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
            $script = $script."}else{";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
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
            $script = $script."zip.on('blur keyup focus', function() {";
            $script = $script."if(zip.val().length == 0){";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
            $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
            $script = $script."}else{";
            $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
            $script = $script."}";
            $script = $script."});";

            // save company edit
            $script = $script."var saveBtn=$('#btnSaveCompany');";
            $script = $script."saveBtn.on('click', function() {";
            $script = $script."$('#altMsgComp').removeClass('alert-danger').html('').hide();";
            $script = $script."var validComp = cmpres.hasClass('pw_strong');";
            $script = $script."var validStreet = streetres.hasClass('pw_strong');";
            $script = $script."var validCity = cityres.hasClass('pw_strong');";
            $script = $script."var validState = state.hasClass('br_strong');";
            $script = $script."var validZip = zip.hasClass('br_strong');";
            $script = $script."if (validComp && validStreet && validCity && validState && validZip){";
            // handle edit upload
            $script = $script."var cid = $('#edt_comp_id').val();";
            $script = $script."var data = {name:company.val(), street:street.val(), city:city.val(), state:state.val(), zip:zip.val(), comp_id:cid};";
            $script = $script."postData('".site_url("edit/edit_agency")."',data,$('#btnSaveCompany'),function(res){";
            $script = $script."if(res.result){";
            //success
            $script = $script."$('#btnEditCompany').click();";
            $script = $script."var csz = city.val()+','+state.val()+' '+zip.val();";
            $script = $script."$('#ag_name').html(company.val());";
            $script = $script."$('#ag_street').html(street.val());";
            $script = $script."$('#ag_city_state_zip').html(csz);";
            $script = $script."$('#hdCompName').val(company.val());";
            $script = $script."$('#hdStreet').val(street.val());";
            $script = $script."$('#hdCity').val(city.val());";
            $script = $script."$('#hdState').val(state.val());";
            $script = $script."$('#hdZip').val(zip.val());";
            $script = $script."}else{";
            $script = $script."$('#altMsgComp').addClass('alert-danger').html(res.msg).show();";
            $script = $script."}";
            $script = $script."});";
            $script = $script."}else{";
            $script = $script."$('#altMsgComp').addClass('alert-danger').html('All fields required').show();";
            $script = $script."}";
            $script = $script."});";

        $script = $script."}else{";
        $script = $script."edtPanl.html(compHtml);";
        $script = $script."edtC.removeClass('btn-warning').addClass('btn-default').html('Edit');";
        $script = $script."}";
        $script = $script."});";
        
        // edit profile
        $script = $script."var showProfEdit = function(name,title){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgProf\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"prof_uid\" id=\"prof_uid\" value=\"".md5($user_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" value=\"'+name+'\" placeholder=\"First and Last Name\">'";
        $script = $script."+'<span id=\"nameres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title\" id=\"title\" value=\"'+title+'\" placeholder=\"Your Job Title\">'";
        $script = $script."+'<span id=\"titleres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveProfile\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // global variables
        $script = $script."var pname=$('#hdPName');";
        $script = $script."var ptitle=$('#hdPTitle');";
        $script = $script."var profHtml='';";
        $script = $script."var edtPanlProf=$('#edtProfile');";
        $script = $script."var edtP=$('#btnEditProfile');";
        $script = $script."edtP.on('click', function() {";
        $script = $script."if (edtP.html() == 'Edit'){";
        $script = $script."profHtml = edtPanlProf.html();";
        $script = $script."edtP.addClass('btn-warning').removeClass('btn-default').html('Cancel');";
        $script = $script."edtPanlProf.html(showProfEdit(pname.val(),ptitle.val()));";
        $script = $script."$('#altMsgProf').removeClass('alert-danger').html('').hide();";
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

            // save profile edit
            $script = $script."var saveBtnP=$('#btnSaveProfile');";
            $script = $script."saveBtnP.on('click', function() {";
            $script = $script."$('#altMsgProf').removeClass('alert-danger').html('').hide();";
            $script = $script."var validName = nameres.hasClass('pw_strong');";
            $script = $script."var validTitle = titleres.hasClass('pw_strong');";
            $script = $script."if (validName && validTitle){";
            $script = $script."var uid = $('#prof_uid').val();";
            $script = $script."var data = {name:name.val(), title:title.val(), user_id:uid};";
            $script = $script."postData('".site_url("edit/edit_profile")."',data,$('#btnSaveProfile'),function(res){";
            $script = $script."if(res.result){";
            $script = $script."$('#btnEditProfile').click();";
            $script = $script."$('#pr_name').html(name.val());";
            $script = $script."$('#spnProfile').html(name.val());";
            $script = $script."$('#ancProfile').attr('title',name.val());";
            $script = $script."$('#pr_title').html(title.val());";
            $script = $script."$('#hdPName').val(name.val());";
            $script = $script."$('#hdPTitle').val(title.val());";
            $script = $script."}else{";
            $script = $script."$('#altMsgProf').addClass('alert-danger').html(res.msg).show();";
            $script = $script."}";
            $script = $script."});";
            $script = $script."}else{";
            $script = $script."$('#altMsgProf').addClass('alert-danger').html('All fields required').show();";
            $script = $script."}";
            $script = $script."});";

        $script = $script."}else{";
        $script = $script."edtPanlProf.html(profHtml);";
        $script = $script."edtP.removeClass('btn-warning').addClass('btn-default').html('Edit');";
        $script = $script."}";
        $script = $script."});";

        // edit account
        $script = $script."var showAccEdit = function(email){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgAcct\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"acct_uid\" id=\"acct_uid\" value=\"".md5($user_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"email\" class=\"col-sm-2 control-label\">Email</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"email\" class=\"form-control\" name=\"email\" id=\"email\" value=\"'+email+'\" placeholder=\"Email\">'";
        $script = $script."+'<span id=\"emailres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<p>Only complete if you wish to change your password.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"password\" class=\"col-sm-2 control-label\">Password</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"password\" class=\"form-control\" name=\"password\" id=\"password\" placeholder=\"Password\">'";
        $script = $script."+'<span id=\"passres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"confirm\" class=\"col-sm-2 control-label\">Confirm</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"password\" class=\"form-control\" name=\"confirm\" id=\"confirm\" placeholder=\"Password Again\">'";
        $script = $script."+'<span id=\"confres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveAccount\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // global variables
        $script = $script."var aemail=$('#hdAEmail');";
        $script = $script."var accHtml='';";
        $script = $script."var edtPanlAcc=$('#edtAccount');";
        $script = $script."var edtA=$('#btnEditAccount');";
        $script = $script."edtA.on('click', function() {";
        $script = $script."if (edtA.html() == 'Edit'){";
        $script = $script."accHtml = edtPanlAcc.html();";
        $script = $script."edtA.addClass('btn-warning').removeClass('btn-default').html('Cancel');";
        $script = $script."edtPanlAcc.html(showAccEdit(aemail.val()));";
        $script = $script."$('#altMsgAcct').removeClass('alert-danger').html('').hide();";
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

            // validate password
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

            // save account edit
            $script = $script."var saveBtnA=$('#btnSaveAccount');";
            $script = $script."saveBtnA.on('click', function() {";
            $script = $script."$('#altMsgAcct').removeClass('alert-danger').html('').hide();";
            $script = $script."var validEmail = emailres.hasClass('pw_strong');";
            $script = $script."var validPw = !pwres.hasClass('pw_weak');";
            $script = $script."var validCPw = !cnfres.hasClass('pw_weak');";
            $script = $script."if (validEmail && validPw && validCPw){";
            $script = $script."var uid = $('#acct_uid').val();";
            $script = $script."var data = {email:email.val(), pass:pw.val(), user_id:uid};";
            $script = $script."postData('".site_url("edit/edit_account")."',data,$('#btnSaveAccount'),function(res){";
            $script = $script."if(res.result){";
            $script = $script."$('#btnEditAccount').click();";
            $script = $script."$('#aemail').html(email.val());";
            $script = $script."}else{";
            $script = $script."$('#altMsgAcct').addClass('alert-danger').html(res.msg).show();";
            $script = $script."}";
            $script = $script."});";
            $script = $script."}else{";
            $script = $script."$('#altMsgProf').addClass('alert-danger').html('All fields required').show();";
            $script = $script."}";

            $script = $script."});";

        $script = $script."}else{";
        $script = $script."edtPanlAcc.html(accHtml);";
        $script = $script."edtA.removeClass('btn-warning').addClass('btn-default').html('Edit');";
        $script = $script."}";
        $script = $script."});";

        // edit payment
        $script = $script."var showPayEdit = function(){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<p>Plan Selection</p>'";
if (isset($plans) && count($plans) > 0) {

    if (!isset($plan_id) || $plan_id == 0) {
        $plan_id = 1;
    }

    foreach ($plans as $pln) {
        $html_id = str_replace(" ", "_", $pln['name']);
        $html_id = strtolower($html_id);

        $script = $script."+'<div class=\"radio\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"radio\" id=\"".$html_id."\" name=\"rdoPlan\" value=\"".$pln['id']."\">".$pln['name']." $".$pln['total']."'";
        $script = $script."+'</label>'";
        $script = $script."+'</div>'";
    }
}
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnUpdatePlan\" class=\"btn btn-default\">Update Plan</button> '";
        $script = $script."+'<button type=\"button\" id=\"btnCancelPlan\" class=\"btn btn-danger\">Cancel Plan</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<p>Credit Card Information</p>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"cardname\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"cardname\" id=\"cardname\" value=\"\" placeholder=\"Name on Credit Card\">'";
        $script = $script."+'<span id=\"ccnameres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"cardnumber\" class=\"col-sm-2 control-label\">Number</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"cardnumber\" id=\"cardnumber\" value=\"\" placeholder=\"Credit Card Number\">'";
        $script = $script."+'<span id=\"ccnumres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<input type=\"hidden\" id=\"cc_type\" value=\"\">'";
        $script = $script."+'<span class=\"cc visa dk\"></span>'";
        $script = $script."+'<span class=\"cc mc dk\"></span>'";
        $script = $script."+'<span class=\"cc amex dk\"></span>'";
        $script = $script."+'<span class=\"cc disc dk\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"expyear expmonth csv\" class=\"col-sm-2 control-label\">Exp</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"number\" min=\"1\" max=\"12\" value=\"\" class=\"form-control\" name=\"expmonth\" id=\"expmonth\" placeholder=\"MM\">'";
        $script = $script."+'<span class=\"input-group-addon\" id=\"expres\">/</span>'";
        $date_min = date('Y');
        $date_max = $date_min+20;
        $script = $script."+'<input type=\"number\" min=\"".$date_min."\" max=\"".$date_max."\" value=\"\" class=\"form-control\" name=\"expyear\" id=\"expyear\" placeholder=\"YYYY\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"col-sm-4\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"cvv\" id=\"cvv\" value=\"\" placeholder=\"CVV\" required>'";
        $script = $script."+'<span class=\"input-group-addon\" id=\"cvvres\"><span class=\"glyphicon glyphicon-credit-card\" style=\"font-size:18px;\" aria-hidden=\"true\"></span></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-offset-2 col-sm-10\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSavePayment\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // global variables
        $script = $script."var payHtml='';";
        $script = $script."var pplan=$('hdPPlan');";
        $script = $script."var edtPanlPay=$('#edtPayment');";
        $script = $script."var edtPay=$('#btnEditPayment');";
        $script = $script."edtPay.on('click', function() {";
        $script = $script."if (edtPay.html() == 'Edit'){";
        $script = $script."payHtml = edtPanlPay.html();";
        $script = $script."edtPay.addClass('btn-warning').removeClass('btn-default').html('Cancel');";
        $script = $script."edtPanlPay.html(showPayEdit());";

            // credit card validate
            $script = $script."filterInt = function(value){";
            $script = $script."if(/^(\-|\+)?([0-9]+|Infinity)$/.test(value)) {";
            $script = $script."return Number(value);";
            $script = $script."}";
            $script = $script."return NaN;";
            $script = $script."};";
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

            // save new cc
            $script = $script."var saveBtnPay = $('btnSavePayment');";
            $script = $script."saveBtnPay.on('click', function() {";
                // TODO: Process Save New CC
            $script = $script."});";

            // cancel plan
            $script = $script."var cnPlanBtn = $('btnCancelPlan');";
            $script = $script."saveBtnPay.on('click', function() {";
                // TODO: Process Cancel Plan
            $script = $script."});";

            // update plan
            $script = $script."var upPlanBtn = $('btnUpdatePlan');";
            $script = $script."saveBtnPay.on('click', function() {";
                // TODO: Process Update Plan
            $script = $script."});";

        $script = $script."}else{";
        $script = $script."edtPanlPay.html(payHtml);";
        $script = $script."edtPay.removeClass('btn-warning').addClass('btn-default').html('Edit');";
        $script = $script."}";
        $script = $script."});";


        // closing - very end
        $script = $script."});";

        return $script;
    }
}
