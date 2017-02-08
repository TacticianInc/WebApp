<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// javascript for billing
if (!function_exists('report_script'))
{
    function report_script($case_id,$user_id,$company_id,$is_admin,$team,$pdf_url)
    {
        $script = "$(document).ready(function() {";

            // post edit data
            $script = $script."var postData = function(url,data,btn,callback) {";
            $script = $script."if(btn){";
            $script = $script."var btntxt = btn.html();";
            $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
            $script = $script."}";
            $script = $script."$.post(url, data)";
            $script = $script.".done(function(ret){";
            $script = $script."return callback(JSON.parse(ret));";
            $script = $script."})";
            $script = $script.".fail(function(err) {";
            $script = $script."return callback(JSON.parse('{\"result\":false,\"msg\":\"'+err.responseText+'\"}'));";
            $script = $script."})";
            $script = $script.".always(function() {";
            $script = $script."if(btn){";
            $script = $script."btn.removeAttr('disabled').html(btntxt);"; // enable button
            $script = $script."}";
            $script = $script."});";
            $script = $script."};";

            $script = $script."function isNumeric(n){";
            $script = $script."return !isNaN(parseFloat(n)) && isFinite(n);";
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

            // load rows
            $script = $script."var buildRows = function(uid,csid,reports){";
                // build html rows
                $script = $script."var ret_value = '';";

                $script = $script."if (!reports || reports.length == 0){";
                    $script = $script."var ret_value='<td colspan=\"7\">No Reports for Given Case</td>';";
                    $script = $script."return ret_value;";
                $script = $script."}";
                
                $script = $script."var ct = reports.length;";
                $script = $script."for(var i=0;i<ct;i++){";
                    $script = $script."var rpt=reports[i];";
                    $script = $script."ret_value += '<tr>';";
                    $script = $script."var dt=new Date(rpt.date_occured);";
                    $script = $script."ret_value += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
                    $script = $script."ret_value += '<td>'+rpt.name+'</td>';";
                    $script = $script."ret_value += '<td>'+rpt.author_name+'</td>';";
                    $script = $script."ret_value += '<td>'+rpt.shared.length+'</td>';";
                    $script = $script."if(rpt.is_redacted == 1){";
                    $script = $script."ret_value += '<td>true</td>';";
                    $script = $script."}else{";
                    $script = $script."ret_value += '<td>false</td>';";
                    $script = $script."}";
                    $script = $script."ret_value += '<td style=\"text-align:right;\">';";

                    // find shared users
                    $script = $script."var sids='';";
                    $script = $script."var cts = rpt.shared.length;";
                    $script = $script."for(var x=0;x<cts;x++){";
                        $script = $script."var shared=rpt.shared[x];";
                        $script = $script."sids += shared.user_id+','";
                    $script = $script."}";
                    $script = $script."sids = sids.replace(/,\s*$/, '');"; // remove last comma

                    $script = $script."ret_value +='<button class=\"btn btn-sm btn-info sharebtn\" id=\"sharebtn_'+rpt.id+'\" sids=\"'+sids+'\" value=\"'+rpt.name+'\" rid=\"'+rpt.id+'\" title=\"Share Report\"><span class=\"glyphicon glyphicon-share\" aria-hidden=\"true\"></span></button>';";
                    $script = $script."ret_value +=' <button class=\"btn btn-sm btn-danger delbtn\" id=\"delbtn_'+rpt.id+'\" rid=\"'+rpt.id+'\" title=\"Delete Report\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button>';";
                    $script = $script."ret_value +=' <button class=\"btn btn-sm btn-default vwbtn\" id=\"vwbtn_'+rpt.id+'\" rid=\"'+rpt.id+'\" title=\"View Report\">View</button>';";
                    $script = $script."ret_value += '</td>';";
                    $script = $script."ret_value += '</tr>';";
                $script = $script."}";

                $script = $script."return ret_value;";
            $script = $script."};";
            $script = $script."var loadRows = function(uid,csid){";
                $script = $script."var data = {userid:uid,caseid:csid};";
                $script = $script."postData('".site_url("data/reports")."',data,null,function(res){";
                    $script = $script."if(res.result){";
                        $script = $script."var body = buildRows(uid,csid,res.reports);";
                        $script = $script."$('#tblReports').html(body);";
                    $script = $script."}else{";
                        $script = $script."$('#tblReports').html('<td colspan=\"7\">No Reports for Given Case</td>');";
                    $script = $script."}";
                $script = $script."});";
            $script = $script."};";

            // load rows on start
            $script = $script."var cid='".$case_id."';";
            $script = $script."var uid='".$user_id."';";
            $script = $script."if(uid && cid){";
                $script = $script."loadRows(uid,cid);";
            $script = $script."}else{";
                $script = $script."$('#tblReports').html('<td colspan=\"7\">No Reports for Given Case</td>');";
            $script = $script."}";

            // on change
            $script = $script."$(document).on('change', '#selCase', function(){";
                $script = $script."$('#tblReports').html();";
                $script = $script."var cid='".$case_id."';";
                $script = $script."var uid='".$user_id."';";
                $script = $script."if(uid && cid){";
                    $script = $script."loadRows(uid,cid);";
                $script = $script."}else{";
                    $script = $script."$('#tblReports').html('<td colspan=\"7\">No Reports for Given Case</td>');";
                $script = $script."}";
            $script = $script."});";

            // build share html
            $script = $script."var buildShareReportForm = function(sids,uid,repid,repname,team){";

                $script = $script."if (!team || team.length == 0){";
                    $script = $script."alert('No team available to share with.');";
                    $script = $script."return;";
                $script = $script."}";

                $script = $script."var retValue='';";
                $script = $script."retValue='<form id=\"frmShareReport\" class=\"form-horizontal\" method=\"post\">';";

                $script = $script."retValue+='<div class=\"alert\" id=\"altMsgShareReport\" role=\"alert\"></div>';";

                $script = $script."retValue+='<p><strong>Share Report</strong>: '+repname+'</p>';";
                $script = $script."retValue+='<hr>';";
                $script = $script."retValue+='<p>Enter an email address to share with non-team members:<p>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"email\" class=\"col-sm-3 control-label\">Email</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";
                $script = $script."retValue+='<input type=\"text\" class=\"form-control\" name=\"email\" id=\"email\" value=\"\" placeholder=\"Email address\">';";
                $script = $script."retValue+='<span id=\"emailres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<hr>';";
                $script = $script."retValue+='<p>Check team members to add or uncheck to remove.</p>';";

                $script = $script."if (team.length > 1) {";
                    $script = $script."retValue+='<div style=\"overflow:auto;height:150px;\">';";
                        $script = $script."var ct=team.length;";
                        $script = $script."for(var i=0;i<ct;i++){";
                            $script = $script."var usr=team[i];";
                            $script = $script."if(uid !== usr.id){";
                                $script = $script."if(sids.includes(usr.id) || sids == usr.id){";
                                    $script = $script."retValue+='<input type=\"checkbox\" class=\"chkShare\" uid=\"'+usr.id+'\" value=\"'+usr.id+'\" checked> '+usr.name+' ['+usr.title+']';";
                                $script = $script."}else{";
                                    $script = $script."retValue+='<input type=\"checkbox\" class=\"chkShare\" uid=\"'+usr.id+'\" value=\"'+usr.id+'\"> '+usr.name+' ['+usr.title+']';";
                                $script = $script."}";
                            $script = $script."}";
                        $script = $script."}";
                    $script = $script."retValue+='</div>';";
                $script = $script."}else{";
                    $script = $script."retValue+='<div style=\"overflow:auto;height:40px;\">';";
                    $script = $script."retValue+='<strong>No Team Members Found</strong>';";
                    $script = $script."retValue+='</div>';";
                $script = $script."}";

                $script = $script."retValue+='<hr>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<div class=\"col-sm-12\" style=\"text-align:right;\">';";
                $script = $script."retValue+='<button type=\"button\" id=\"btnShareReport\" uid=\"'+uid+'\" rid=\"'+repid+'\" class=\"btn btn-success\">Share Report</button>';";
                $script = $script."retValue+=' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='</form>';";
                $script = $script."return retValue;";

            $script = $script."};";

            // build new report html
            $script = $script."var buildNewReportForm = function(dfcid,uid,compid){";

                $script = $script."var retValue='';";
                $script = $script."retValue='<form id=\"frmNewReport\" class=\"form-horizontal\" method=\"post\">';";

                $script = $script."retValue+='<p>Enter report details and press Add Report.</p>';";
                $script = $script."retValue+='<div class=\"alert\" id=\"altMsgNewReport\" role=\"alert\"></div>';";

                $script = $script."retValue+='<hr>';";
                $script = $script."retValue+='<p>Enter an identifier for your report.</p>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"name\" class=\"col-sm-3 control-label\">Name</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";
                $script = $script."retValue+='<input type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" value=\"\" placeholder=\"Report Name\">';";
                $script = $script."retValue+='<span id=\"nameres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<hr>';";

                // redaction
                $script = $script."retValue+='<p>Redaction Settings.</p>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<div class=\"col-sm-12\" style=\"text-align:right;\">';";
                $script = $script."retValue+='<button type=\"button\" id=\"btnSaveNewReport\" uid=\"'+uid+'\" compid=\"'+compid+'\" class=\"btn btn-success\">Add Report</button>';";
                $script = $script."retValue+=' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='</form>';";
                $script = $script."return retValue;";
            $script = $script."};";

            // add button
            $script = $script."$('#btnAddNewReport').on('click', function(e){";
                $script = $script."e.preventDefault();";

                $script = $script."var uid='".$user_id."';";
                $script = $script."var cmid='".$company_id."';";
                $script = $script."var default_cid='".$case_id."';";

                $script = $script."openModal('Add New Report',buildNewReportForm(default_cid,uid,cmid));";
                $script = $script."$('#altMsgNewReport').hide();";

                // validate name
                $script = $script."var name=$('#name');";
                $script = $script."var nameres=$('#nameres');";
                $script = $script."name.on('blur keyup focus', function() {";
                $script = $script."if(name.val().length == 0){";
                $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."}else{";
                $script = $script."if(name.val().length >= 2){";
                $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}else{";
                $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."}";
                $script = $script."});";

                // add new report
                $script = $script."$(document).on('click', '#btnSaveNewReport', function(){";
                    $script = $script."$('#altMsgNewReport').removeClass('alert-danger').html('').hide();";

                    $script = $script."var btn=$(this);";
                    $script = $script."var uid=$(this).attr('uid');";
                    $script = $script."var cmid=$(this).attr('compid');";

                    $script = $script."if(nameres.hasClass('pw_weak')){";
                        $script = $script."$('#altMsgNewReport').addClass('alert-danger').html('Missing required information.').show();";
                    $script = $script."}else{";
                        // save report
                        $script = $script."var cid='".$case_id."';";
                        $script = $script."var nme=$('#name').val();";

                        $script = $script."var data = {caseid:cid,userid:uid,name:nme};";
                        $script = $script."postData('".site_url("add/add_report")."',data,btn,function(res){";
                        $script = $script."if(res.result){";

                            // reload data
                            $script = $script."loadRows(uid,cid);";
                            // close modal
                            $script = $script."$('#btnCloseWin').click();";

                        $script = $script."}else{";
                        $script = $script."$('#altMsgNewReport').addClass('alert-danger').html('Unable to save report.').show();";
                        $script = $script."}";
                        $script = $script."});";

                    $script = $script."}";
                $script = $script."});";

            $script = $script."});";

            // delete button
            $script = $script."$(document).on('click', '.delbtn', function(){";
                $script = $script."var cnfdel = confirm('Delete: Are you sure?');";
                $script = $script."if (cnfdel == true) {";
                    $script = $script."var repid=$(this).attr('rid');";
                    $script = $script."var cid=$('#selCase :selected').attr('id');";
                    $script = $script."var data = {report_id:repid};";
                    $script = $script."postData('".site_url("edit/delete_report")."',data,null,function(res){";
                    $script = $script."if(res.result){";
                        // reload data
                        $script = $script."loadRows(uid,cid);";
                    $script = $script."}else{";
                    $script = $script."alert('Unable to delete report.');";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."}";
            $script = $script."});";

            // share button
            $script = $script."$(document).on('click', '.sharebtn', function(){";
                $script = $script."var team='".json_encode($team)."';";
                $script = $script."var team=JSON.parse(team);";

                $script = $script."var uid='".$user_id."';";
                $script = $script."var repid=$(this).attr('rid');";
                $script = $script."var repname=$(this).attr('value');";
                $script = $script."var sids=$(this).attr('sids');";
                
                $script = $script."openModal('Share Report',buildShareReportForm(sids,uid,repid,repname,team));";
                $script = $script."$('#altMsgShareReport').hide();";

                // validate email if not empty
                $script = $script."var email=$('#email');";
                $script = $script."var emailres=$('#emailres');";
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

                //btnShareReport
                $script = $script."$(document).on('click', '#btnShareReport', function(){";

                    $script = $script."$('#altMsgShareReport').hide();";

                    $script = $script."if(emailres.hasClass('pw_weak')){";
                        $script = $script."$('#altMsgShareReport').addClass('alert-danger').html('Invalid email. Correct or remove.').show();";
                        $script = $script."return false;";
                    $script = $script."}";

                    $script = $script."var uids = [];";
                    // Get user ids
                    $script = $script."$('.chkShare').each(function () {";
                        $script = $script."if (this.checked){";
                            $script = $script."uids.push($(this).attr('uid'));";
                        $script = $script."}";
                    $script = $script."});";

                    // add/share_report
                    $script = $script."var eml=$('#email').val();";
                    $script = $script."var btn=$(this);";
                    $script = $script."var data = {reportid:repid,userids:uids,email:eml};";
                    $script = $script."postData('".site_url("add/share_report")."',data,btn,function(res){";
                    $script = $script."if(res.result){";

                        // reload data
                        $script = $script."var uid='".$user_id."';";
                        $script = $script."var cid='".$case_id."';";
                        $script = $script."loadRows(uid,cid);";
                        // close modal
                        $script = $script."$('#btnCloseWin').click();";

                    $script = $script."}else{";
                    $script = $script."$('#altMsgShareReport').addClass('alert-danger').html('Unable to share report.').show();";
                    $script = $script."}";
                    $script = $script."});";

                $script = $script."});";

            $script = $script."});";

            // view
            $script = $script."$(document).on('click', '.vwbtn', function(){";
                $script = $script."var rid=$(this).attr('rid');";
                $script = $script."var uid='".$user_id."';";
                $script = $script."var pdf_url='".$pdf_url."';";
                $script = $script."var form='<form id=\"pdf_form\" action=\"'+pdf_url+'\" method=\"post\" target=\"_blank\">';";
                $script = $script."form +='<input id=\"uid\" name=\"uid\" type=\"hidden\" value=\"'+uid+'\">';";
                $script = $script."form +='<input id=\"rid\" name=\"rid\" type=\"hidden\" value=\"'+rid+'\">';";
                $script = $script."form +='</form>';";
                $script = $script."var mdlCont = $('#modal_container');";
                $script = $script."mdlCont.html(form);";
                $script = $script."$('#pdf_form').submit();";
            $script = $script."});";

        $script = $script."});";

        return $script;
    }
}