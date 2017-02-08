<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('cases_script'))
{
    function cases_script($cases,$is_admin)
    {
        $script = "$(document).ready(function() {";

        // build case table
        $script = $script."var allRows='';";
        $script = $script."var openRows='';";
        $script = $script."var closedRows='';";
        $script = $script."var allCount=0;";
        $script = $script."var openCount=0;";
        $script = $script."var closedCount=0;";

        if (isset($cases) && count($cases) > 0) {
            foreach ($cases as $case) {
        
                $script = $script."var row='';";
                $script = $script."row += '<tr>';";

                $script = $script."row += '<td><a href=\"".site_url('mycases/view_case')."/".md5($case['id'])."\">".$case['name']."</a></td>';";
                $script = $script."row += '<td>".date('m/d/Y', strtotime($case['created']))."</td>';";
                $script = $script."row += '<td>".date('m/d/Y H:i:s', strtotime($case['modified']))."</td>';";
                $script = $script."row += '</tr>';";

                $script = $script."allRows += row;";
                $script = $script."allCount += 1;";

                if($case['is_closed']){
                    $script = $script."closedRows += row;";
                    $script = $script."closedCount += 1;";
                }else{
                    $script = $script."openRows += row;";
                    $script = $script."openCount += 1;";
                }

            }
        }

        // add counts and rows
        $script = $script."$('#allCt').html(allCount);";
        $script = $script."$('#openCt').html(openCount);";
        $script = $script."$('#closedCt').html(closedCount);";
        // manage tab changes via hash
        $script = $script."var tab_all = $('#tabAll');";
        $script = $script."var tab_open = $('#tabOpen');";
        $script = $script."var tab_closed = $('#tabClosed');";
        // set default
        $script = $script."tab_all.removeClass('active');";
        $script = $script."tab_open.removeClass('active');";
        $script = $script."tab_closed.removeClass('active');";
        // hash change
        $script = $script."var hash_change=function(hash){";
        $script = $script."if(hash){";
        $script = $script."switch(hash){";
        // all
        $script = $script."case '#all':";
        $script = $script."tab_all.removeClass('active').addClass('active');";
        $script = $script."tab_open.removeClass('active');";
        $script = $script."tab_closed.removeClass('active');";
        $script = $script."$('#tblCases').html(allRows);";
        $script = $script."break;";
        // open
        $script = $script."case '#open':";
        $script = $script."tab_all.removeClass('active');";
        $script = $script."tab_open.removeClass('active').addClass('active');";
        $script = $script."tab_closed.removeClass('active');";
        $script = $script."$('#tblCases').html(openRows);";
        $script = $script."break;";
        // closed
        $script = $script."case '#closed':";
        $script = $script."tab_all.removeClass('active');";
        $script = $script."tab_open.removeClass('active');";
        $script = $script."tab_closed.removeClass('active').addClass('active');";
        $script = $script."$('#tblCases').html(closedRows);";
        $script = $script."break;";
        // unknown hash
        $script = $script."default:";
        $script = $script."tab_all.removeClass('active').addClass('active');";
        $script = $script."tab_open.removeClass('active');";
        $script = $script."tab_closed.removeClass('active');";
        $script = $script."$('#tblCases').html(allRows);";
        $script = $script."break;";
        $script = $script."}";
        $script = $script."}else{";
        // empty hash
        $script = $script."tab_all.removeClass('active').addClass('active');";
        $script = $script."tab_open.removeClass('active');";
        $script = $script."tab_closed.removeClass('active');";
        $script = $script."$('#tblCases').html(allRows);";
        $script = $script."}";
        $script = $script."};";
        // default
        $script = $script."hash_change(window.location.hash);";
        // hash change event handler
        $script = $script."$(window).on('hashchange',function(){";
        $script = $script."hash_change(window.location.hash);";
        $script = $script."});";
    
        


        // end script
        $script = $script."});";

        return $script;
    }
}

if (!function_exists('view_case_script'))
{
    function view_case_script($team, $base_url, $company_id, $case_id, $user_id, $case_url, $case_name, $is_admin, $is_team_lead)
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
        // determine if browser supports date
        $script = $script."var supportsHTML5Date = function() {";
        $script = $script."var input = document.createElement('input');";
        $script = $script."input.setAttribute('type','date');";
        $script = $script."var notADateValue = 'not-a-date';";
        $script = $script."input.setAttribute('value', notADateValue);";
        $script = $script."return (input.value !== notADateValue);";
        $script = $script."};";
        // validate creation date
        $script = $script."var checkdate = function(input){";
        $script = $script."var validformat=/^\d{2}\/\d{2}\/\d{4}$/;";
        $script = $script."if (!validformat.test(input.val())) {";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."var monthfield=input.val().split('/')[0];";
        $script = $script."var dayfield=input.val().split('/')[1];";
        $script = $script."var yearfield=input.val().split('/')[2];";
        $script = $script."var dayobj = new Date(yearfield, monthfield-1, dayfield);";
        $script = $script."if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return true;";
        $script = $script."}";
        $script = $script."};";

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
        // show alert
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

        // GLOBAL
        // handle tab change
        $script = $script."var case_name='".$case_name."';";
        $script = $script."var tabCase = $('#tabCase');";
        $script = $script."var tabSynopsis = $('#tabSynopsis');";
        $script = $script."var tabInterviews = $('#tabInterviews');";
        $script = $script."var tabAttachments = $('#tabAttachments');";
        $script = $script."var tabAdministrative = $('#tabAdministrative');";
        $script = $script."var pnlMainCase = $('#pnlMainCase');";
        $script = $script."var pnlMainSynopsis = $('#pnlMainSynopsis');";
        $script = $script."var pnlMainInterviews = $('#pnlMainInterviews');";
        $script = $script."var pnlMainAttachments = $('#pnlMainAttachments');";
        $script = $script."var pnlMainAdministrative = $('#pnlMainAdministrative');";
        // default hide all tab panels
        $script = $script."tabCase.removeClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.hide();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        // breadcrumb
        $script = $script."var breadcrumb_change=function(elem,url,actElem){";
        $script = $script."var ret_value='<li>';";
        $script = $script."ret_value+='<a href=\"".$case_url."\">Cases</a>';";
        $script = $script."ret_value+='</li>';";
        $script = $script."if(elem && url){";
        $script = $script."ret_value+='<li>';";
        $script = $script."ret_value+='<a href=\"'+url+'\">'+elem+'</a>';";
        $script = $script."ret_value+='</li>';";
        $script = $script."}";
        $script = $script."ret_value+='<li class=\"active\">'+actElem+'</li>';";
        $script = $script."$('#brdMain').html(ret_value);";
        $script = $script."};";
        // hash change
        $script = $script."var hash_change=function(hash){";
        $script = $script."if(hash){";
        $script = $script."switch(hash){";
        // case
        $script = $script."case '#case':";
        $script = $script."breadcrumb_change(null,null,case_name);";
        $script = $script."tabCase.removeClass('active').addClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.show();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."break;";
        // synopsis
        $script = $script."case '#synopsis':";
        $script = $script."breadcrumb_change(case_name,'#case','Synopsis');";
        $script = $script."tabCase.removeClass('active');";
        $script = $script."tabSynopsis.removeClass('active').addClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.hide();";
        $script = $script."pnlMainSynopsis.show();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."break;";
        // interviews
        $script = $script."case '#interviews':";
        $script = $script."breadcrumb_change(case_name,'#case','Interviews');";
        $script = $script."tabCase.removeClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active').addClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.hide();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.show();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."break;";
        // attachments
        $script = $script."case '#attachments':";
        $script = $script."breadcrumb_change(case_name,'#case','Attachments');";
        $script = $script."tabCase.removeClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active').addClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.hide();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.show();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."break;";
        // administrative
        $script = $script."case '#administrative':";
        $script = $script."breadcrumb_change(case_name,'#case','Administrative');";
        $script = $script."tabCase.removeClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active').addClass('active');";
        $script = $script."pnlMainCase.hide();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.show();";
        $script = $script."break;";
        // unknown hash
        $script = $script."default:";
        $script = $script."breadcrumb_change(null,null,case_name);";
        $script = $script."tabCase.removeClass('active').addClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.show();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."break;";
        $script = $script."}";
        $script = $script."}else{";
        // empty hash
        $script = $script."breadcrumb_change(null,null,case_name);";
        $script = $script."tabCase.removeClass('active').addClass('active');";
        $script = $script."tabSynopsis.removeClass('active');";
        $script = $script."tabInterviews.removeClass('active');";
        $script = $script."tabAttachments.removeClass('active');";
        $script = $script."tabAdministrative.removeClass('active');";
        $script = $script."pnlMainCase.show();";
        $script = $script."pnlMainSynopsis.hide();";
        $script = $script."pnlMainInterviews.hide();";
        $script = $script."pnlMainAttachments.hide();";
        $script = $script."pnlMainAdministrative.hide();";
        $script = $script."}";
        $script = $script."};";
        // default
        $script = $script."hash_change(window.location.hash);";
        // hash change event handler
        $script = $script."$(window).on('hashchange',function(){";
        $script = $script."hash_change(window.location.hash);";
        $script = $script."});";
        $script = $script."filterInt = function(value){";
        $script = $script."if(/^(\-|\+)?([0-9]+|Infinity)$/.test(value)) {";
        $script = $script."return Number(value);";
        $script = $script."}";
        $script = $script."return NaN;";
        $script = $script."};";

        // GLOBAL CONTROLS
        // import file
        $script = $script."var curFileObj={};";
        $script = $script."var impFileList=[];";
        $script = $script."var buildImportFile = function(intid) {";
        $script = $script."if(!intid){intid=0;}";
        $script = $script."var ret_value=''";
        $script = $script."+'<form id=\"frmImpFile\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgImpFile\" role=\"alert\"></div>'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file (Word, Excel, PowerPoint, PDF, Image, or Video) and press Add to Case.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleImpFile\" id=\"fleImpFile\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div id=\"fleDragDrop\">'";
        $script = $script."+'<span>Or Drag File Here</span>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"progress\" id=\"prgHolder\">'";
        $script = $script."+'<div class=\"progress-bar progress-bar-success\" id=\"prgBar\" role=\"progressbar\" style=\"width:2em;\">'";
        $script = $script."+'&nbsp;0%'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_fle\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_fle\" id=\"title_fle\" value=\"\" placeholder=\"Document Title\">'";
        $script = $script."+'<span id=\"titleres_fle\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"tags_fle\" class=\"col-sm-2 control-label\">Tags</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"tags_fle\" id=\"tags_fle\" value=\"\" placeholder=\"Comma Seperated Tags\">'";
        $script = $script."+'<span id=\"tagsres_fle\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<input type=\"hidden\" id=\"intid\" value=\"'+intid+'\">'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveImpFile\" class=\"btn btn-success\">Add to Case</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // file present handler
        $script = $script."var readURLForm = function(input,notes,tags,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (input.files && input.files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=input.files[0].type;";
        $script = $script."retvalue.name=input.files[0].name;";
        $script = $script."reader.readAsDataURL(input.files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        $script = $script."var readURLDrop = function(files,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (files && files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=files[0].type;";
        $script = $script."retvalue.name=files[0].name;";
        $script = $script."reader.readAsDataURL(files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        // doc handler
        $script = $script."var readURLFormDoc = function(input,doc_type,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (input.files && input.files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.att_type=input.files[0].type;";
        $script = $script."retvalue.doc_type=doc_type;";
        $script = $script."retvalue.name=input.files[0].name;";
        $script = $script."reader.readAsDataURL(input.files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        $script = $script."var readURLDropDoc = function(files,doc_type,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (files && files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.att_type=files[0].type;";
        $script = $script."retvalue.doc_type=doc_type;";
        $script = $script."retvalue.name=files[0].name;";
        $script = $script."reader.readAsDataURL(files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        // build file presentation icon
        $script = $script."var presentFile = function(name) {";
        $script = $script."if(name){";
        $script = $script."var ft=name.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."var img='';";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'png':";
        $script = $script."case 'jpg':";
        $script = $script."case 'jpeg': img='".base_url("img/icons/image.png")."'; break;";
        $script = $script."case 'txt': img='".base_url("img/icons/text.png")."'; break;";
        $script = $script."case 'xlsx': img='".base_url("img/icons/excel.png")."'; break;";
        $script = $script."case 'docx': img='".base_url("img/icons/word.png")."'; break;";
        $script = $script."case 'pptx': img='".base_url("img/icons/powerpoint.png")."'; break;";
        $script = $script."case 'pdf': img='".base_url("img/icons/pdf.png")."'; break;";
        $script = $script."case 'mp4':";
        $script = $script."case 'webm':";
        $script = $script."case 'ogv':";
        $script = $script."case 'mp3': img='".base_url("img/icons/video.png")."'; break;";
        $script = $script."}";
        $script = $script."if(img !== ''){";
        $script = $script."var pres='<img src=\"'+img+'\" class=\"flimg\"><span class=\"flname\">'+name+'</span>';";
        $script = $script."return pres;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        // add files
        $script = $script."$('#btnAddAtt,#btnAddAttachment,#ancAddAttachments').on('click', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."openModal('Import A File',buildImportFile());";
        $script = $script."$('#prgHolder').hide();";
        // handle tags and title validation
        $script = $script."$('#title_fle').on('blur keyup focus', function(){";
        $script = $script."var title=$('#title_fle');";
        $script = $script."var titleres=$('#titleres_fle');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#tags_fle').on('blur keyup focus', function(){";
        $script = $script."var tags=$('#tags_fle');";
        $script = $script."var tagsres=$('#tagsres_fle');";
        $script = $script."if(tags.val().length == 0){";
        $script = $script."tagsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(tags.val().trim().indexOf(',') != -1){";
        $script = $script."tagsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."tagsres.html('comma required').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        // handle file control add
        $script = $script."$('#fleImpFile').on('change', function(){";
        $script = $script."readURLForm(this,$('#notes_fle').val(),$('#tags_fle').val(),function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";
        // handle drop file
        $script = $script."$(document).on('dragover', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragover', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('drop', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDrop(files,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // build row for display
        $script = $script."$(document).on('click', '#btnSaveImpFile', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        $script = $script."if(curFileObj){";
        $script = $script."curFileObj['title'] = $('#title_fle').val();";
        $script = $script."curFileObj['tags'] = $('#tags_fle').val();";
        $script = $script."impFileList.push(curFileObj);";
        $script = $script."curFileObj={};";
        $script = $script."}";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (impFileList && impFileList.length > 0)) {";
        // save file via AJAX
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var intid=$('#intid').val();";
        $script = $script."var data = {caseid:cid,uid:userid,iid:intid,fls:impFileList};";
        $script = $script."postData('".site_url("add/add_supporting_docs")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // success - load table
        $script = $script."var data = {caseid:cid};";
        $script = $script."postData('".site_url("data/attachments")."',data,btn,function(res){";
        $script = $script."if(res.result && res.docs){";
        $script = $script."var docs=res.docs;";
        $script = $script."$('#tbodyAtt').html('');";
        $script = $script."$('#tbodyAttMain').html('');";
        $script = $script."$('#tblIntAttsBody').html('');";
        $script = $script."var doccount=docs.length;";
        $script = $script."$('#attMainCount').html(doccount+' attachment(s)');";
        $script = $script."$('#numAttachments').html(doccount);";
        $script = $script."for(var i=0;i<docs.length;i++){";
        $script = $script."var doc=docs[i];";
        $script = $script."var row='<tr>';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $script = $script."row += '<td><button class=\"btn btn-danger btn-xs delatt\" type=\"button\" dataid=\"'+doc.id+'\" dataname=\"'+doc.number+'\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
}else{
        $script = $script."row += '<td>&nbsp;</td>';";
}
        $script = $script."row += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
        $script = $script."row += '<td><a href=\"'+doc.url+'\" class=\"ancAtt\">'+doc.number+'</a></td>';";
        $script = $script."var dt=new Date(doc.created);";
        $script = $script."row += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
        $script = $script."row += '</tr>';";
        $script = $script."$('#tbodyAtt').append(row);";
        $script = $script."$('#tbodyAttMain').append(row);";
            //$script = $script."if (intid == doc.intid){";
            //$script = $script."$('#tblIntAttsBody').append(row);";
            //$script = $script."}";
        $script = $script."}";
        $script = $script."$('.ancintview[data=\"'+intid+'\"]').click();";
        $script = $script."impFileList = [];";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."impFileList = [];";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('No file added. Please select or drop a file to Add to Case.').show();";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."}";
        $script = $script."});";
        // remove attachment
        $script = $script."$(document).on('click','.delatt',function(){";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var aname=$(this).attr('dataname');";
        $script = $script."var aid=$(this).attr('dataid');";
        $script = $script."var intid=$(this).attr('intid');";
        $script = $script."openAlertPopUp('Delete '+aname,'<span id=\"alert_text\">This cannot be undone. Are you sure?</span>');";
        $script = $script."var okbtn = $('#btnAltOk');";
        $script = $script."okbtn.on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var data = {attid:aid};";
        $script = $script."postData('".site_url("edit/remove_attachment")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // success - load table
        $script = $script."var data = {caseid:cid};";
        $script = $script."postData('".site_url("data/attachments")."',data,btn,function(res){";
        $script = $script."if(res.result && res.docs){";
        $script = $script."var docs=res.docs;";
        $script = $script."$('#tbodyAtt').html('');";
        $script = $script."$('#tbodyAttMain').html('');";
        $script = $script."$('#tblIntAttsBody').html('');";
        $script = $script."var doccount=docs.length;";
        $script = $script."$('#attMainCount').html(doccount+' attachment(s)');";
        $script = $script."$('#numAttachments').html(doccount);";
        $script = $script."for(var i=0;i<docs.length;i++){";
        $script = $script."var doc=docs[i];";
        $script = $script."var row='<tr>';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $script = $script."row += '<td><button class=\"btn btn-danger btn-xs delatt\" type=\"button\" intid=\"'+doc.intid+'\" dataid=\"'+doc.id+'\" dataname=\"'+doc.name+'\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
}else{
        $script = $script."row += '<td>&nbsp;</td>';";
}
        $script = $script."row += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
        $script = $script."row += '<td><a href=\"'+doc.url+'\" class=\"ancAtt\">'+doc.number+'</a></td>';";
        $script = $script."var dt=new Date(doc.created);";
        $script = $script."row += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
        $script = $script."row += '</tr>';";
        $script = $script."$('#tbodyAtt').append(row);";
        $script = $script."$('#tbodyAttMain').append(row);";
            $script = $script."if (intid == doc.intid){";
            $script = $script."$('#tblIntAttsBody').append(row);";
            $script = $script."}";
        $script = $script."}";
        $script = $script."$('.ancintview[data=\"'+intid+'\"]').click();";
        $script = $script."}";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";
        // import document
        $script = $script."var buildDocUpload=function(name,id,other){";
        $script = $script."var ret_value=''";
        $script = $script."+'<form id=\"frmImpDoc\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgImpDoc\" role=\"alert\"></div>';";
        $script = $script."if(other){";
        $script = $script."ret_value+='<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_doc\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_doc\" id=\"title_doc\" value=\"\" placeholder=\"Document Title\">'";
        $script = $script."+'<span id=\"titleres_doc\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>';";
        $script = $script."}else{";
        $script = $script."ret_value+='<div style=\"text-align:center\" id=\"title_dv\">'";
        $script = $script."+'<strong>Document for</strong>: '+name";
        $script = $script."+'</div>';";
        $script = $script."}";
        $script = $script."ret_value+='<hr>'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file (Word, Excel, PowerPoint, PDF, Image, or Video) and press Add.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleImpDoc\" id=\"fleImpDoc\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div id=\"fleDragDrop\">'";
        $script = $script."+'<span>Or Drag File Here</span>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"progress\" id=\"prgHolderDoc\">'";
        $script = $script."+'<div class=\"progress-bar progress-bar-success\" id=\"prgBarDoc\" role=\"progressbar\" style=\"width:2em;\">'";
        $script = $script."+'&nbsp;0%'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveImpDoc\" class=\"btn btn-success\">Add</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // attach multiple documents
        $script = $script."var buildMultipleAttach=function(name,docs,is_single){";
        $script = $script."var ret_value=''";
        $script = $script."+'<form id=\"frmMultAtt\" class=\"form-horizontal\" method=\"post\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgMultAtt\" role=\"alert\"></div>';";
        $script = $script."ret_value+='<div style=\"text-align:center\" id=\"title_dv\">'";
        $script = $script."+'<strong>Document for</strong>: '+name";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>';";
        $script = $script."if(docs){";
        $script = $script."ret_value+='<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">';";
        $script = $script."if(is_single){";
        $script = $script."ret_value+='<p>Select a file</p>';";
        $script = $script."ret_value+='<select id=\"selAttFile\" class=\"form-control\">';";
        $script = $script."ret_value+='<option id=\"0\">Select an available file</option>';";
        $script = $script."}else{";
        $script = $script."ret_value+='<p>Select files to include</p>';";
        $script = $script."ret_value+='<select id=\"selAttFile\" class=\"form-control\" multiple>';";
        $script = $script."}";
        $script = $script."docs.forEach(function(dc){";
        $script = $script."ret_value+='<option id=\"'+dc.id+'\">'+dc.filename+'</option>';";
        $script = $script."});";
        $script = $script."ret_value+='</select>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."}";
        $script = $script."ret_value+='<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnUseMultAtt\" class=\"btn btn-success\">Use Selected</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnUploadNew\" class=\"btn btn-info\">Upload New File</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // add document rows
        $script = $script."var addDocumentRows=function(tbody,rows){";
        $script = $script."if(rows && rows.length > 0){";
        $script = $script."tbody.html('');";
        $script = $script."for(var i=0;i<rows.length;i++){";
        $script = $script."tbody.append(rows[i]);";
        $script = $script."}";
        $script = $script."}";
        $script = $script."};";
        // view document file
        $script = $script."var buildViewDocument=function(name,filename,icon,added){";
        $script = $script."var ret_value=''";
        $script = $script."+'<div style=\"text-align:center\" id=\"title_dv\">'";
        $script = $script."+'<strong>Document for</strong>: '+name";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<img class=\"flimg\" src=\"'+icon+'\">'";
        $script = $script."+'<span class=\"flname\">'+filename+'</span>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<span>Added On: '+added+'</span>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div>'";
        $script = $script."+'<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-info\">Close</button>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        $script = $script."var buildMultiViewDocument=function(name,docs){";
        $script = $script."var ret_value=''";
        $script = $script."+'<div style=\"text-align:center\" id=\"title_dv\">'";
        $script = $script."+'<strong>Document for</strong>: '+name";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>';";
        $script = $script."for(var i=0;i<docs.length;i++){";
        $script = $script."ret_value+='<p>'";
        $script = $script."+'<img class=\"flimg\" src=\"'+docs[i].icon+'\">'";
        $script = $script."+'<span class=\"flname\"><strong>'+docs[i].filename+'</strong></span>'";
        $script = $script."+' <span>['+docs[i].date_added+']</span>'";
        $script = $script."+'</p>';";
        $script = $script."}";
        $script = $script."ret_value+='<hr>'";
        $script = $script."+'<div>'";
        $script = $script."+'<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-info\">Close</button>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // open edit team
        $script = $script."var openEditTeam = function(burl,team,users){";
        $script = $script."var meid='".$user_id."';";
        $script = $script."var ret_value='';";
        $script = $script."ret_value+='<p style=\"font-weight:700;\">Available Users</p>';";
        $script = $script."ret_value+='<div id=\"tblUserHolder\" style=\"height:100px;overflow:scroll;\">';";
        $script = $script."if(users.length > 0){";
        $script = $script."ret_value+='<table class=\"table table-striped\" id=\"tblUsers\">';";
        $script = $script."for(var i=0;i<users.length;i++){";
        $script = $script."ret_value+='<tr>';";
        $script = $script."ret_value+='<td><button class=\"btn btn-info btn-xs addteam\" type=\"button\" data=\"'+users[i].id+'\" title=\"Add Team Member\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></td>';";
        $script = $script."ret_value+='<td><img src=\"'+burl+users[i].image+'\" style=\"width:24px;height:24px;\"> '+users[i].name+'</td>';";
        $script = $script."ret_value+='<td>'+users[i].title+'</td>';";
        $script = $script."ret_value+='</tr>';";
        $script = $script."}";
        $script = $script."ret_value+='</table>';";
        $script = $script."}else{";
        $script = $script."ret_value+='<p>No Users Available: Invite More</p>';";
        $script = $script."}";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='<hr>';";
        $script = $script."ret_value+='<p style=\"font-weight:700;\">Team Members</p>';";
        $script = $script."ret_value+='<div id=\"tblTeamHolder\" style=\"height:100px;overflow:scroll;\">';";
        $script = $script."ret_value+='<table class=\"table table-striped\" id=\"tblTeam\">';";
        $script = $script."for(var i=0;i<team.length;i++){";
        $script = $script."var adtxt='Collab';";
        $script = $script."if(team[i].is_case_admin){adtxt='Lead';}";
        $script = $script."ret_value+='<tr>';";
        $script = $script."if(meid!=team[i].user_id){";
        $script = $script."ret_value+='<td><button class=\"btn btn-danger btn-xs remteam\" type=\"button\" data=\"'+team[i].user_id+'\" title=\"Remove Team Member\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
        $script = $script."}else{";
        $script = $script."ret_value+='<td>&nbsp;</td>';";
        $script = $script."}";
        $script = $script."ret_value+='<td><img src=\"'+burl+team[i].image+'\" style=\"width:24px;height:24px;\"> '+team[i].name+'</td>';";
        $script = $script."ret_value+='<td>'+team[i].title+'</td>';";
        $script = $script."if(meid!=team[i].user_id){";
        $script = $script."ret_value+='<td><a href=\"#\" class=\"ancedtrole\" data=\"'+team[i].name+'\" id=\"'+team[i].user_id+'\" title=\"Edit Role\">'+adtxt+'</a></td>';";
        $script = $script."}else{";
        $script = $script."ret_value+='<td>'+adtxt+'</td>';";
        $script = $script."}";
        $script = $script."ret_value+='</tr>';";
        $script = $script."}";
        $script = $script."ret_value+='</table>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='<hr>'";
        $script = $script."+'<div>'";
        $script = $script."+'<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // rebuild team rows modal
        $script = $script."var rebuildEditTeam = function(burl,team,users,teamobj,userobj){";
        $script = $script."var meid='".$user_id."';";
        $script = $script."teamobj.html('');";
        $script = $script."userobj.html('');";
        $script = $script."var user_value='';";
        $script = $script."if(users.length > 0){";
        $script = $script."user_value+='<table class=\"table table-striped\" id=\"tblUsers\">';";
        $script = $script."for(var i=0;i<users.length;i++){";
        $script = $script."user_value+='<tr>';";
        $script = $script."user_value+='<td><button class=\"btn btn-info btn-xs addteam\" type=\"button\" data=\"'+users[i].id+'\" title=\"Add Team Member\"><span class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span></button></td>';";
        $script = $script."user_value+='<td><img src=\"'+burl+users[i].image+'\" style=\"width:24px;height:24px;\"> '+users[i].name+'</td>';";
        $script = $script."user_value+='<td>'+users[i].title+'</td>';";
        $script = $script."user_value+='</tr>';";
        $script = $script."}";
        $script = $script."user_value+='</table>';";
        $script = $script."}else{";
        $script = $script."user_value+='<p>No Users Available: Invite More</p>';";
        $script = $script."}";
        $script = $script."userobj.html(user_value);";
        $script = $script."var team_value='';";
        $script = $script."team_value+='<table class=\"table table-striped\" id=\"tblTeam\">';";
        $script = $script."for(var i=0;i<team.length;i++){";
        $script = $script."var adtxt='Collab';";
        $script = $script."if(team[i].is_case_admin){adtxt='Lead';}";
        $script = $script."team_value+='<tr>';";
        $script = $script."if(meid!=team[i].user_id){";
        $script = $script."team_value+='<td><button class=\"btn btn-danger btn-xs remteam\" type=\"button\" data=\"'+team[i].user_id+'\" title=\"Remove Team Member\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
        $script = $script."}else{";
        $script = $script."team_value+='<td>&nbsp;</td>';";
        $script = $script."}";
        $script = $script."team_value+='<td><img src=\"'+burl+team[i].image+'\" style=\"width:24px;height:24px;\"> '+team[i].name+'</td>';";
        $script = $script."team_value+='<td>'+team[i].title+'</td>';";
        $script = $script."if(meid!=team[i].user_id){";
        $script = $script."team_value+='<td><a href=\"#\" class=\"ancedtrole\" data=\"'+team[i].name+'\" id=\"'+team[i].user_id+'\" title=\"Edit Role\">'+adtxt+'</a></td>';";
        $script = $script."}else{";
        $script = $script."team_value+='<td>'+adtxt+'</td>';";
        $script = $script."}";
        $script = $script."team_value+='</tr>';";
        $script = $script."}";
        $script = $script."team_value+='</table>';";
        $script = $script."teamobj.html(team_value);";
        $script = $script."};";
        // rebuild team rows app
        $script = $script."var rebuildTeamRows = function(burl,team,callback){";
        $script = $script."var team_value='';";
        $script = $script."for(var i=0;i<team.length;i++){";
        $script = $script."var adtxt='Collaborator';";
        $script = $script."if(team[i].is_case_admin){adtxt='Lead';}";
        $script = $script."team_value+='<tr>';";
        $script = $script."team_value+='<td><img src=\"'+burl+team[i].image+'\" style=\"width:24px;height:24px;\"> '+team[i].name+'</td>';";
        $script = $script."team_value+='<td>'+team[i].title+'</td>';";
        $script = $script."team_value+='<td>'+adtxt+'</td>';";
        $script = $script."team_value+='</tr>';";
        $script = $script."}";
        $script = $script."return callback(team_value,team.length);";
        $script = $script."};";
        // open edit case name
        $script = $script."var openEditCaseName = function(name){";
        $script = $script."var ret_value=''";
        $script = $script."+'<p>Enter New Name for: '+name+'</p>'";
        $script = $script."+'<form id=\"frmCaseName\" class=\"form-horizontal\" method=\"post\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgEditCName\" role=\"alert\"></div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"cname\" class=\"col-sm-2 control-label\">Case</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"cname\" id=\"cname\" value=\"\" placeholder=\"Case Name\">'";
        $script = $script."+'<span id=\"cnameres\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveCaseName\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // edit client
        $script = $script."var openEditClient = function(name,street,city,state,zip,phone,email){";

        $script = $script."if(!email){";
        $script = $script."email='';";
        $script = $script."}";
        $script = $script."if(!phone){";
        $script = $script."phone='';";
        $script = $script."}";

        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgClient\" role=\"alert\"></div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_sup\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_sup\" id=\"name_sup\" value=\"'+name+'\" placeholder=\"First and Last Name\">'";
        $script = $script."+'<span id=\"nameres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street_sup\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street_sup\" id=\"street_sup\" value=\"'+street+'\" placeholder=\"Street Address\">'";
        $script = $script."+'<span id=\"streetres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city_sup\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city_sup\" id=\"city_sup\" value=\"'+city+'\" placeholder=\"City\">'";
        $script = $script."+'<span id=\"cityres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<label for=\"state_sup\" class=\"col-sm-2 control-label\">State</label>'";
        $script = $script."+'<div class=\"col-sm-2\">'";
        $script = $script."+'<input type=\"text\" size=\"2\" class=\"form-control\" name=\"state_sup\" id=\"state_sup\" value=\"'+state+'\" placeholder=\"State\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"zip_sup\" class=\"col-sm-2 control-label\">Zipcode</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"zip_sup\" id=\"zip_sup\" value=\"'+zip+'\" placeholder=\"5 digit Zipcode\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"phone_sup\" class=\"col-sm-2 control-label\">Phone</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"phone_sup\" id=\"phone_sup\" value=\"'+phone+'\" placeholder=\"Telephone\">'";
        $script = $script."+'<span id=\"phoneres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"email_sup\" class=\"col-sm-2 control-label\">Email</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"email_sup\" id=\"email_sup\" value=\"'+email+'\" placeholder=\"Email\">'";
        $script = $script."+'<span id=\"emailres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveClient\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // edit supporting
        $script = $script."var openEditSupporting = function(name,title,street,city,state,zip,phone,email){";

        $script = $script."if(!email){";
        $script = $script."email='';";
        $script = $script."}";
        $script = $script."if(!phone){";
        $script = $script."phone='';";
        $script = $script."}";

        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgSup\" role=\"alert\"></div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_sup\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_sup\" id=\"name_sup\" value=\"'+name+'\" placeholder=\"First and Last Name\">'";
        $script = $script."+'<span id=\"nameres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_sup\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_sup\" id=\"title_sup\" value=\"'+title+'\" placeholder=\"Title\">'";
        $script = $script."+'<span id=\"titleres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street_sup\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street_sup\" id=\"street_sup\" value=\"'+street+'\" placeholder=\"Street Address\">'";
        $script = $script."+'<span id=\"streetres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city_sup\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city_sup\" id=\"city_sup\" value=\"'+city+'\" placeholder=\"City\">'";
        $script = $script."+'<span id=\"cityres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<label for=\"state_sup\" class=\"col-sm-2 control-label\">State</label>'";
        $script = $script."+'<div class=\"col-sm-2\">'";
        $script = $script."+'<input type=\"text\" size=\"2\" class=\"form-control\" name=\"state_sup\" id=\"state_sup\" value=\"'+state+'\" placeholder=\"State\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"zip_sup\" class=\"col-sm-2 control-label\">Zipcode</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"zip_sup\" id=\"zip_sup\" value=\"'+zip+'\" placeholder=\"5 digit Zipcode\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"phone_sup\" class=\"col-sm-2 control-label\">Phone</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"phone_sup\" id=\"phone_sup\" value=\"'+phone+'\" placeholder=\"Telephone\">'";
        $script = $script."+'<span id=\"phoneres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"email_sup\" class=\"col-sm-2 control-label\">Email</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"email_sup\" id=\"email_sup\" value=\"'+email+'\" placeholder=\"Email\">'";
        $script = $script."+'<span id=\"emailres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveEditSup\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // build interview index
        $script = $script."var openInterviewIndex = function(ints){";
        $script = $script."if(!ints){alert('No Interviews Found');return false;}";
        $script = $script."var ret_value='';";
        $script = $script."ret_value+='<div class=\"col-xs-12\">';";
        $script = $script."ret_value+='<div id=\"pnlMainCase\" class=\"pnl row\" style=\"margin-top:20px;padding-top:5px;\">';";
        $script = $script."ret_value+='<h3 style=\"margin:10px;font-weight:400;\">Interview Index</h3>';";
        $script = $script."ret_value+='<table class=\"table table-hover\">';";
        $script = $script."ret_value+='<thead>';";
        $script = $script."ret_value+='<tr>';";
        $script = $script."ret_value+='<th>Name <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_name\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Agent <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_agent\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Date Occured <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_date\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Approved <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_approved\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='</tr>';";
        $script = $script."ret_value+='</thead>';";
        $script = $script."ret_value+='<tbody id=\"tblInterviews\">';";
            $script = $script."for(var i=0;i<ints.length;i++){";
                $script = $script."var int=ints[i];";
                $script = $script."ret_value+='<tr>';";
                $script = $script."ret_value+='<td><a href=\"#\" class=\"ancintpop\" data=\"'+int.id+'\">'+int.name+'</a></td>';";
                $script = $script."ret_value+='<td>'+int.user_name+'</td>';";
                $script = $script."ret_value+='<td>'+int.date_occured+'</td>';";
                $script = $script."var isapp='False';";
                $script = $script."if(int.is_approved == '1'){";
                $script = $script."isapp='True';";
                $script = $script."}";
                $script = $script."ret_value+='<td>'+isapp+'</td>';";
                $script = $script."ret_value+='</tr>';";
            $script = $script."}";
        $script = $script."ret_value+='</tbody>';";
        $script = $script."ret_value+='</table>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // build attachment index
        $script = $script."var openAttachmentIndex = function(atts){";
        $script = $script."if(!atts){alert('No Attachments Found');return false;}";
        $script = $script."var ret_value='';";
        $script = $script."ret_value+='<div class=\"col-xs-12\">';";
        $script = $script."ret_value+='<div id=\"pnlMainCase\" class=\"pnl row\" style=\"margin-top:20px;padding-top:5px;\">';";
        $script = $script."ret_value+='<h3 style=\"margin:10px;font-weight:400;\">Attachment Index</h3>';";
        $script = $script."ret_value+='<table class=\"table table-hover\">';";
        $script = $script."ret_value+='<thead>';";
        $script = $script."ret_value+='<tr>';";
        $script = $script."ret_value+='<th>&nbsp;</th>';";
        $script = $script."ret_value+='<th>Type <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_type\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Attachment Number <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_attnum\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Date Obtained <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_date\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Agent <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_agent\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Source <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_source\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='</tr>';";
        $script = $script."ret_value+='</thead>';";
        $script = $script."ret_value+='<tbody id=\"tblAttachments\">';";
            $script = $script."for(var i=0;i<atts.length;i++){";
                $script = $script."var att=atts[i];";
                $script = $script."ret_value+='<tr>';";
                $script = $script."ret_value+='<td><img src=\"'+att.icon+'\" style=\"width:24px;height:24px;\"></td>';";
                $script = $script."ret_value+='<td>'+att.postfix+'</td>';";
                $script = $script."ret_value+='<td><a href=\"#\" class=\"anctags\" dataid=\"'+att.id+'\" dataname=\"'+att.number+'\" tags=\"'+att.tags+'\">'+att.number+'</a></td>';";
                $script = $script."ret_value+='<td>'+att.created+'</td>';";
                $script = $script."ret_value+='<td>'+att.username+'</td>';";
                $script = $script."ret_value+='<td>'+att.title+'</td>';";
                $script = $script."ret_value+='</tr>';";
            $script = $script."}";
        $script = $script."ret_value+='</tbody>';";
        $script = $script."ret_value+='</table>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // display tags
        $script = $script."var openDisplayTags = function(name,tags){";
        $script = $script."if(!name){alert('Invalid Attachment');return false;}";
        $script = $script."var ret_value='<p>Tags for '+name+'</p>';";
        $script = $script."ret_value+='<hr>';";
        $script = $script."ret_value+='<p>'+tags+'</p>';";
        $script = $script."ret_value+='<hr>';";
        $script = $script."ret_value+='<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
        $script = $script."return ret_value;";
        $script = $script."};";

        //START - interview rows sort
        // rebuild att ind rows
        $script = $script."var rebuildIntRows = function(ints,callback){";
            $script = $script."if(!ints){alert('No Attachments Found');return callback(false);}";
            $script = $script."var rows='';";
            $script = $script."for(var i=0;i<ints.length;i++){";
                $script = $script."var int=ints[i];";
                $script = $script."rows+='<tr>';";
                $script = $script."rows+='<td><a href=\"#\" class=\"ancintpop\" data=\"'+int.id+'\">'+int.name+'</a></td>';";
                $script = $script."rows+='<td>'+int.user_name+'</td>';";
                $script = $script."rows+='<td>'+int.date_occured+'</td>';";
                $script = $script."var isapp='False';";
                $script = $script."if(int.is_approved == '1'){";
                $script = $script."isapp='True';";
                $script = $script."}";
                $script = $script."rows+='<td>'+isapp+'</td>';";
                $script = $script."rows+='</tr>';";
            $script = $script."}";
            $script = $script."return callback(rows);";
        $script = $script."};";
        // manage sort attachments
        $script = $script."var sortInterviews = function(anc,ints,tbody){";
        $script = $script."if(!anc){alert('Invalid sort');return false;}";
        $script = $script."if(!ints){alert('No Interviews Found');return false;}";
        $script = $script."if(!tbody){alert('No Row Found');return false;}";
        $script = $script."var dir=anc.attr('sorted');";
        $script = $script."var new_dir='';";
            $script = $script."function genPropSortFunc(prop, reverse) {";
            $script = $script."return function (a, b) {";
            $script = $script."if (a[prop] < b[prop]) return reverse ? 1 : -1;";
            $script = $script."if (a[prop] > b[prop]) return reverse ? -1 : 1;";
            $script = $script."return 0;";
            $script = $script."};";
            $script = $script."}";
            $script = $script."switch(anc.attr('id')){";
                $script = $script."case 'ord_name':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."ints.sort(genPropSortFunc('name',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."ints.sort(genPropSortFunc('name',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_agent':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."ints.sort(genPropSortFunc('user_name',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."ints.sort(genPropSortFunc('user_name',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_date':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."ints.sort(genPropSortFunc('date_occured',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."ints.sort(genPropSortFunc('date_occured',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_approved':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."ints.sort(genPropSortFunc('is_approved',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."ints.sort(genPropSortFunc('is_approved',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
            $script = $script."}";
            // rebuild rows
            $script = $script."rebuildIntRows(ints,function(ret){";
                $script = $script."if(!ret){return false;}";
                $script = $script."tbody.html(ret);";
                // save new direction
                $script = $script."anc.attr('sorted',new_dir);";
                $script = $script."return true;";
            $script = $script."});";
        $script = $script."};";
        //END - interview rows sort

        // rebuild att ind rows
        $script = $script."var rebuildAttRows = function(atts,callback){";
            $script = $script."if(!atts){alert('No Attachments Found');return callback(false);}";
            $script = $script."var rows='';";
            $script = $script."for(var i=0;i<atts.length;i++){";
            $script = $script."var att=atts[i];";
            $script = $script."rows+='<tr>';";
            $script = $script."rows+='<td><img src=\"'+att.icon+'\" style=\"width:24px;height:24px;\"></td>';";
            $script = $script."rows+='<td>'+att.postfix+'</td>';";
            $script = $script."rows+='<td><a href=\"#\" class=\"anctags\" dataid=\"'+att.id+'\" dataname=\"'+att.number+'\" tags=\"'+att.tags+'\">'+att.number+'</a></td>';";
            $script = $script."rows+='<td>'+att.created+'</td>';";
            $script = $script."rows+='<td>'+att.username+'</td>';";
            $script = $script."rows+='<td>'+att.title+'</td>';";
            $script = $script."rows+='</tr>';";
            $script = $script."}";
            $script = $script."return callback(rows);";
        $script = $script."};";
        // manage sort attachments
        $script = $script."var sortAttachments = function(anc,atts,tbody){";
        $script = $script."if(!anc){alert('Invalid sort');return false;}";
        $script = $script."if(!atts){alert('No Attachments Found');return false;}";
        $script = $script."if(!tbody){alert('No Row Found');return false;}";
        $script = $script."var dir=anc.attr('sorted');";
        $script = $script."var new_dir='';";
            $script = $script."function genPropSortFunc(prop, reverse) {";
            $script = $script."return function (a, b) {";
            $script = $script."if (a[prop] < b[prop]) return reverse ? 1 : -1;";
            $script = $script."if (a[prop] > b[prop]) return reverse ? -1 : 1;";
            $script = $script."return 0;";
            $script = $script."};";
            $script = $script."}";
            $script = $script."switch(anc.attr('id')){";
                $script = $script."case 'ord_type':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."atts.sort(genPropSortFunc('postfix',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."atts.sort(genPropSortFunc('postfix',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_attnum':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."atts.sort(genPropSortFunc('number',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."atts.sort(genPropSortFunc('number',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_date':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."atts.sort(genPropSortFunc('created',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."atts.sort(genPropSortFunc('created',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_agent':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."atts.sort(genPropSortFunc('username',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."atts.sort(genPropSortFunc('username',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_source':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."atts.sort(genPropSortFunc('title',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."atts.sort(genPropSortFunc('title',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
            $script = $script."}";
            // rebuild rows
            $script = $script."rebuildAttRows(atts,function(ret){";
                $script = $script."if(!ret){return false;}";
                $script = $script."tbody.html(ret);";
                // save new direction
                $script = $script."anc.attr('sorted',new_dir);";
                $script = $script."return true;";
            $script = $script."});";
        $script = $script."};";
        // rebuild lead rows
        $script = $script."var rebuildLeadRows = function(leads,callback){";
            $script = $script."if(!leads){alert('No Attachments Found');return callback(false);}";
            $script = $script."var meid='".$user_id."';";
            $script = $script."var rows='';";
            $script = $script."for(var i=0;i<leads.length;i++){";
            $script = $script."var lead=leads[i];";
            $script = $script."rows+='<tr>';";
            $script = $script."rows+='<td>'+lead.number+'</td>';";
            $script = $script."rows+='<td><a href=\"#\" class=\"ancviewlead\" dataid=\"'+lead.id+'\" dataname=\"'+lead.title+'\" comments=\"'+lead.comments+'\">'+lead.title+'</a></td>';";
            $script = $script."rows+='<td>'+lead.source+'</td>';";
            $script = $script."rows+='<td>'+lead.assigned_to+'</td>';";
            $script = $script."rows+='<td>'+lead.date_assigned+'</td>';";
            $script = $script."var chk='';";
                // handle check / uncheck
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
            $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\">';";
            $script = $script."if(lead.is_complete == 1){";
            $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" checked>';";
            $script = $script."}";
}else{
            $script = $script."if(meid==lead.userid){";
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\">';";
                $script = $script."if(lead.is_complete == 1){";
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" checked>';";
                $script = $script."}";
            $script = $script."}else{";
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" disabled=\"disabled\">';";
                $script = $script."if(lead.is_complete == 1){";
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" disabled=\"disabled\" checked>';";
                $script = $script."}";
            $script = $script."}";
}
            $script = $script."rows+='<td>'+chk+'</td>';";
            $script = $script."rows+='</tr>';";
            $script = $script."}";
            $script = $script."return callback(rows);";
        $script = $script."};";
        // manage sort leads
        $script = $script."var sortLeads = function(anc,leads,tbody){";
        $script = $script."if(!anc){alert('Invalid sort');return false;}";
        $script = $script."if(!leads){alert('No Leads Found');return false;}";
        $script = $script."if(!tbody){alert('No Row Found');return false;}";
        $script = $script."var dir=anc.attr('sorted');";
        $script = $script."var new_dir='';";
            $script = $script."function genPropSortFunc(prop, reverse) {";
            $script = $script."return function (a, b) {";
            $script = $script."if (a[prop] < b[prop]) return reverse ? 1 : -1;";
            $script = $script."if (a[prop] > b[prop]) return reverse ? -1 : 1;";
            $script = $script."return 0;";
            $script = $script."};";
            $script = $script."}";
            $script = $script."switch(anc.attr('id')){";
                $script = $script."case 'ord_leadnumber':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('number',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('number',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_name':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('title',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('title',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_source':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('source',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('source',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_assto':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('assigned_to',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('assigned_to',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_dateass':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('date_assigned',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('date_assigned',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
                $script = $script."case 'ord_complete':";
                    $script = $script."if(dir=='za'){";
                    $script = $script."leads.sort(genPropSortFunc('is_complete',true));";
                    $script = $script."new_dir='az';";
                    $script = $script."}else{";
                    $script = $script."leads.sort(genPropSortFunc('is_complete',false));";
                    $script = $script."new_dir='za';";
                    $script = $script."}";
                $script = $script."break;";
            $script = $script."}";
            // rebuild rows
            $script = $script."rebuildLeadRows(leads,function(ret){";
                $script = $script."if(!ret){return false;}";
                $script = $script."tbody.html(ret);";
                // save new direction
                $script = $script."anc.attr('sorted',new_dir);";
                $script = $script."return true;";
            $script = $script."});";
        $script = $script."};";

        // build lead sheet
        $script = $script."var openLeadSheet = function(leads){";
        $script = $script."var meid='".$user_id."';";
        $script = $script."var ret_value='';";
        $script = $script."ret_value+='<div class=\"col-xs-12\">';";
        $script = $script."ret_value+='<div id=\"pnlMainAttachments\" class=\"pnl row\" style=\"margin-top:20px;padding-top:5px;\">';";
        $script = $script."ret_value+='<h3 style=\"margin:10px;font-weight:400;\">Lead Sheet <button type=\"button\" id=\"btnAddLead\" class=\"btn btn-success btn-xs\">Add New Lead</button></h3>';";
        $script = $script."ret_value+='<table class=\"table table-hover\">';";
        $script = $script."ret_value+='<thead>';";
        $script = $script."ret_value+='<tr>';";
        $script = $script."ret_value+='<th>Lead Number <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_leadnumber\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Name <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_name\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Source <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_source\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Assigned To <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_assto\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Date Assigned <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_dateass\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='<th>Is Complete <a href=\"#\" class=\"ordsort\" sorted=\"\" id=\"ord_complete\" title=\"Re-Order\"><span class=\"glyphicon glyphicon-sort\" aria-hidden=\"true\"></span></a></th>';";
        $script = $script."ret_value+='</tr>';";
        $script = $script."ret_value+='</thead>';";
        $script = $script."ret_value+='<tbody id=\"tblLeadSheet\">';";
            $script = $script."for(var i=0;i<leads.length;i++){";
                $script = $script."var lead=leads[i];";
                $script = $script."ret_value+='<tr>';";
                $script = $script."ret_value+='<td>'+lead.number+'</td>';";
                $script = $script."ret_value+='<td><a href=\"#\" class=\"ancviewlead\" dataid=\"'+lead.id+'\" dataname=\"'+lead.title+'\" comments=\"'+lead.comments+'\">'+lead.title+'</a></td>';";
                $script = $script."ret_value+='<td>'+lead.source+'</td>';";
                $script = $script."ret_value+='<td>'+lead.assigned_to+'</td>';";
                $script = $script."ret_value+='<td>'+lead.date_assigned+'</td>';";
                $script = $script."var chk='';";
                // handle check / uncheck
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\">';";
                $script = $script."if(lead.is_complete == 1){";
                $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" checked>';";
                $script = $script."}";
}else{
                $script = $script."if(meid==lead.userid){";
                    $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\">';";
                    $script = $script."if(lead.is_complete == 1){";
                    $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" checked>';";
                    $script = $script."}";
                $script = $script."}else{";
                    $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" disabled=\"disabled\">';";
                    $script = $script."if(lead.is_complete == 1){";
                    $script = $script."chk='<input type=\"checkbox\" id=\"'+lead.id+'\" class=\"chkcomplete\" disabled=\"disabled\" checked>';";
                    $script = $script."}";
                $script = $script."}";
}
                $script = $script."ret_value+='<td>'+chk+'</td>';";
                $script = $script."ret_value+='</tr>';";
            $script = $script."}";
        $script = $script."ret_value+='</tbody>';";
        $script = $script."ret_value+='</table>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."ret_value+='</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // lead view
        $script = $script."var openLeadData = function(name,text){";
        $script = $script."var ret_value='';";
        $script = $script."if(!name){alert('No Lead Data');return false;}";
        $script = $script."var ret_value='<p>Data for '+name+'</p>';";
        $script = $script."ret_value+='<hr>';";
        $script = $script."ret_value+='<p>'+text+'</p>';";
        $script = $script."ret_value+='<hr>';";
        $script = $script."ret_value+='<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // new lead entry
        $script = $script."var openNewLead = function(){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgLead\" role=\"alert\"></div>'";
        // name
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_le\" class=\"col-sm-3 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_le\" id=\"name_le\" value=\"\" placeholder=\"Lead Name\">'";
        $script = $script."+'<span id=\"nameres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // source
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"source_le\" class=\"col-sm-3 control-label\">Source</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"source_le\" id=\"source_le\" value=\"\" placeholder=\"Lead Source\">'";
        $script = $script."+'<span id=\"sourceres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // assigned_to
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"assignedto_le\" class=\"col-sm-4 control-label\">Assigned To</label>'";
        $script = $script."+'<div class=\"col-sm-8\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<select class=\"form-control\" id=\"assignedto_le\">'";
        $script = $script."+'<option value=\"\" data=\"\">Please Select</option>'";
if(isset($team) && count($team) > 0) {
    foreach ($team as $mem) {
        $script = $script."+'<option value=\"".md5($mem['user_id'])."\">".$mem['name']."</option>'";
    }
}
        $script = $script."+'</select>'";
        $script = $script."+'<span id=\"assignedtores_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // date_assigned
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"date_le\" class=\"col-sm-4 control-label\">Date Assigned</label>'";
        $script = $script."+'<div class=\"col-sm-8\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"date\" class=\"form-control\" name=\"dateass_le\" id=\"dateass_le\" value=\"\" max=\"".date('Y-m-d')."\" data=\"".date('m/d/Y')."\" placeholder=\"\">'";
        $script = $script."+'<span id=\"dateassres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // comments
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"notes\" class=\"col-sm-3 control-label\">Notes</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<textarea class=\"form-control\" name=\"notes_le\" id=\"notes_le\" value=\"\" placeholder=\"Notes and Comments\" rows=\"3\"></textarea>'";
        $script = $script."+'<span id=\"notesres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // is_completed
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_sup\" class=\"col-sm-3 control-label\">&nbsp;</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"checkbox\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"checkbox\" class=\"chkIsCompleted\" id=\"chkIsCompleted\" value=\"1\">'";
        $script = $script."+'Is Completed'";
        $script = $script."+'</label>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnAddNewLead\" class=\"btn btn-success\">Add</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // end lead entry

        // CASE
        // panel and buttons for show hide info panels
        $script = $script."var btnInterviews = $('#btnInterviews');";
        $script = $script."var pnlInterviews = $('#pnlInterviews');";
        $script = $script."var btnAttachments = $('#btnAttachments');";
        $script = $script."var pnlAttachments = $('#pnlAttachments');";
        $script = $script."var btnTeam = $('#btnTeam');";
        $script = $script."var pnlTeam = $('#pnlTeam');";
        // hide panels by default
        $script = $script."pnlInterviews.hide();";
        $script = $script."pnlAttachments.hide();";
        $script = $script."pnlTeam.hide();";
        // manage open/close
        $script = $script."btnInterviews.on('click', function(){";
        $script = $script."if(pnlInterviews.is(':visible')){";
        $script = $script."btnInterviews.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlInterviews.hide();";
        $script = $script."$('#udInterviews').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnInterviews.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlInterviews.show();";
        $script = $script."$('#udInterviews').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."btnAttachments.on('click', function(){";
        $script = $script."if(pnlAttachments.is(':visible')){";
        $script = $script."btnAttachments.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlAttachments.hide();";
        $script = $script."$('#udAttachments').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnAttachments.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlAttachments.show();";
        $script = $script."$('#udAttachments').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."btnTeam.on('click', function(){";
        $script = $script."if(pnlTeam.is(':visible')){";
        $script = $script."btnTeam.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlTeam.hide();";
        $script = $script."$('#udTeam').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnTeam.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlTeam.show();";
        $script = $script."$('#udTeam').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."});";
        // handle open close case
        $script = $script."$('#btnOpen').on('click',function(){";
        $script = $script."var btn=$(this);";
        $script = $script."if(btn.hasClass('active')){return false;}";
        $script = $script."$('#btnOpen').removeClass('active');";
        $script = $script."$('#btnClosed').removeClass('active');";
        $script = $script."var data = {cid:'".$case_id."',isclosed:false};";
        $script = $script."postData('".site_url("edit/open_close_case")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."$('#btnOpen').removeClass('active').addClass('active');";
        $script = $script."$('#btnClosed').removeClass('active');";
        $script = $script."}else{";
        $script = $script."$('#btnOpen').removeClass('active');";
        $script = $script."$('#btnClosed').removeClass('active').addClass('active');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."$('#btnClosed').on('click',function(){";
        $script = $script."var btn=$(this);";
        $script = $script."if(btn.hasClass('active')){return false;}";
        $script = $script."$('#btnOpen').removeClass('active');";
        $script = $script."$('#btnClosed').removeClass('active');";
        $script = $script."var data = {cid:'".$case_id."',isclosed:true};";
        $script = $script."postData('".site_url("edit/open_close_case")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."$('#btnOpen').removeClass('active');";
        $script = $script."$('#btnClosed').removeClass('active').addClass('active');";
        $script = $script."}else{";
        $script = $script."$('#btnOpen').removeClass('active').addClass('active');";
        $script = $script."$('#btnClosed').removeClass('active');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // edit team
        $script = $script."$('#btnAddTeam,#btnAddNewTeam,#ancAddTeamMember').on('click',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cmid='".md5($company_id)."';";
        $script = $script."var burl='".$base_url."img/user/';";
        $script = $script."var data = {compid:cmid,caseid:cid};";
        $script = $script."postData('".site_url("data/team_and_users")."',data,null,function(res){";
        $script = $script."if(res.result){";
        $script = $script."openModal('Edit Team',openEditTeam(burl,res.team,res.users));";
        $script = $script."}else{";
        $script = $script."alert('An Error Occured: '+res.msg);";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // edit team controls
        // remove from team
        $script = $script."$(document).on('click', '.remteam', function(){";
        $script = $script."var uid=$(this).attr('data');";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cmid='".md5($company_id)."';";
        $script = $script."var burl='".$base_url."img/user/';";
        $script = $script."var data = {userid:uid,caseid:cid};";
        $script = $script."postData('".site_url("edit/remove_user_team")."',data,null,function(res){";
            $script = $script."if(res.result){";
                // reload rows
                $script = $script."var data = {compid:cmid,caseid:cid};";
                $script = $script."postData('".site_url("data/team_and_users")."',data,null,function(resp){";
                $script = $script."if(resp.result){"; 
                    $script = $script."rebuildEditTeam(burl,resp.team,resp.users,$('#tblTeamHolder'),$('#tblUserHolder'));";
                    $script = $script."rebuildTeamRows(burl,resp.team,function(rows,count){";
                    $script = $script."$('#memCount').html(count+' Member(s)');";
                    $script = $script."$('#viewTeam').html(count);";
                    $script = $script."$('#tbodySupDocsPanel').html(rows);";
                    $script = $script."$('#tbodySupDocsMain').html(rows);";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+resp.msg);";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."}else{";
            $script = $script."alert('An Error Occured: '+res.msg);";
            $script = $script."return false;";
            $script = $script."}";
        $script = $script."});";
        $script = $script."";
        $script = $script."});";
        // add user to team - addteam
        $script = $script."$(document).on('click', '.addteam', function(){";
        $script = $script."var uid=$(this).attr('data');";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cmid='".md5($company_id)."';";
        $script = $script."var burl='".$base_url."img/user/';";
        $script = $script."var data = {userid:uid,caseid:cid,admin:false};";
        $script = $script."postData('".site_url("add/add_team_member")."',data,null,function(res){";
            $script = $script."if(res.result){";
                // reload rows
                $script = $script."var data = {compid:cmid,caseid:cid};";
                $script = $script."postData('".site_url("data/team_and_users")."',data,null,function(resp){";
                $script = $script."if(resp.result){";
                    $script = $script."rebuildEditTeam(burl,resp.team,resp.users,$('#tblTeamHolder'),$('#tblUserHolder'));";
                    $script = $script."rebuildTeamRows(burl,resp.team,function(rows,count){";
                    $script = $script."$('#memCount').html(count+' Member(s)');";
                    $script = $script."$('#viewTeam').html(count);";
                    $script = $script."$('#tbodySupDocsPanel').html(rows);";
                    $script = $script."$('#tbodySupDocsMain').html(rows);";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+resp.msg);";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."}else{";
            $script = $script."alert('An Error Occured: '+res.msg);";
            $script = $script."return false;";
            $script = $script."}";
        $script = $script."});";
        $script = $script."";
        $script = $script."});";
        // change role
        $script = $script."$(document).on('click', '.ancedtrole', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var uid=$(this).attr('id');";
        $script = $script."var uname=$(this).attr('data');";
        $script = $script."var edtcntrl='<td colspan=\"3\">';";
        $script = $script."var selid='rsel_'+uid;";
        $script = $script."edtcntrl+='<span>'+uname+'</span>';";
        $script = $script."edtcntrl+='&nbsp;<select id=\"'+selid+'\">';";
        $script = $script."edtcntrl+='<option value=\"\">Select Role</option>';";
        $script = $script."edtcntrl+='<option value=\"1\">Lead</option>';";
        $script = $script."edtcntrl+='<option value=\"0\">Collaborator</option>';";
        $script = $script."edtcntrl+='</select>';";
        $script = $script."edtcntrl+='</td>';";
        $script = $script."edtcntrl+='<td>';";
        $script = $script."edtcntrl+='<button type=\"button\" data=\"'+uid+'\" class=\"btn btn-info btn-xs saverole\" title=\"Save Role\">Save</button>';";
        $script = $script."edtcntrl+=' <button type=\"button\" class=\"btn btn-danger btn-xs cancelrole\" title=\"Cancel\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button>';";
        $script = $script."edtcntrl+='</td>';";
        $script = $script."var oldrow = $(this).parents('td').parents('tr').html();";
        $script = $script."var row=$(this).parents('td').parents('tr').html(edtcntrl);";
            // cancel save role
            $script = $script."$('.cancelrole').on('click',function(e){";
            $script = $script."e.preventDefault();";
            $script = $script."$(this).parents('td').parents('tr').html(oldrow);";
            $script = $script."});";
            // save role
            $script = $script."$('.saverole').on('click',function(e){";
            $script = $script."e.preventDefault();";
            $script = $script."var uid=$(this).attr('data');";
            $script = $script."var isadmin=$('#rsel_'+uid).val();";
            $script = $script."var cid='".$case_id."';";
            $script = $script."var cmid='".md5($company_id)."';";
            $script = $script."var burl='".$base_url."img/user/';";
            $script = $script."if(!isadmin){";
            $script = $script."alert('Please select a Role.');";
            $script = $script."return false;";
            $script = $script."}";
                $script = $script."var data = {userid:uid,caseid:cid,admin:isadmin};";
                $script = $script."postData('".site_url("edit/change_user_case_role")."',data,null,function(res){";
                $script = $script."if(res.result){";
                    // reload rows
                    $script = $script."var data = {compid:cmid,caseid:cid};";
                    $script = $script."postData('".site_url("data/team_and_users")."',data,null,function(resp){";
                    $script = $script."if(resp.result){";
                        $script = $script."rebuildEditTeam(burl,resp.team,resp.users,$('#tblTeamHolder'),$('#tblUserHolder'));";
                        $script = $script."rebuildTeamRows(burl,resp.team,function(rows,count){";
                        $script = $script."$('#memCount').html(count+' Member(s)');";
                        $script = $script."$('#viewTeam').html(count);";
                        $script = $script."$('#tbodySupDocsPanel').html(rows);";
                        $script = $script."$('#tbodySupDocsMain').html(rows);";
                        $script = $script."});";
                    $script = $script."}else{";
                    $script = $script."alert('An Error Occured: '+resp.msg);";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+res.msg);";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."});";
        $script = $script."});";
        // interview index view
        $script = $script."$('#btnIntInd').on('click',function(e){";
        $script = $script."e.preventDefault();";
            // load interviews
            $script = $script."var cid='".$case_id."';";
            $script = $script."var data = {caseid:cid};";
            $script = $script."postData('".site_url("data/interviews")."',data,null,function(res){";
                $script = $script."if(res.result){";
                    $script = $script."var url=document.location.protocol+'//'+document.location.hostname+document.location.pathname;";
                    $script = $script."breadcrumb_change(case_name,url,'Interview Index');";
                    $script = $script."$('#contents').html(openInterviewIndex(res.interviews));";
                    // handle sort and reload rows
                    $script = $script."$('.ordsort').on('click',function(e){";
                    $script = $script."e.preventDefault();";
                    $script = $script."sortInterviews($(this),res.interviews,$('#tblInterviews'));";
                    $script = $script."});";
                    // handle view attachment
                    $script = $script."$(document).on('click','.ancintpop',function(e){";
                    $script = $script."e.preventDefault();";
                    $script = $script."var iid=$(this).attr('data');";
                    // load interview data and show in popup modal

                        $script = $script."var data = {intid:iid};";
                        $script = $script."postData('".site_url("data/interview")."',data,null,function(res){";
                            $script = $script."if(res.result){";
                                
                                $script = $script."var interview = res.interview;";

                                // load interview data and show in popup modal
                                $script = $script."var body='<p><strong>Description:</strong></p>';";
                                $script = $script."body+='<p>'+interview.description+'</p>';";
                                $script = $script."body+='<hr>';";
                                $script = $script."body+='<p><strong>Attachments:</strong></p>';";
                                $script = $script."body+='<table id=\"tblIntPop\" class=\"table table-hover\">';";
                                $script = $script."body+='<thead>';";
                                $script = $script."body+='<tr>';";
                                $script = $script."body+='<th>Type</th>';";
                                $script = $script."body+='<th>Number</th>';";
                                $script = $script."body+='<th>Obtained</th>';";
                                $script = $script."body+='</tr>';";
                                $script = $script."body+='</thead>';";
                                $script = $script."body+='<tbody id=\"tblBodyIntPop\">';";

                                //interview.attachments
                                $script = $script."for(var i=0;i<interview.attachments.length;i++){";
                                    $script = $script."var doc=interview.attachments[i];";
                                    $script = $script."var row='<tr>';";
                                    $script = $script."body += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
                                    $script = $script."body += '<td>'+doc.number+'</td>';";
                                    $script = $script."var dt=new Date(doc.created);";
                                    $script = $script."body += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
                                    $script = $script."body += '</tr>';";
                                $script = $script."}";

                                $script = $script."body+='</tbody>';";
                                $script = $script."body+='</table>';";

                                $script = $script."body+='<hr>';";
                                $script = $script."body+='<button id=\"btnCloseWin\" class=\"btn btn-default\" type=\"button\">Close</button>';";

                                $script = $script."openModal(interview.name,body);";
                                
                            $script = $script."}else{";
                            $script = $script."alert('An Error Occured: '+res.msg);";
                            $script = $script."return false;";
                            $script = $script."}";
                        $script = $script."});";

                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+res.msg);";
                $script = $script."return false;";
                $script = $script."}";
            $script = $script."});";
        $script = $script."});";
        // attachment index view
        $script = $script."$('#btnAttInd').on('click',function(e){";
        $script = $script."e.preventDefault();";
            // load attachments
            $script = $script."var cid='".$case_id."';";
            $script = $script."var data = {caseid:cid};";
            $script = $script."postData('".site_url("data/attachments")."',data,null,function(res){";
            $script = $script."if(res.result){";
            $script = $script."var url=document.location.protocol+'//'+document.location.hostname+document.location.pathname;";
            $script = $script."breadcrumb_change(case_name,url,'Attachment Index');";
            $script = $script."$('#contents').html(openAttachmentIndex(res.docs));";
                // handle sort and reload rows
                $script = $script."$('.ordsort').on('click',function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."sortAttachments($(this),res.docs,$('#tblAttachments'));";
                $script = $script."});";
                // handle view attachment
                $script = $script."$(document).on('click','.anctags',function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."var nme=$(this).attr('dataname');";
                $script = $script."var tags=$(this).attr('tags');";
                $script = $script."openModal(nme,openDisplayTags(nme,tags));";
                $script = $script."});";
            $script = $script."}else{";
            $script = $script."alert('An Error Occured: '+res.msg);";
            $script = $script."return false;";
            $script = $script."}";
            $script = $script."});";
        $script = $script."});";
        // leadsheet
        $script = $script."$('#btnLeadSheet').on('click',function(e){";
        $script = $script."e.preventDefault();";
            $script = $script."var rows;";
            $script = $script."var cid='".$case_id."';";
            $script = $script."var data = {caseid:cid};";
            $script = $script."postData('".site_url("data/leadsheet")."',data,null,function(res){";
            $script = $script."if(res.result){";
                $script = $script."var url=document.location.protocol+'//'+document.location.hostname+document.location.pathname;";
                $script = $script."breadcrumb_change(case_name,url,'Lead Sheet');";
                $script = $script."rows=res.leads;";
                $script = $script."$('#contents').html(openLeadSheet(rows));";
                // handle sort
                $script = $script."$('.ordsort').on('click',function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."sortLeads($(this),rows,$('#tblLeadSheet'));";
                $script = $script."});";
                // handle mark complete / uncomplete
                $script = $script."$(document).on('click','.chkcomplete',function(e){";
                    $script = $script."var lid=$(this).attr('id');";
                    $script = $script."var is_complete=false;";
                    $script = $script."if($(this).is(':checked') == true){";
                    $script = $script."is_complete=true;";
                    $script = $script."}";
                        $script = $script."var data = {leadid:lid,complete:is_complete};";
                        $script = $script."postData('".site_url("edit/mark_lead_complete")."',data,null,function(res){";
                        $script = $script."if(res.result){";
                            // reload rows
                            $script = $script."var data = {caseid:cid};";
                            $script = $script."postData('".site_url("data/leadsheet")."',data,null,function(res){";
                            $script = $script."if(res.result){";
                                // handle last sort
                                $script = $script."rows=res.leads;";
                                // rebuild rows
                                $script = $script."rebuildLeadRows(rows,function(ret){";
                                    $script = $script."if(!ret){return false;}";
                                    $script = $script."$('#tblLeadSheet').html(ret);";
                                    $script = $script."return true;";
                                $script = $script."});";
                            $script = $script."}else{";
                            $script = $script."alert('An Error Occured: '+res.msg);";
                            $script = $script."return false;";
                            $script = $script."}";
                            $script = $script."});";
                        $script = $script."}else{";
                        $script = $script."alert('An Error Occured: '+res.msg);";
                        $script = $script."return false;";
                        $script = $script."}";
                        $script = $script."});";
                $script = $script."});";
                // handle view Comments
                $script = $script."$(document).on('click','.ancviewlead',function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."var text=$(this).attr('comments');";
                $script = $script."var nme=$(this).attr('dataname');";
                $script = $script."openModal(nme,openLeadData(nme,text));";
                $script = $script."});";
                // add new lead
                $script = $script."$('#btnAddLead').on('click',function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."var btn=$(this);";
                $script = $script."var cid='".$case_id."';";
                $script = $script."openModal('Add New Lead',openNewLead());";
                $script = $script."$('#altMsgLead').hide();";
                // validate name
                $script = $script."var name=$('#name_le');";
                $script = $script."var nameres=$('#nameres_le');";
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
                // validate source
                $script = $script."var source=$('#source_le');";
                $script = $script."var sourceres=$('#sourceres_le');";
                $script = $script."source.on('blur keyup focus', function() {";
                $script = $script."if(source.val().length == 0){";
                $script = $script."sourceres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."}else{";
                $script = $script."if(source.val().trim().indexOf(' ') != -1){";
                $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}else{";
                $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."}";
                $script = $script."});";
                // validate assigned to
                $script = $script."var assignedto=$('#assignedto_le');";
                $script = $script."var assignedtores=$('#assignedtores_le');";
                $script = $script."assignedto.on('change', function(){";
                $script = $script."if(assignedto.val().length == 0){";
                $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}else if (assignedto.val().length > 0){";
                $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}";
                $script = $script."});";
                // validate date assigned
                $script = $script."var dateass=$('#dateass_le');";
                $script = $script."var dateassres=$('#dateassres_le');";
                $script = $script."if (supportsHTML5Date() == false){";
                $script = $script."if(dateass.val().length == 0){";
                $script = $script."dateass.val(dateass.attr('data'));";
                $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}";
                $script = $script."dateass.on('blur keyup focus', function() {";
                $script = $script."if(dateass.val().length == 0){";
                $script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."$('#viewCreationDate').html('');";
                $script = $script."}else if(checkdate(dateass)){";
                $script = $script."var selDate = new Date(dateass.val()+' UTC');";
                $script = $script."var curDate = Date.now();";
                $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                //$script = $script."if (selDate.getTime() <= curDate) {";
                //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                //$script = $script."}else{";
                //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                //$script = $script."}";
                $script = $script."}else{";
                $script = $script."dateassres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}";
                //handle date picker validation
                $script = $script."if (supportsHTML5Date() == true){";
                $script = $script."dateass.on('change blur keyup focus', function(){";
                $script = $script."var dt=new Date(dateass.val());";
                $script = $script."if(isNaN(dt.getTime())){";
                $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}else{";
                $script = $script."var curDate = Date.now();";
                $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                //$script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                //$script = $script."if (dt.getTime() <= curDate) {";
                //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                //$script = $script."}else{";
                //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                //$script = $script."}";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}";
                // validate notes
                $script = $script."var notes=$('#notes_le');";
                $script = $script."var notesres=$('#notesres_le');";
                $script = $script."notes.on('blur keyup focus', function(){";
                $script = $script."$('#altMsgLead').removeClass('alert-danger').html('').hide();";
                $script = $script."if(notes.val().length == 0){";
                $script = $script."notesres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."}else{";
                $script = $script."if((notes.val().trim().indexOf(' ') != -1) && (notes.val().length > 10)){";
                $script = $script."notesres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}else{";
                $script = $script."notesres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."}";
                $script = $script."});";
                // add lead entry - save form to local storage for now
                $script = $script."$('#btnAddNewLead').on('click', function() {";
                $script = $script."$('#altMsgLead').removeClass('alert-danger').html('').hide();";
                $script = $script."var validName = nameres.hasClass('pw_strong');";
                $script = $script."var validSource = sourceres.hasClass('pw_strong');";
                $script = $script."var validAssTo = assignedtores.hasClass('pw_strong');";
                $script = $script."var validDateAss = dateassres.hasClass('pw_strong');";
                $script = $script."var validNotes = notesres.hasClass('pw_strong');";
                $script = $script."if (validName && validSource && validAssTo && validDateAss && validNotes) {";
                    // save to DB - for reuse we add to an array
                    $script = $script."var iscomp=$('#chkIsCompleted:checked').val();";
                    $script = $script."var leadsheet=[];";
                    $script = $script."var entry={};";
                    $script = $script."entry.name = name.val();";
                    $script = $script."entry.source = source.val();";
                    //$script = $script."entry.asto = assignedto.val();";
                    $script = $script."entry.astoid = assignedto.val();";
                    $script = $script."entry.asdt = dateass.val();";
                    $script = $script."entry.notes = '';";
                    $script = $script."if(notes){";
                    $script = $script."entry.notes = notes.val();";
                    $script = $script."}";
                    $script = $script."if(iscomp && iscomp==true){";
                    $script = $script."entry.iscomp=true";
                    $script = $script."}else{";
                    $script = $script."entry.iscomp=false;";
                    $script = $script."}";
                    $script = $script."leadsheet.push(entry);";
                    $script = $script."var cid='".$case_id."';";
                    $script = $script."var data = {caseid:cid,leads:leadsheet};";
                    $script = $script."postData('".site_url("add/add_lead_entries")."',data,btn,function(res){";
                    $script = $script."if(res.result){";
                        // reload rows
                        $script = $script."var cid='".$case_id."';";
                        $script = $script."var data = {caseid:cid};";
                        $script = $script."postData('".site_url("data/leadsheet")."',data,null,function(res){";
                        $script = $script."if(res.result){";
                                // handle last sort
                                $script = $script."rows=res.leads;";
                                // rebuild rows
                                $script = $script."rebuildLeadRows(rows,function(ret){";
                                    $script = $script."if(!ret){return false;}";
                                    $script = $script."$('#tblLeadSheet').html(ret);";
                                    $script = $script."$('#btnCloseWin').click();";
                                $script = $script."});";
                        $script = $script."}else{";
                        $script = $script."$('#altMsgLead').addClass('alert-danger').html('Unable to reload leads.').show();";
                        $script = $script."}";
                        $script = $script."});";
                    $script = $script."}else{";
                    $script = $script."$('#altMsgLead').addClass('alert-danger').html('Unable to save lead.').show();";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."$('#altMsgLead').addClass('alert-danger').html('Valid lead entry required.').show();";
                $script = $script."}";
                $script = $script."});";
                // end validate
                $script = $script."});";
            $script = $script."}else{";
                // determine if error is for no data
                $script = $script."if(res.msg == 'Unable to load leadsheet.'){";
                $script = $script."$('#ancLeadSheet').click();";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+res.msg);";
                $script = $script."return false;";
                $script = $script."}";
            $script = $script."}";
            $script = $script."});";
        $script = $script."});";
        // add lead without lead sheet
        $script = $script."$('#ancLeadSheet').on('click',function(e){";
        $script = $script."e.preventDefault();";
            // start validate
            $script = $script."var cid='".$case_id."';";
            $script = $script."openModal('Add New Lead',openNewLead());";
            $script = $script."$('#altMsgLead').hide();";
            // validate name
            $script = $script."var name=$('#name_le');";
            $script = $script."var nameres=$('#nameres_le');";
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
            // validate source
            $script = $script."var source=$('#source_le');";
            $script = $script."var sourceres=$('#sourceres_le');";
            $script = $script."source.on('blur keyup focus', function() {";
            $script = $script."if(source.val().length == 0){";
            $script = $script."sourceres.html('').removeClass('pw_strong').removeClass('pw_weak');";
            $script = $script."}else{";
            $script = $script."if(source.val().trim().indexOf(' ') != -1){";
            $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}else{";
            $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}";
            $script = $script."}";
            $script = $script."});";
            // validate assigned to
            $script = $script."var assignedto=$('#assignedto_le');";
            $script = $script."var assignedtores=$('#assignedtores_le');";
            $script = $script."assignedto.on('change', function(){";
            $script = $script."if(assignedto.val().length == 0){";
            $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}else if (assignedto.val().length > 0){";
            $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}";
            $script = $script."});";
            // validate date assigned
            $script = $script."var dateass=$('#dateass_le');";
            $script = $script."var dateassres=$('#dateassres_le');";
            $script = $script."if (supportsHTML5Date() == false){";
            $script = $script."if(dateass.val().length == 0){";
            $script = $script."dateass.val(dateass.attr('data'));";
            $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}";
            $script = $script."dateass.on('blur keyup focus', function() {";
            $script = $script."if(dateass.val().length == 0){";
            $script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
            $script = $script."$('#viewCreationDate').html('');";
            $script = $script."}else if(checkdate(dateass)){";
            $script = $script."var selDate = new Date(dateass.val()+' UTC');";
            $script = $script."var curDate = Date.now();";
            $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            //$script = $script."if (selDate.getTime() <= curDate) {";
            //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            //$script = $script."}else{";
            //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            //$script = $script."}";
            $script = $script."}else{";
            $script = $script."dateassres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}";
            $script = $script."});";
            $script = $script."}";
            //handle date picker validation
            $script = $script."if (supportsHTML5Date() == true){";
            $script = $script."dateass.on('change blur keyup focus', function(){";
            $script = $script."var dt=new Date(dateass.val());";
            $script = $script."if(isNaN(dt.getTime())){";
            $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}else{";
            $script = $script."var curDate = Date.now();";
            $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            //$script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
            //$script = $script."if (dt.getTime() <= curDate) {";
            //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            //$script = $script."}else{";
            //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            //$script = $script."}";
            $script = $script."}";
            $script = $script."});";
            $script = $script."}";
            // validate notes
            $script = $script."var notes=$('#notes_le');";
            $script = $script."var notesres=$('#notesres_le');";
            $script = $script."notes.on('blur keyup focus', function(){";
            $script = $script."$('#altMsgLead').removeClass('alert-danger').html('').hide();";
            $script = $script."if(notes.val().length == 0){";
            $script = $script."notesres.html('').removeClass('pw_strong').removeClass('pw_weak');";
            $script = $script."}else{";
            $script = $script."if((notes.val().trim().indexOf(' ') != -1) && (notes.val().length > 10)){";
            $script = $script."notesres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
            $script = $script."}else{";
            $script = $script."notesres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
            $script = $script."}";
            $script = $script."}";
            $script = $script."});";
            // add lead entry - save form to local storage for now
            $script = $script."$('#btnAddNewLead').on('click', function() {";
            $script = $script."$('#altMsgLead').removeClass('alert-danger').html('').hide();";
            $script = $script."var validName = nameres.hasClass('pw_strong');";
            $script = $script."var validSource = sourceres.hasClass('pw_strong');";
            $script = $script."var validAssTo = assignedtores.hasClass('pw_strong');";
            $script = $script."var validDateAss = dateassres.hasClass('pw_strong');";
            $script = $script."var validNotes = notesres.hasClass('pw_strong');";
            $script = $script."if (validName && validSource && validAssTo && validDateAss && validNotes) {";
                // save to DB - for reuse we add to an array
                $script = $script."var iscomp=$('#chkIsCompleted:checked').val();";
                $script = $script."var leadsheet=[];";
                $script = $script."var entry={};";
                $script = $script."entry.name = name.val();";
                $script = $script."entry.source = source.val();";
                //$script = $script."entry.asto = assignedto.val();";
                $script = $script."entry.astoid = assignedto.val();";
                $script = $script."entry.asdt = dateass.val();";
                $script = $script."entry.notes = '';";
                $script = $script."if(notes){";
                $script = $script."entry.notes = notes.val();";
                $script = $script."}";
                $script = $script."if(iscomp && iscomp==true){";
                $script = $script."entry.iscomp=true";
                $script = $script."}else{";
                $script = $script."entry.iscomp=false;";
                $script = $script."}";
                $script = $script."leadsheet.push(entry);";
                $script = $script."var cid='".$case_id."';";
                $script = $script."var data = {caseid:cid,leads:leadsheet};";
                $script = $script."postData('".site_url("add/add_lead_entries")."',data,null,function(res){";
                $script = $script."if(res.result){";
                    // open lead sheet
                    $script = $script."$('#btnCloseWin').click();";
                    $script = $script."$('#btnLeadSheet').click();";
                $script = $script."}else{";
                $script = $script."$('#altMsgLead').addClass('alert-danger').html('Unable to save lead.').show();";
                $script = $script."}";
                $script = $script."});";
            $script = $script."}else{";
            $script = $script."$('#altMsgLead').addClass('alert-danger').html('Valid lead entry required.').show();";
            $script = $script."}";
            $script = $script."});";
            // end validate
        $script = $script."});";
        // edit case name
        $script = $script."$('#btnEditCaseName').on('click',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var ccname=$(this).attr('data');";
        $script = $script."openModal('Edit Case Name',openEditCaseName(ccname));";
        $script = $script."$('#altMsgEditCName').hide();";
        $script = $script."$('#cname').on('blur keyup focus', function(){";
        $script = $script."var cname=$('#cname');";
        $script = $script."var cnameres=$('#cnameres');";
        $script = $script."if(cname.val().length == 0){";
        $script = $script."cnameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(cname.val().length >= 3){";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#btnSaveCaseName').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."$('#altMsgEditCName').html('').hide();";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cname=$('#cname').val();";
        $script = $script."var cnameres=$('#cnameres');";
        $script = $script."if(cnameres.hasClass('pw_strong')){";
        $script = $script."var data = {casename:cname,caseid:cid};";
        $script = $script."postData('".site_url("edit/case_name")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."case_name=cname;"; // set global
        $script = $script."var bcrumb = $('#brdMain').html().replace(ccname, cname);";
        $script = $script."$('#brdMain').html(bcrumb);"; // set breadcrumb
        $script = $script."document.title = case_name+' | Tactician';"; // set page title
        $script = $script."$('#dvTitle').html(cname);";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgEditCName').addClass('alert-danger').html(res.msg).show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgEditCName').addClass('alert-danger').html('Valid Case Name Required').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // edit supporting
        $script = $script."$('.btnedtdetail').on('click', function(){";
                $script = $script."var id=$(this).attr('data');";
                $script = $script."var sp=$(this).attr('sup');";
                $script = $script."var data;";
                $script = $script."switch(sp){";
                $script = $script."case 'Attorney': data={attorneyid:id}; break;";
                $script = $script."case 'LE Agency':data={leagentid:id}; break;";
                $script = $script."case 'CPA': data={cpaid:id}; break;";
                $script = $script."case 'District Attorney': data={daid:id}; break;";
                $script = $script."}";
                $script = $script."postData('".site_url("data/case_details")."',data,null,function(res){";
                $script = $script."if(res.result){";
                $script = $script."var sups=res.supporting;";
                $script = $script."if(sups.length == 0){";
                $script = $script."alert('ERROR: No '+sp+' Found.');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."var item={};";
                $script = $script."for(var i=0;i<sups.length;i++){";
                    $script = $script."if(sups[i].profession == sp){";
                        $script = $script."item=sups[i];";
                    $script = $script."}";
                $script = $script."}";
                $script = $script."openModal('Edit '+item.profession, openEditSupporting(item.name,item.title,item.street,item.city,item.state,item.zip,item.phone,item.email));";
                $script = $script."$('#altMsgSup').hide();";
                    $script = $script."var stinp=document.getElementById('state_sup');";
                    $script = $script."var statesel=new Awesomplete(stinp,{minChars:1,maxItems:5});";
                    $script = $script."statesel.list=['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];";
                    // validate name
                    $script = $script."var name=$('#name_sup');";
                    $script = $script."var nameres=$('#nameres_sup');";
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
                    $script = $script."var title=$('#title_sup');";
                    $script = $script."var titleres=$('#titleres_sup');";
                    $script = $script."title.on('blur keyup focus', function() {";
                    $script = $script."if(title.val().length == 0){";
                    $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(title.val().length >= 3){";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."});";
                    // validate street
                    $script = $script."var street=$('#street_sup');";
                    $script = $script."var streetres=$('#streetres_sup');";
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
                    $script = $script."var city=$('#city_sup');";
                    $script = $script."var cityres=$('#cityres_sup');";
                    $script = $script."var state=$('#state_sup');";
                    $script = $script."var zip=$('#zip_sup');";
                    $script = $script."city.on('blur keyup focus change', function() {";
                    $script = $script."if(city.val().length == 0){";
                    $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(city.val().length >= 3){";
                    // ajax load zip codes
                    $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
                    $script = $script."if(data){";
                    $script = $script."var zipList=JSON.parse(data);";
                    $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
                    $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
                    // validate phone
                    $script = $script."var phone=$('#phone_sup');";
                    $script = $script."var phoneres=$('#phoneres_sup');";
                    $script = $script."phone.on('blur keyup focus', function() {";
                    $script = $script."if(phone.val().length == 0){";
                    $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(phone.val().length >= 10){";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."});";
                    // validate email
                    $script = $script."var email=$('#email_sup');";
                    $script = $script."var emailres=$('#emailres_sup');";
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
                    // run start validation
                    $script = $script."name.blur();";
                    $script = $script."title.blur();";
                    $script = $script."street.blur();";
                    $script = $script."city.blur();";
                    $script = $script."state.blur();";
                    $script = $script."zip.blur();";
                    $script = $script."phone.blur();";
                    $script = $script."email.blur();";
                    // save
                    $script = $script."$('#btnSaveEditSup').on('click', function(e){";
                        $script = $script."$('#altMsgSup').hide();";
                        $script = $script."var btn=$(this);";
                        $script = $script."var isSupValid = function(nres,tres,sres,cres,stres,zres) {";
                        $script = $script."var retvalue=false;";
                        $script = $script."if(nres.hasClass('pw_strong') && tres.hasClass('pw_strong') && cres.hasClass('pw_strong') && stres.hasClass('br_strong') && zres.hasClass('br_strong')){";
                        $script = $script."retvalue=true;";
                        $script = $script."}";
                        $script = $script."return retvalue;";
                        $script = $script."};";
                        $script = $script."if(isSupValid(nameres,titleres,streetres,cityres,state,zip)){";
                            $script = $script."var data = {roleid:id,name:name.val(),title:title.val(),street:street.val(),city:city.val(),state:state.val(),zip:zip.val(),phone:phone.val(),email:email.val()};";
                            $script = $script."postData('".site_url("edit/edit_supporting_role")."',data,btn,function(resp){";
                            $script = $script."if(resp.result){";
                                $script = $script."$('#td_'+id).html(name.val());";
                                $script = $script."$('#btnCloseWin').click();";
                            $script = $script."}else{";
                            $script = $script."$('#altMsgSup').addClass('alert-danger').html(resp.msg).show();";
                            $script = $script."return false;";
                            $script = $script."}";
                            $script = $script."});";
                        $script = $script."}else{";
                        $script = $script."$('#altMsgSup').addClass('alert-danger').html('Valid entires required.').show();";
                        $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+res.msg);";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
        $script = $script."});";
        // edit client
        $script = $script."$('#btnEditClient').on('click', function(){";
        $script = $script."var id=$(this).attr('data');";
            $script = $script."data={clientid:id};";
            $script = $script."postData('".site_url("data/case_details")."',data,null,function(res){";
                $script = $script."if(res.result){";
                $script = $script."var item=res.client;";
                $script = $script."openModal('Edit Client', openEditClient(item.name,item.street,item.city,item.state,item.zip,item.phone,item.email));";
                $script = $script."$('#altMsgClient').hide();";
                    $script = $script."var stinp=document.getElementById('state_sup');";
                    $script = $script."var statesel=new Awesomplete(stinp,{minChars:1,maxItems:5});";
                    $script = $script."statesel.list=['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];";
                    // validate name
                    $script = $script."var name=$('#name_sup');";
                    $script = $script."var nameres=$('#nameres_sup');";
                    $script = $script."name.on('blur keyup focus', function() {";
                    $script = $script."if(name.val().length == 0){";
                    $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(name.val().length >= 1){";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."});";
                    // validate street
                    $script = $script."var street=$('#street_sup');";
                    $script = $script."var streetres=$('#streetres_sup');";
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
                    $script = $script."var city=$('#city_sup');";
                    $script = $script."var cityres=$('#cityres_sup');";
                    $script = $script."var state=$('#state_sup');";
                    $script = $script."var zip=$('#zip_sup');";
                    $script = $script."city.on('blur keyup focus change', function() {";
                    $script = $script."if(city.val().length == 0){";
                    $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(city.val().length >= 3){";
                    // ajax load zip codes
                    $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
                    $script = $script."if(data){";
                    $script = $script."var zipList=JSON.parse(data);";
                    $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
                    $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
                    // validate phone
                    $script = $script."var phone=$('#phone_sup');";
                    $script = $script."var phoneres=$('#phoneres_sup');";
                    $script = $script."phone.on('blur keyup focus', function() {";
                    $script = $script."if(phone.val().length == 0){";
                    $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(phone.val().length >= 10){";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."});";
                    // validate email
                    $script = $script."var email=$('#email_sup');";
                    $script = $script."var emailres=$('#emailres_sup');";
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

                    // run start validation
                    $script = $script."name.blur();";
                    $script = $script."street.blur();";
                    $script = $script."city.blur();";
                    $script = $script."state.blur();";
                    $script = $script."zip.blur();";
                    $script = $script."email.blur();";
                    $script = $script."phone.blur();";
                    // save
                    $script = $script."$('#btnSaveClient').on('click', function(e){";
                        $script = $script."$('#altMsgClient').hide();";
                        $script = $script."var btn=$(this);";
                        $script = $script."var isSupValid = function(nres,sres,cres,stres,zres) {";
                        $script = $script."var retvalue=false;";
                        $script = $script."if(nres.hasClass('pw_strong') && cres.hasClass('pw_strong') && stres.hasClass('br_strong') && zres.hasClass('br_strong')){";
                        $script = $script."retvalue=true;";
                        $script = $script."}";
                        $script = $script."return retvalue;";
                        $script = $script."};";
                        $script = $script."if(isSupValid(nameres,streetres,cityres,state,zip)){";
                            $script = $script."var data = {clientid:id,name:name.val(),street:street.val(),city:city.val(),state:state.val(),zip:zip.val(),phone:phone.val(),email:email.val()};";
                            $script = $script."postData('".site_url("edit/edit_client")."',data,btn,function(resp){";
                            $script = $script."if(resp.result){";
                                $script = $script."$('#td_client').html(name.val());";
                                $script = $script."$('#btnCloseWin').click();";
                            $script = $script."}else{";
                            $script = $script."$('#altMsgClient').addClass('alert-danger').html(resp.msg).show();";
                            $script = $script."return false;";
                            $script = $script."}";
                            $script = $script."});";
                        $script = $script."}else{";
                        $script = $script."$('#altMsgClient').addClass('alert-danger').html('Valid entires required.').show();";
                        $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                $script = $script."alert('An Error Occured: '+res.msg);";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
        $script = $script."});";


        // SYNOPSIS
        // handle rich text
        $script = $script."var toolbarOptions = [[{ 'font': [] }],['bold', 'italic', 'underline']];";
        $script = $script."var quill = new Quill('#edSynopsis', {";
        $script = $script."theme: 'snow',";
        $script = $script."modules: {toolbar: toolbarOptions}";
        $script = $script."});";
        // handle click on doc
        $script = $script."$(document).on('click',\"[name='chkConfDocs']\",function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var id=parseInt($(this).attr('data'));";
        $script = $script."var name=$(this).attr('value');";
        $script = $script."var is_other=false;";
        $script = $script."if(id==6 || id>=16){";
        $script = $script."is_other=true;";
        $script = $script."}";
        $script = $script."if($(this).is(':checked') == false){";
        // not checked
            $script = $script."var chk=$(this);";
            $script = $script."var docs=$(this).attr('docs');";
            $script = $script."if(docs){";
            $script = $script."var data = {docid:docs};";
            $script = $script."postData('".site_url("edit/remove_document")."',data,null,function(res){";
            $script = $script."chk.removeAttr('checked');";
            $script = $script."chk.attr('docs','');";
            $script = $script."});";
            $script = $script."}else{";
            $script = $script."chk.removeAttr('checked');";
            $script = $script."chk.attr('docs','');";
            $script = $script."}";
        $script = $script."}else{";
        // checked
            // determine if has file or not
            $script = $script."if($('#spn_'+id).hasClass('text-success')){";
                $script = $script."var chk=$(this);";
                $script = $script."var avdocs=chk.attr('avdocs');";
                $script = $script."if(avdocs.includes(',')){";
                    // add multiple docs
                    $script = $script."var upBtn=$('.btnupload[data=\"'+id+'\"]');";
                    $script = $script."var cmid='".md5($company_id)."';";
                    $script = $script."var data={compid:cmid,doctypeid:id};";
                    $script = $script."postData('".site_url("data/available_docs")."',data,null,function(res){";
                    $script = $script."if(res.result){";
                    $script = $script."openModal('Select Document',buildMultipleAttach(name,res.docs,true));";
                    $script = $script."$('#altMsgMultAtt').hide();";
                    $script = $script."$('#btnUseMultAtt').on('click',function(){";
                    $script = $script."var userid='".md5($user_id)."';";
                    $script = $script."var cid='".$case_id."';";
                    $script = $script."var selDocs=$('#selAttFile :selected').attr('id');";
                    $script = $script."if(!selDocs || selDocs == 0){";
                    $script = $script."$('#altMsgMultAtt').addClass('alert-danger').html('You must select a file').show();";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."var data = {docid:selDocs,caseid:cid,uid:userid};";
                    $script = $script."postData('".site_url("add/attach_doc_to_case")."',data,$('#btnUseMultAtt'),function(res){";
                    $script = $script."chk.prop('checked', true);";
                    $script = $script."chk.attr('docs',selDocs);";
                    $script = $script."$('#btnCloseWin').click();";
                    $script = $script."});";
                    $script = $script."});";
                    $script = $script."$('#btnUploadNew').on('click',function(){";
                    $script = $script."$('#btnCloseWin').click();";
                    $script = $script."upBtn.click();";
                    $script = $script."});";
                    $script = $script."}else{";
                    $script = $script."alert('An error occured. Unable to load documents');";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                    // add single doc
                    $script = $script."var userid='".md5($user_id)."';";
                    $script = $script."var cid='".$case_id."';";
                    $script = $script."var data = {docid:avdocs,caseid:cid,uid:userid};";
                    $script = $script."postData('".site_url("add/attach_doc_to_case")."',data,null,function(res){";
                    $script = $script."chk.prop('checked', true);";
                    $script = $script."chk.attr('docs',avdocs);";
                    $script = $script."});";
                $script = $script."}";
            $script = $script."}else{";
                // upload file
                $script = $script."openModal('Import A Document',buildDocUpload(name,id,is_other));";
                $script = $script."$('#altMsgImpDoc').hide();";
                $script = $script."$('#prgHolderDoc').hide();";
                $script = $script."$('#title_doc').on('blur keyup focus', function(){";
                $script = $script."var title=$('#title_doc');";
                $script = $script."var titleres=$('#titleres_doc');";
                $script = $script."if(title.val().length == 0){";
                $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."}else if(title.val().length >= 3){";
                $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}else{";
                $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."});";
                $script = $script."$('#fleImpDoc').on('change', function(){";
                $script = $script."readURLFormDoc(this,id,function(ret){";
                $script = $script."if(ret['data']){";
                $script = $script."var elem = presentFile(ret['name']);";
                $script = $script."if(elem){";
                $script = $script."$('#fleDragDrop').html(elem);";
                // store file object to global
                $script = $script."curFileObj=ret;";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
                $script = $script."}";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
                $script = $script."}";
                $script = $script."});";
                $script = $script."});";
                $script = $script."$('#fleDragDrop').on('drop', function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."e.stopPropagation();";
                $script = $script."$(this).css('border', '1px solid #f9f9f9');";
                $script = $script."var files = e.originalEvent.dataTransfer.files;";
                $script = $script."readURLDropDoc(files,id,function(ret){";
                $script = $script."if(ret['data']){";
                $script = $script."var elem = presentFile(ret['name']);";
                // store file object to global
                $script = $script."curFileObj=ret;";
                $script = $script."if(elem){";
                $script = $script."$('#fleDragDrop').html(elem);";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
                $script = $script."}";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
                $script = $script."}";
                $script = $script."});";
                $script = $script."});";
                // save file
                $script = $script."$('#btnSaveImpDoc').on('click', function(){";
                $script = $script."var btn=$(this);";
                $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
                $script = $script."$('#altMsgImpDoc').removeClass('alert-danger').html('').hide();";
                $script = $script."var title=$('#title_dv').html();";
                $script = $script."var titleres=$('#titleres_doc');";
                $script = $script."if(is_other && titleres.hasClass('pw_strong')){";
                $script = $script."title=$('#title_doc').val();";
                $script = $script."if(!title){";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."}else if(is_other){";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (curFileObj)) {";
                // add title to object
                $script = $script."curFileObj.title=title;";
                // save file via AJAX
                $script = $script."var userid='".md5($user_id)."';";
                $script = $script."var cid='".$case_id."';";
                $script = $script."var cmid='".$company_id."';";
                $script = $script."var data = {compid:cmid,caseid:cid,uid:userid,file:curFileObj};";
                $script = $script."postData('".site_url("add/add_admin_doc")."',data,btn,function(res){";
                $script = $script."if(res.result){";
                // reload documents
                $script = $script."var cmid='".md5($company_id)."';";
                $script = $script."var data = {compid:cmid,caseid:cid};";
                $script = $script."postData('".site_url("data/documents")."',data,btn,function(res){";
                $script = $script."if(res.result){";
                // HANDLE REBUILD ROWS
                $script = $script."addDocumentRows($('#tblConfDocs'),res.conf_docs);";
                $script = $script."$('#btnCloseWin').click();";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('A valid file is required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // manage upload button
        $script = $script."$(document).on('click','.btnupload',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var id=parseInt($(this).attr('data'));";
        $script = $script."var name=$('#chk_'+id).attr('value');";
        $script = $script."var is_other=false;";
        $script = $script."if(id==6 || id>=16){";
        $script = $script."is_other=true;";
        $script = $script."}";
        // upload file
        $script = $script."openModal('Import A Document',buildDocUpload(name,id,is_other));";
        $script = $script."$('#altMsgImpDoc').hide();";
        $script = $script."$('#prgHolderDoc').hide();";
        $script = $script."$('#title_doc').on('blur keyup focus', function(){";
        $script = $script."var title=$('#title_doc');";
        $script = $script."var titleres=$('#titleres_doc');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#fleImpDoc').on('change', function(){";
        $script = $script."readURLFormDoc(this,id,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."$('#fleDragDrop').on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDropDoc(files,id,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // save file
        $script = $script."$('#btnSaveImpDoc').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpDoc').removeClass('alert-danger').html('').hide();";
        $script = $script."var title=$('#title_dv').html();";
        $script = $script."var titleres=$('#titleres_doc');";
        $script = $script."if(is_other && titleres.hasClass('pw_strong')){";
        $script = $script."title=$('#title_doc').val();";
        $script = $script."if(!title){";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}else if(is_other){";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (curFileObj)) {";
        // add title to object
        $script = $script."curFileObj.title=title;";
        // save file via AJAX
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cmid='".$company_id."';";
        $script = $script."var data = {compid:cmid,caseid:cid,uid:userid,file:curFileObj};";
        $script = $script."postData('".site_url("add/add_admin_doc")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // reload documents
        $script = $script."var cmid='".md5($company_id)."';";
        $script = $script."var data = {compid:cmid,caseid:cid};";
        $script = $script."postData('".site_url("data/documents")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // HANDLE REBUILD ROWS
        $script = $script."addDocumentRows($('#tblConfDocs'),res.conf_docs);";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('A valid file is required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        // end upload
        $script = $script."});";
        // handle Add Other Row
        $script = $script."$('#btnAddConfDocs').on('click',function(e){";
        $script = $script."e.preventDefault();";
        // get count of rows
        $script = $script."var ct = $('#tblConfDocs').children('tr').length;";
        $script = $script."var nct=(ct+11);";
        $script = $script."if(nct >= 21){ $('#btnAddConfDocs').hide() }";
        $script = $script."if(nct > 21){return false;}";
        $script = $script."var row='<tr>'";
        $script = $script."+'<td><input type=\"checkbox\" id=\"chk_'+nct+'\" avdocs=\"\" docs=\"\" name=\"chkConfDocs\" data=\"'+nct+'\" value=\"Other\"></td>'";
        $script = $script."+'<td title=\"\">Other</td>'";
        $script = $script."+'<td><span class=\"glyphicon glyphicon-plus text-warning\" id=\"spn_'+nct+'\"></span></td>'";
        $script = $script."+'<td><button type=\"button\" class=\"btn btn-info btn-xs btnupload\" cat=\"1\" data=\"'+nct+'\">New</button></td>'";
        $script = $script."+'</tr>';";
        $script = $script."$('#tblConfDocs').append(row);";
        $script = $script."});";
        // handle File View
        $script = $script."$(document).on('click','.ancDocView',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var name=$(this).html();";
        $script = $script."var filename=$(this).attr('fname');";
        $script = $script."var icon=$(this).attr('icon');";
        $script = $script."var added=$(this).attr('added');";
        $script = $script."openModal('Document',buildViewDocument(name,filename,icon,added));";
        $script = $script."});";
        // handle synopsis upload
        $script = $script."var buildImportSynopsis = function() {";
        $script = $script."var ret_value=''";
        $script = $script."+'<form id=\"frmImpSynopsis\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgImpSynopsis\" role=\"alert\"></div>'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file (Word, PDF, or Text) and press Import.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleImpSynopsis\" id=\"fleImpSynopsis\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div id=\"fleDragDrop\">'";
        $script = $script."+'<span>Or Drag File Here</span>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveImpSynopsis\" class=\"btn btn-success\">Import</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        $script = $script."$('#btnImportSynopsis').on('click',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."openModal('Upload Synopsis',buildImportSynopsis());";
        $script = $script."$('#altMsgImpSynopsis').hide();";
        // doc handler
        $script = $script."var readURLFormSynopsis = function(input,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (input.files && input.files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=input.files[0].type;";
        $script = $script."retvalue.name=input.files[0].name;";
        $script = $script."reader.readAsDataURL(input.files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        $script = $script."var readURLDropSynopsis = function(files,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (files && files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=files[0].type;";
        $script = $script."retvalue.name=files[0].name;";
        $script = $script."reader.readAsDataURL(files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        // build file presentation icon
        $script = $script."var presentSynopsis = function(name) {";
        $script = $script."if(name){";
        $script = $script."var ft=name.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."var img='';";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'txt': img='".base_url("img/icons/text.png")."'; break;";
        $script = $script."case 'docx': img='".base_url("img/icons/word.png")."'; break;";
        $script = $script."case 'pdf': img='".base_url("img/icons/pdf.png")."'; break;";
        $script = $script."}";
        $script = $script."if(img !== ''){";
        $script = $script."var pres='<img src=\"'+img+'\" class=\"flimg\"><span class=\"flname\">'+name+'</span>';";
        $script = $script."return pres;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        $script = $script."$('#fleImpSynopsis').on('change', function(){";
        $script = $script."readURLFormSynopsis(this,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentSynopsis(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html('Invalid file type. Limited to: .docx, .txt, or .pdf').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."$('#fleDragDrop').on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDropSynopsis(files,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentSynopsis(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html('Invalid file type. Limited to: .docx, .txt, or .pdf').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // save file
        $script = $script."$('#btnSaveImpSynopsis').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpSynopsis').removeClass('alert-danger').html('').hide();";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (curFileObj)) {";
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var data = {caseid:cid,uid:userid,file:curFileObj.data,size:curFileObj.size,type:curFileObj.type,name:curFileObj.name};";
        $script = $script."postData('".site_url("add/add_synopsis_doc")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // HANDLE LOAD OF DOC TO RICH TEXT
        $script = $script."var vw='<div class=\"responsive-doc\" id=\"attViewer\">';";
        $script = $script."var url=res.url;";
        $script = $script."var ft=url.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'txt': vw +='<iframe src=\"'+url+'\"></iframe>'; break;";
        $script = $script."case 'docx': vw +='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'pdf': vw +='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."vw +='<iframe src=\"'+res.url+'\"></iframe>';";
        $script = $script."}";
        $script = $script."vw +='</div>';";
        $script = $script."$('#docSynopsis').html(vw);";
        $script = $script."$('#btnSaveSynopsis').html('Type New Synopsis').attr('title','Type New Synopsis');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpSynopsis').addClass('alert-danger').html('A valid file is required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // handle type synopsis
        $script = $script."$('#btnSaveSynopsis').on('click',function(){";
        $script = $script."if($(this).html() == 'Type New Synopsis'){";
        $script = $script."var vw='<div id=\"edSynopsis\" style=\"height:350px;\"></div>';";
        $script = $script."$('#docSynopsis').html(vw);";
        $script = $script."var toolbarOptions = [[{ 'font': [] }],['bold', 'italic', 'underline']];";
        $script = $script."var quill = new Quill('#edSynopsis', {";
        $script = $script."theme: 'snow',";
        $script = $script."modules: {toolbar: toolbarOptions}";
        $script = $script."});";
        $script = $script."$(this).html('Save Synopsis').attr('title','Save Synopsis');";
        $script = $script."}else{";
        $script = $script."var contents = $('.ql-editor').html();";
        //add_synopsis_text
        $script = $script."var btn=$(this);";
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var data = {caseid:cid,uid:userid,text:contents};";
        $script = $script."postData('".site_url("add/add_synopsis_text")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."}else{";
        $script = $script."alert('An Error Occured: '+res.msg);";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}";
        $script = $script."});";
        // end save text
        
        // INTERVIEWS
        $script = $script."var viewApproveInterview = function(id) {";
            $script = $script."var ret_value='';";
            $script = $script."ret_value+='<p>Note: Interview will only be included in report once approved.</p>';";
            $script = $script."ret_value+='<hr>';";
            $script = $script."ret_value+='<p><strong>Notes for Agent:</strong></p>';";
            $script = $script."ret_value+='<textarea id=\"txtAppNotes\" cols=\"5\" style=\"width:350px;\"></textarea>';";
            $script = $script."ret_value+='<hr>';";
            $script = $script."ret_value+='<button id=\"btnApprove\" data=\"'+id+'\" class=\"btn btn-success\">Approve</button>';";
            $script = $script."ret_value+=' <button id=\"btnReturn\" data=\"'+id+'\" class=\"btn btn-info\">Return</button>';";
            $script = $script."return ret_value;";
        $script = $script."};";
    
        $script = $script."var viewNewInterview = function(name,agent,date,desc,loc,dob,emp,street,city,state,zip,phone,title,notes,id) {";
        $script = $script."var retValue='';";
        $script = $script."if(!id){id=0;}";

        $script = $script."retValue += '<div class=\"row\">';";
            $script = $script."retValue += '<div class=\"col-xs-6\">';";
                $script = $script."retValue += '<h4><span>'+name+'</span></h4>';";
                $script = $script."if(title){";
                    $script = $script."retValue += '<p><strong>Title:</strong> '+title+'</p>';";
                $script = $script."}";
                $script = $script."retValue += '<p><strong>Employer:</strong> '+emp+'</p>';";
                $script = $script."retValue += '<p><strong>DOB:</strong> '+dob+'</p>';";
                $script = $script."if(street){";
                    $script = $script."retValue += '<p><strong>Street:</strong> '+street+'</p>';";
                    $script = $script."retValue += '<p><strong>City, State, Zip:</strong> '+city+', '+state+' '+zip+'</p>';";
                $script = $script."}";
                $script = $script."retValue += '<p><strong>Phone:</strong> '+phone+'</p>';";
            $script = $script."retValue += '</div>';";

            $script = $script."retValue += '<div class=\"col-xs-6\">';";
                $script = $script."retValue += '<p><strong>Location:</strong> '+loc+'</p>';";
                $script = $script."retValue += '<p><strong>Agent:</strong> '+agent+'</p>';";
                $script = $script."retValue += '<p><strong>Date:</strong> '+date+'</p>';";
                $script = $script."retValue += '<p>'+desc+'</p>';";
            $script = $script."retValue += '</div>';";
        $script = $script."retValue += '</div>';";
        
        $script = $script."retValue += '<hr>';";

        // interview text
        $script = $script."retValue += '<p><strong>Notes:</strong></p>';";
        $script = $script."if(notes){";
            $script = $script."retValue += '<p id=\"txtnotes\" style=\"height:100px;\"></p>';";
        $script = $script."}";
        $script = $script."retValue += '<div style=\"background-color:#f2f2f2;margin-top:10px;margin-bottom:10px;padding:10px;\"><button type=\"button\" id=\"btnAddNotes\" class=\"btn btn-sm btn-success\" data=\"'+id+'\">Add / Edit Notes</button></div>';";
        
        $script = $script."retValue += '<hr>';";
        $script = $script."retValue += '<p><strong>Files:</strong></p>';";
        $script = $script."retValue += '<table id=\"tblIntAtts\" class=\"table table-hover\">';";

        $script = $script."retValue += '<thead>';";
        $script = $script."retValue += '<tr>';";
        $script = $script."retValue += '<th></th>';";
        $script = $script."retValue += '<th>Type</th>';";
        $script = $script."retValue += '<th>Number</th>';";
        $script = $script."retValue += '<th>Obtained</th>';";
        $script = $script."retValue += '</tr>';";
        $script = $script."retValue += '</thead>';";
        $script = $script."retValue += '<tbody id=\"tblIntAttsBody\"></tbody>';";
        $script = $script."retValue += '</table>';";

        $script = $script."return retValue;";
        $script = $script."};";

        // Load Attachments
        $script = $script."var addIntAttsPop = function(tbl, intid){";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var data = {caseid:cid};";
        $script = $script."postData('".site_url("data/attachments")."',data,null,function(res){";
        $script = $script."if(res.result && res.docs){";
        $script = $script."tbl.html('');";
        $script = $script."var docs=res.docs;";
        $script = $script."var doccount=docs.length;";
        $script = $script."for(var i=0;i<docs.length;i++){";
            $script = $script."var doc=docs[i];";
            $script = $script."var row='<tr>';";
            $script = $script."row += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
            $script = $script."row += '<td><a href=\"'+doc.url+'\" class=\"ancAtt\">'+doc.number+'</a></td>';";
            $script = $script."var dt=new Date(doc.created);";
            $script = $script."row += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
            $script = $script."row += '</tr>';";
                // load attachment row to interview
                $script = $script."if(doc.intid == intid){";
                $script = $script."tbl.append(row);";
                $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        $script = $script."};";

        // LOAD ATTACHMENTS FOR MAIN
        $script = $script."var addIntAtts = function(intid){";
        $script = $script."var cid='".$case_id."';";
        // success - load table
        $script = $script."var data = {int_id:intid};";
        $script = $script."postData('".site_url("data/interview_attachments")."',data,null,function(res){";
        $script = $script."if(res.result && res.docs){";
        $script = $script."var docs=res.docs;";
        $script = $script."$('#tblIntAttsBody').html('');";
        $script = $script."var doccount=docs.length;";
        //$script = $script."$('#attMainCount').html(doccount+' attachment(s)');";
        $script = $script."for(var i=0;i<docs.length;i++){";
            $script = $script."var doc=docs[i];";
            $script = $script."var row='<tr>';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
            $script = $script."row += '<td><button class=\"btn btn-danger btn-xs delatt\" intid=\"'+doc.intid+'\" type=\"button\" dataid=\"'+doc.id+'\" dataname=\"'+doc.number+'\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
}else{
            $script = $script."row += '<td>&nbsp;</td>';";
}
            $script = $script."row += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
            $script = $script."row += '<td><a href=\"'+doc.url+'\" class=\"ancAtt\">'+doc.number+'</a></td>';";
            $script = $script."var dt=new Date(doc.created);";
            $script = $script."row += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
            $script = $script."row += '</tr>';";
                // load attachment row to interview
                $script = $script."if(doc.intid == intid){";
                $script = $script."var rowi='<tr>';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
                $script = $script."rowi += '<td><button class=\"btn btn-danger btn-xs delatt\" intid=\"'+doc.intid+'\" type=\"button\" dataid=\"'+doc.id+'\" dataname=\"'+doc.number+'\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
}else{
                $script = $script."rowi += '<td>&nbsp;</td>';";
}
                $script = $script."rowi += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
                $script = $script."rowi += '<td><a href=\"'+doc.url+'\" class=\"ancAttInt\">'+doc.number+'</a></td>';";
                $script = $script."var dt=new Date(doc.created);";
                $script = $script."rowi += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
                $script = $script."rowi += '</tr>';";
                $script = $script."$('#tblIntAttsBody').append(rowi);";
                $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        $script = $script."};";
        // - END

        $script = $script."var viewIntList = function(name,agent,date,desc,id) {";
        $script = $script."var retValue='';";
        $script = $script."retValue += '<tr>';";
        $script = $script."if(!id){id=0;}";
        $script = $script."retValue += '<td><a href=\"#\" class=\"ancintview\" data=\"'+id+'\">'+name+'</td>';";
        $script = $script."retValue += '<td>'+agent+'</td>';";
        $script = $script."retValue += '<td>'+date+'</td>';";
        // determine if is admin
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        //$script = $script."retValue += '<td style=\"text-align:right;\">True</td>';";
        $script = $script."retValue += '<td style=\"text-align:right;\"><input id=\"chk_intapp_'+id+'\" class=\"chk_intapp\" data=\"'+id+'\" value=\"1\" type=\"checkbox\">';";
}else{
        $script = $script."retValue += '<td style=\"text-align:right;\">False</td>';";
}
        $script = $script."retValue += '</tr>';";
        $script = $script."return retValue;";
        $script = $script."};";

        // edit interview notes
        // btnAddNotes
        $script = $script."$(document).on('click', '#btnAddNotes', function(e){";
            $script = $script."var id=$(this).attr('data');";

            // load notes
            $script = $script."var data = {intid:id};";
            $script = $script."postData('".site_url("data/interview_notes")."',data,null,function(resp){";
            $script = $script."if(resp.result){";

                $script = $script."var notes='';";
                $script = $script."if(!resp.notes){";
                    $script = $script."notes='';";
                $script = $script."}else{";
                    $script = $script."notes=resp.notes;";
                $script = $script."}";

                $script = $script."var form='';";
                $script = $script."form +='<p>Type your interview notes below:</p>';";
                $script = $script."form +='<div id=\"edInterview\" style=\"height:250px;\"></div>';";
                $script = $script."form +='<div style=\"text-align:right;margin-top:10px;\">';";
                $script = $script."form +='<button type=\"button\" id=\"btnSaveNotesInt\" data=\"'+id+'\" class=\"btn btn-success\">Save</button>';";
                $script = $script."form +=' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
                $script = $script."form +='</div>';";
                $script = $script."form +='<input type=\"hidden\" id=\"txt_notes_contents\" value=\"\">';";

                $script = $script."openModal('Add / Edit Notes',form);";
                $script = $script."var toolbarOptions = [[{ 'font': [] }],['bold', 'italic', 'underline']];";
                $script = $script."var quill = new Quill('#edInterview', {";
                $script = $script."theme: 'snow',";
                $script = $script."modules: {toolbar: toolbarOptions}";
                $script = $script."});";
                // load contents
                $script = $script."if(resp.notes){";
                    $script = $script."quill.setContents(JSON.parse(notes));";
                $script = $script."}";
                $script = $script."quill.on('text-change', function(delta, oldDelta, source) {";
                $script = $script."$('#txt_notes_contents').val(JSON.stringify(quill.getContents()));";
                $script = $script."});";

                // save notes
                $script = $script."$(document).on('click', '#btnSaveNotesInt', function(e){";
                    $script = $script."var id=$(this).attr('data');";
                    $script = $script."var notes=$('#txt_notes_contents').val();";
                    //$script = $script."var notes = $('.ql-editor').html();";
                    $script = $script."var data = {iid:id,notes:notes};";
                    $script = $script."postData('".site_url("edit/interview_notes_edit")."',data,null,function(resp){";
                    $script = $script."if(resp.result){";
                        // handle update
                        $script = $script."$('.ancintview[data=\"'+id+'\"]').click();";
                        $script = $script."$('.close').click();";
                    $script = $script."}else{";
                    $script = $script."alert('An Error Occured: '+resp.msg);";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."});";

            $script = $script."}else{";
                    $script = $script."alert('Invalid interview');";
                    $script = $script."return false;";
            $script = $script."}";
            $script = $script."});";

        $script = $script."});";

        // approve interview
        $script = $script."$(document).on('click', '#btnAddAttApp,.chk_intapp', function(e){";
            $script = $script."e.preventDefault();";
            $script = $script."var id=$(this).attr('data');";
            $script = $script."var name='a';";
            $script = $script."var agent='b';";
            $script = $script."var date='c';";
            $script = $script."openModal('Update Interview Status',viewApproveInterview(id));";
                // approve
                $script = $script."$(document).on('click', '#btnApprove', function(e){";
                    $script = $script."var id=$(this).attr('data');";
                    $script = $script."var notes=$('#txtAppNotes').val();";
                    $script = $script."var meid='".$user_id."';";
                    // mark as saved
                    $script = $script."var data = {iid:id,supid:meid,comments:notes};";
                    $script = $script."postData('".site_url("edit/interview_approve")."',data,null,function(resp){";
                    $script = $script."if(resp.result){";
                        // handle update
                        //$script = $script."$('.chk_intapp').each(function() {";
                        //    $script = $script."if ($(this).attr('data') == id){";
                        //        $script = $script."$(this).attr('checked','checked');";
                        //    $script = $script."}";
                        //$script = $script."});";
                        $script = $script."$('.close').click();";
                        $script = $script."location.reload();";
                    $script = $script."}else{";
                    $script = $script."alert('An Error Occured: '+resp.msg);";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."});";
                // return
                $script = $script."$(document).on('click', '#btnReturn', function(e){";
                    
                    // handle return - send email
                    // interview_return
                    $script = $script."var id=$(this).attr('data');";
                    $script = $script."var notes=$('#txtAppNotes').val();";
                    $script = $script."var meid='".$user_id."';";
                    // mark as saved
                    $script = $script."var data = {iid:id,supid:meid,comments:notes};";
                    $script = $script."postData('".site_url("edit/interview_return")."',data,null,function(resp){";
                    $script = $script."if(resp.result){";
                        // handle update
                        $script = $script."$('.chk_intapp').each(function() {";
                            $script = $script."if ($(this).attr('data') == id){";
                                $script = $script."$(this).removeAttr('checked');";
                            $script = $script."}";
                        $script = $script."});";
                    $script = $script."$('.close').click();";
                    $script = $script."}else{";
                    $script = $script."alert('Unable to return Interview');";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."});";

                $script = $script."});";
        $script = $script."});";

        // add attachment to interview
        $script = $script."$(document).on('click', '#btnAddAttInt', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var intid=$(this).attr('data');";
        $script = $script."openModal('Import A File',buildImportFile(intid));";
        $script = $script."$('#prgHolder').hide();";
        // handle tags and title validation
        $script = $script."$('#title_fle').on('blur keyup focus', function(){";
        $script = $script."var title=$('#title_fle');";
        $script = $script."var titleres=$('#titleres_fle');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#tags_fle').on('blur keyup focus', function(){";
        $script = $script."var tags=$('#tags_fle');";
        $script = $script."var tagsres=$('#tagsres_fle');";
        $script = $script."if(tags.val().length == 0){";
        $script = $script."tagsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(tags.val().trim().indexOf(',') != -1){";
        $script = $script."tagsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."tagsres.html('comma required').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        // handle file control add
        $script = $script."$('#fleImpFile').on('change', function(){";
        $script = $script."readURLForm(this,$('#notes_fle').val(),$('#tags_fle').val(),function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";
        // handle drop file
        $script = $script."$(document).on('dragover', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragover', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('drop', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDrop(files,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // build row for display
        $script = $script."$(document).on('click', '#btnSaveImpFilex', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        $script = $script."if(curFileObj){";
        $script = $script."curFileObj['title'] = $('#title_fle').val();";
        $script = $script."curFileObj['tags'] = $('#tags_fle').val();";
        $script = $script."impFileList.push(curFileObj);";
        $script = $script."curFileObj={};";
        $script = $script."}";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (impFileList && impFileList.length > 0)) {";
        // save file via AJAX
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var intid=$('#intid').val();";
        $script = $script."var data = {caseid:cid,uid:userid,iid:intid,fls:impFileList};";
        $script = $script."postData('".site_url("add/add_supporting_docs")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // success - load table
        $script = $script."var data = {caseid:cid};";
        $script = $script."postData('".site_url("data/attachments")."',data,btn,function(res){";
        $script = $script."if(res.result && res.docs){";
        $script = $script."var docs=res.docs;";
        $script = $script."$('#tbodyAtt').html('');";
        $script = $script."$('#tbodyAttMain').html('');";
        $script = $script."$('#tblIntAttsBody').html('');";
        $script = $script."var doccount=docs.length;";
        $script = $script."$('#attMainCount').html(doccount+' attachment(s)');";
        $script = $script."$('#numAttachments').html(doccount);";
        $script = $script."for(var i=0;i<docs.length;i++){";
        $script = $script."var doc=docs[i];";
        $script = $script."var row='<tr>';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $script = $script."row += '<td><button class=\"btn btn-danger btn-xs delatt\" type=\"button\" dataid=\"'+doc.id+'\" dataname=\"'+doc.number+'\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td>';";
}else{
        $script = $script."row += '<td>&nbsp;</td>';";
}
        $script = $script."row += '<td><img src=\"'+doc.icon+'\" style=\"width:24px;height:24px;\"> '+doc.postfix+'</td>';";
        $script = $script."row += '<td><a href=\"'+doc.url+'\" class=\"ancAtt\">'+doc.number+'</a></td>';";
        $script = $script."var dt=new Date(doc.created);";
        $script = $script."row += '<td>'+dt.toLocaleDateString('en-US')+'</td>';";
        $script = $script."row += '</tr>';";
        $script = $script."$('#tbodyAtt').append(row);";
        $script = $script."$('#tbodyAttMain').append(row);";
        
            // load attachment row to interview
            $script = $script."if(doc.intid == intid){";
            $script = $script."$('#tblIntAttsBody').append(row);";
            $script = $script."}";

        $script = $script."}";
        $script = $script."impFileList = [];";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."impFileList = [];";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('No file added. Please select or drop a file to Add to Case.').show();";
        $script = $script."btn.removeAttr('disabled').html('Add to Case');";
        $script = $script."}";
        $script = $script."});";

        $script = $script."$(document).on('click','.ancintview',function(e){";
            $script = $script."e.preventDefault();";
            // move to interview tab
            $script = $script."window.location.hash='#interviews';";
            // get interview id
            $script = $script."var inid = $(this).attr('data');";
            // load interview from db
            $script = $script."var data = {intid:inid};";
            $script = $script."postData('".site_url("data/interview")."',data,null,function(resp){";
            $script = $script."if(resp.result){";
                // display interview
                $script = $script."var interview=resp.interview;";
                $script = $script."var body = viewNewInterview(interview.name,interview.user_name,interview.date_occured,interview.description,interview.location,interview.dob,interview.emp,interview.street,interview.city,interview.state,interview.zip,interview.phone,interview.title,interview.notes,interview.id);";
                $script = $script."$('#dvIntView').html(body);";
                // show notes txtnotes
                $script = $script."if(interview.notes){";
                    $script = $script."var quill = new Quill('#txtnotes', {";
                    $script = $script."theme: 'snow',";
                    $script = $script."readOnly: true,";
                    $script = $script."modules: {toolbar: null}";
                    $script = $script."});";
                    $script = $script."quill.setContents(JSON.parse(interview.notes));";
                $script = $script."}";
                
                $script = $script."addIntAtts(inid);";
                $script = $script."$('#dvIntBanner').html('<button id=\"btnAddAttInt\" data=\"'+interview.id+'\" class=\"btn btn-success\" type=\"button\" title=\"Add Attachment\">Add Attachment</button>');";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
                $script = $script."$('#dvIntBanner').append(' <button id=\"btnAddAttApp\" data=\"'+interview.id+'\" class=\"btn btn-default\" type=\"button\" title=\"Update Approval Status\">Update Approval Status</button>');";
}
                // add edit button
                $script = $script."var meid='".$user_id."';";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
                $script = $script."$('#dvIntBanner').append(' <button id=\"btnEditInt\" data=\"'+interview.id+'\" class=\"btn btn-info\" type=\"button\" title=\"Edit Interview\">Edit Interview</button>');";
}else{
                $script = $script."if (interview.user_id == meid){";
                $script = $script."$('#dvIntBanner').append(' <button id=\"btnEditInt\" data=\"'+interview.id+'\" class=\"btn btn-info\" type=\"button\" title=\"Edit Interview\">Edit Interview</button>');";
                $script = $script."}";
}
            $script = $script."}else{";
            $script = $script."alert('An Error Occured: '+resp.msg);";
            $script = $script."return false;";
            $script = $script."}";
            $script = $script."});";
        $script = $script."});";

        $script = $script."var buildNewInterview = function() {";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Complete the form to add a new interview.</p>'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgInt\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"int_comp_id\" id=\"inv_comp_id\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<input type=\"hidden\" name=\"inv_case_id\" id=\"inv_comp_id\" value=\"".md5($case_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_int\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_int\" id=\"name_int\" value=\"\" placeholder=\"Interviewee Name\">'";
        $script = $script."+'<span id=\"nameres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //dob
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"dob_int\" class=\"col-sm-2 control-label\">DOB</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"dob_int\" id=\"dob_int\" value=\"\" placeholder=\"Interviewee Date of Birth\">'";
        $script = $script."+'<span id=\"dobres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Title
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_int\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_int\" id=\"title_int\" value=\"\" placeholder=\"Interviewee Title\">'";
        $script = $script."+'<span id=\"titleres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Employer
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"emp_int\" class=\"col-sm-2 control-label\">Employer</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"emp_int\" id=\"emp_int\" value=\"\" placeholder=\"Interviewee Employer\">'";
        $script = $script."+'<span id=\"empres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Address
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street_int\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street_int\" id=\"street_int\" value=\"\" placeholder=\"Interviewee Street\">'";
        $script = $script."+'<span id=\"streetres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city_int\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city_int\" id=\"city_int\" value=\"\" placeholder=\"Interviewee City\">'";
        $script = $script."+'<span id=\"cityres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"state_int\" class=\"col-sm-2 control-label\">State/Zip</label>'";
        $script = $script."+'<div class=\"col-sm-4\">'";
        //$script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"state_int\" id=\"state_int\" value=\"\" placeholder=\"State\">'";
        //$script = $script."+'<span id=\"stateres_int\" class=\"input-group-addon\"></span>'";
        //$script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        //$script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"zip_int\" id=\"zip_int\" value=\"\" placeholder=\"Zip\">'";
        //$script = $script."+'<span id=\"zipres_int\" class=\"input-group-addon\"></span>'";
        //$script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Phone
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"phone_int\" class=\"col-sm-2 control-label\">Phone</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"phone_int\" id=\"phone_int\" value=\"\" placeholder=\"Interview Phone\">'";
        $script = $script."+'<span id=\"phoneres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<hr>'";

        //location
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"location_int\" class=\"col-sm-2 control-label\">Location</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"location_int\" id=\"location_int\" value=\"\" placeholder=\"Interview Location\">'";
        $script = $script."+'<span id=\"locationres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"desc_int\" class=\"col-sm-2 control-label\">Desc</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<textarea class=\"form-control\" name=\"desc_int\" id=\"desc_int\" placeholder=\"Interview Description\"></textarea>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"date_int\" class=\"col-sm-2 control-label\">Date</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"date\" class=\"form-control\" name=\"date_int\" id=\"date_int\" value=\"\" max=\"".date('Y-m-d')."\" data=\"".date('m/d/Y')."\" placeholder=\"\">'";
        $script = $script."+'<span id=\"dateres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnAddNewInt\" class=\"btn btn-success\">Add</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // handle add/menu buttons
        $script = $script."$(document).on('click','#btnAddInterview',function(e){";
        $script = $script."openModal('New Interview',buildNewInterview());";
        $script = $script."$('#altMsgInt').hide();";
        // validate name
        $script = $script."var name=$('#name_int');";
        $script = $script."var nameres=$('#nameres_int');";
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

        // validate DOB
        $script = $script."var dob=$('#dob_int');";
        $script = $script."var dobres=$('#dobres_int');";
        $script = $script."dob.on('blur keyup focus', function() {";
        $script = $script."if(dob.val().length == 0){";
        $script = $script."dobres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(dob.val().length >= 1){";
        $script = $script."dobres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."dobres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // validate title
        $script = $script."var title=$('#title_int');";
        $script = $script."var titleres=$('#titleres_int');";
        $script = $script."title.on('blur keyup focus', function() {";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(title.val().length >= 1){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // validate employer
        $script = $script."var emp=$('#emp_int');";
        $script = $script."var empres=$('#empres_int');";
        $script = $script."emp.on('blur keyup focus', function() {";
        $script = $script."if(emp.val().length == 0){";
        $script = $script."empres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(emp.val().length >= 1){";
        $script = $script."empres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."empres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
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

        // validate street
        $script = $script."var street=$('#street_int');";
        $script = $script."var streetres=$('#streetres_int');";
        $script = $script."street.on('blur keyup focus', function() {";
        $script = $script."if(street.val().length == 0){";
        $script = $script."streetres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(street.val().trim().indexOf(' ') != -1){";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate city
        $script = $script."var city=$('#city_int');";
        $script = $script."var cityres=$('#cityres_int');";
        $script = $script."var state=$('#state_int');";
        $script = $script."var zip=$('#zip_int');";
        $script = $script."city.on('blur keyup focus change', function() {";
        $script = $script."if(city.val().length == 0){";
        $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(city.val().length >= 3){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
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
        $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
        $script = $script."}";
        $script = $script."});";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate zip code
        $script = $script."zip.on('blur keyup focus', function(e){";
        $script = $script."if(zip.val().length == 0){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate phone
        $script = $script."var phone=$('#phone_int');";
        $script = $script."var phoneres=$('#phoneres_int');";
        $script = $script."phone.on('blur keyup focus', function() {";
        $script = $script."if(phone.val().length == 0){";
        $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(phone.val().length >= 10){";
        $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate location
        $script = $script."var loc=$('#location_int');";
        $script = $script."var locres=$('#locationres_int');";
        $script = $script."loc.on('blur keyup focus', function() {";
        $script = $script."if(loc.val().length == 0){";
        $script = $script."locres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(loc.val().length >= 1){";
        $script = $script."locres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."locres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
    
        // validate date
        $script = $script."var checkdate = function(input){";
        $script = $script."var validformat=/^\d{2}\/\d{2}\/\d{4}$/;";
        $script = $script."if (!validformat.test(input.val())) {";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."var monthfield=input.val().split('/')[0];";
        $script = $script."var dayfield=input.val().split('/')[1];";
        $script = $script."var yearfield=input.val().split('/')[2];";
        $script = $script."var dayobj = new Date(yearfield, monthfield-1, dayfield);";
        $script = $script."if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return true;";
        $script = $script."}";
        $script = $script."};";
        // determine if browser supports date
        $script = $script."var supportsHTML5Date = function() {";
        $script = $script."var input = document.createElement('input');";
        $script = $script."input.setAttribute('type','date');";
        $script = $script."var notADateValue = 'not-a-date';";
        $script = $script."input.setAttribute('value', notADateValue);";
        $script = $script."return (input.value !== notADateValue);";
        $script = $script."};";
        // creation date validation for non html5 date supported browsers
        $script = $script."var crdate=$('#date_int');";
        $script = $script."var crdateres=$('#dateres_int');";
        $script = $script."if (supportsHTML5Date() == false){";
        $script = $script."if(crdate.val().length == 0){";
        $script = $script."crdate.val(crdate.attr('data'));";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}";
        $script = $script."if(checkdate(crdate)){";
        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
        $script = $script."var curDate = Date.now();";
        //$script = $script."if (selDate.getTime() <= curDate) {";
        //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."}else{";
        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}";
        $script = $script."crdate.on('blur keyup focus', function() {";
        $script = $script."if(crdate.val().length == 0){";
        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}else if(checkdate(crdate)){";
        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
        $script = $script."var curDate = Date.now();";
        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."if (selDate.getTime() <= curDate) {";
        //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."}else{";
        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}";
        //update bar with current date if date picker
        $script = $script."if (supportsHTML5Date() == true){";
        $script = $script."crdate.on('change blur keyup focus', function() {";
        $script = $script."var dt=new Date(crdate.val());";
        $script = $script."var m=(dt.getMonth()+1);";
        $script = $script."if(m < 10){m=0+String(m)}";
        $script = $script."var d=(dt.getDate()+1);";
        $script = $script."if(d < 10){d=0+String(d)}";
        $script = $script."var y=dt.getFullYear();";
        $script = $script."var dtd=m+'/'+d+'/'+y;";
        $script = $script."$('#viewCreationDate').html(dtd);";
        $script = $script."});";
        $script = $script."}";
        // handle save via ajax
            $script = $script."$('#btnAddNewInt').on('click',function(){";
                $script = $script."$('#altMsgInt').hide();";
                $script = $script."var nme=name.val();";
                $script = $script."var dsc=$('#desc_int').val();";
                $script = $script."var dte=crdate.val();";
                $script = $script."var uid='".md5($user_id)."';";
                $script = $script."var cid='".$case_id."';";

                $script = $script."var v_title=title.val();";
                $script = $script."var v_emp=emp.val();";
                $script = $script."var v_street=street.val();";
                $script = $script."var v_city=city.val();";
                $script = $script."var v_state=state.val();";
                $script = $script."var v_zip=zip.val();";
                $script = $script."var v_phone=phone.val();";

                $script = $script."var v_dob=dob.val();";
                $script = $script."var v_loc=loc.val();";

                $script = $script."var data = {userid:uid,caseid:cid,name:nme,desc:dsc,dte_occured:dte,title:v_title,emp:v_emp,street:v_street,city:v_city,state:v_state,zip:v_zip,phone:v_phone,dob:v_dob,location:v_loc};";
                $script = $script."postData('".site_url("add/add_interview")."',data,null,function(resp){";
                $script = $script."if(resp.result){";
                    // handle update - update rows and add case to view

                    // .ancintview click

                    // view
                    $script = $script."var agent=$('#spnProfile').html();";
                    //$script = $script."var body=viewNewInterview(nme,agent,dte,dsc,resp.id);";
                    //$script = $script."$('#dvIntBanner').html('<button id=\"btnAddAttInt\" data=\"'+resp.id+'\" class=\"btn btn-success\" type=\"button\" title=\"Add Attachment\">Add Attachment</button>');";
                    //$script = $script."$('#dvIntView').html(body);";

                    // get rows
                    $script = $script."var lstBody=viewIntList(nme,agent,dte,dsc,resp.id);";
                    
                    // update counts
                    $script = $script."var ct=parseInt($('#viewInterviews').html());";
                    $script = $script."$('#viewInterviews').html(ct+1);";
                    $script = $script."$('#intCount').html(ct+1);";

                    // update case tab interview list
                    $script = $script."if(ct == 0){";
                    $script = $script."$('#tblIntList').html(lstBody);";
                    $script = $script."$('#tblIntListTab').html(lstBody);";
                    $script = $script."}else{";
                    $script = $script."$('#tblIntList').append(lstBody);";
                    $script = $script."$('#tblIntListTab').append(lstBody);";
                    $script = $script."}";

                    $script = $script."$('.ancintview[data=\"'+resp.id+'\"]').click();";

                    // close window
                    $script = $script."$('#btnCloseWin').click();";
                    
                $script = $script."}else{";
                $script = $script."$('#altMsgInt').addClass('alert-danger').html('An Error Occured: '+resp.msg).show();";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."});";
        $script = $script."});";

        // Edit Interview
        $script = $script."var buildEditInterview = function(name,desc,date,title,emp,street,city,state,zip,phone,dob,location,id) {";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Complete the form to add a new interview.</p>'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgInt\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"int_comp_id\" id=\"inv_comp_id\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<input type=\"hidden\" name=\"inv_case_id\" id=\"inv_comp_id\" value=\"".md5($case_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_int\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_int\" id=\"name_int\" value=\"'+name+'\" placeholder=\"Interviewee Name\">'";
        $script = $script."+'<span id=\"nameres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //dob
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"dob_int\" class=\"col-sm-2 control-label\">DOB</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"dob_int\" id=\"dob_int\" value=\"'+dob+'\" placeholder=\"Interviewee Date of Birth\">'";
        $script = $script."+'<span id=\"dobres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Title
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_int\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_int\" id=\"title_int\" value=\"'+title+'\" placeholder=\"Interviewee Title\">'";
        $script = $script."+'<span id=\"titleres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Employer
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"emp_int\" class=\"col-sm-2 control-label\">Employer</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"emp_int\" id=\"emp_int\" value=\"'+emp+'\" placeholder=\"Interviewee Employer\">'";
        $script = $script."+'<span id=\"empres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Address
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street_int\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street_int\" id=\"street_int\" value=\"'+street+'\" placeholder=\"Interviewee Street\">'";
        $script = $script."+'<span id=\"streetres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city_int\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city_int\" id=\"city_int\" value=\"'+city+'\" placeholder=\"Interviewee City\">'";
        $script = $script."+'<span id=\"cityres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"state_int\" class=\"col-sm-2 control-label\">State/Zip</label>'";
        $script = $script."+'<div class=\"col-sm-4\">'";
        //$script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"state_int\" id=\"state_int\" value=\"'+state+'\" placeholder=\"State\">'";
        //$script = $script."+'<span id=\"stateres_int\" class=\"input-group-addon\"></span>'";
        //$script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        //$script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"zip_int\" id=\"zip_int\" value=\"'+zip+'\" placeholder=\"Zip\">'";
        //$script = $script."+'<span id=\"zipres_int\" class=\"input-group-addon\"></span>'";
        //$script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        //Phone
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"phone_int\" class=\"col-sm-2 control-label\">Phone</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"phone_int\" id=\"phone_int\" value=\"'+phone+'\" placeholder=\"Interview Phone\">'";
        $script = $script."+'<span id=\"phoneres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<hr>'";

        //location
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"location_int\" class=\"col-sm-2 control-label\">Location</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"location_int\" id=\"location_int\" value=\"'+location+'\" placeholder=\"Interview Location\">'";
        $script = $script."+'<span id=\"locationres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"desc_int\" class=\"col-sm-2 control-label\">Desc</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<textarea class=\"form-control\" name=\"desc_int\" id=\"desc_int\" placeholder=\"Interview Description\">'+desc+'</textarea>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"date_int\" class=\"col-sm-2 control-label\">Date</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"date\" class=\"form-control\" name=\"date_int\" id=\"date_int\" value=\"'+date+'\" max=\"".date('Y-m-d')."\" data=\"".date('m/d/Y')."\" placeholder=\"\">'";
        $script = $script."+'<span id=\"dateres_int\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveEditInt\" data=\"'+id+'\" class=\"btn btn-success\">Save</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // handle edit menu button
        $script = $script."$(document).on('click','#btnEditInt',function(e){";
            $script = $script."var id=$(this).attr('data');";
            // Load Data
            $script = $script."var data = {intid:id};";
            $script = $script."postData('".site_url("data/interview")."',data,null,function(resp){";
                
                $script = $script."if(resp.result){";
                    // display interview
                    $script = $script."var interview=resp.interview;";
                    $script = $script."var name=interview.name;";
                    $script = $script."var desc=interview.description;";
                    $script = $script."var date=interview.date_occured;";

                    // title,emp,street,city,state,zip,phone
                    $script = $script."var int_title=interview.title;";
                    $script = $script."var int_emp=interview.emp;";
                    $script = $script."var int_street=interview.street;";
                    $script = $script."var int_city=interview.city;";
                    $script = $script."var int_state=interview.state;";
                    $script = $script."var int_zip=interview.zip;";
                    $script = $script."var int_phone=interview.phone;";

                    $script = $script."var int_dob=interview.dob;";
                    $script = $script."var int_location=interview.location;";

                    $script = $script."openModal('Edit Interview',buildEditInterview(name,desc,date,int_title,int_emp,int_street,int_city,int_state,int_zip,int_phone,int_dob,int_location,interview.id));";
                    $script = $script."$('#altMsgInt').hide();";

                    // validate data
                    // validate name
                    $script = $script."var name=$('#name_int');";
                    $script = $script."var nameres=$('#nameres_int');";
                    $script = $script."if(name.val().trim().indexOf(' ') != -1){";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."name.on('blur keyup focus', function() {";
                    $script = $script."if(name.val().length == 0){";
                    $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(name.val().length >= 1){";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."});";

                    // validate DOB
                    $script = $script."var dob=$('#dob_int');";
                    $script = $script."var dobres=$('#dobres_int');";
                    $script = $script."if(dob.val().length == 0){";
                    $script = $script."dobres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(dob.val().length >= 1){";
                    $script = $script."dobres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."dobres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."dob.on('blur keyup focus', function() {";
                    $script = $script."if(dob.val().length == 0){";
                    $script = $script."dobres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(dob.val().length >= 1){";
                    $script = $script."dobres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."dobres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."});";

                    // validate title
                    $script = $script."var title=$('#title_int');";
                    $script = $script."var titleres=$('#titleres_int');";
                    $script = $script."if(title.val().length == 0){";
                    $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(title.val().length >= 1){";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."title.on('blur keyup focus', function() {";
                    $script = $script."if(title.val().length == 0){";
                    $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(title.val().length >= 1){";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."});";

                    // validate employer
                    $script = $script."var emp=$('#emp_int');";
                    $script = $script."var empres=$('#empres_int');";
                    $script = $script."if(emp.val().length == 0){";
                    $script = $script."empres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(emp.val().length >= 1){";
                    $script = $script."empres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."empres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."emp.on('blur keyup focus', function() {";
                    $script = $script."if(emp.val().length == 0){";
                    $script = $script."empres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(emp.val().length >= 1){";
                    $script = $script."empres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."empres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
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

                    // validate street
                    $script = $script."var street=$('#street_int');";
                    $script = $script."var streetres=$('#streetres_int');";
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

                    // validate city
                    $script = $script."var city=$('#city_int');";
                    $script = $script."var cityres=$('#cityres_int');";
                    $script = $script."var state=$('#state_int');";
                    $script = $script."var zip=$('#zip_int');";
                    $script = $script."if(city.val().length == 0){";
                    $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(city.val().length >= 3){";
                    // ajax load zip codes
                    $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
                    $script = $script."if(data){";
                    $script = $script."var zipList=JSON.parse(data);";
                    $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
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
                    $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
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
                    $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
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
                    $script = $script."new Awesomplete(document.querySelector('#zip_int'),{ list: zipList });";
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
                    $script = $script."zip.on('blur keyup focus', function(e){";
                    $script = $script."if(zip.val().length == 0){";
                    $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
                    $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
                    $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
                    $script = $script."}else{";
                    $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
                    $script = $script."}";
                    $script = $script."});";

                    // validate phone
                    $script = $script."var phone=$('#phone_int');";
                    $script = $script."var phoneres=$('#phoneres_int');";
                    $script = $script."if(phone.val().length == 0){";
                    $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(phone.val().length >= 10){";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."phone.on('blur keyup focus', function() {";
                    $script = $script."if(phone.val().length == 0){";
                    $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else if(phone.val().length >= 10){";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."});";

                    // validate location
                    $script = $script."var loc=$('#location_int');";
                    $script = $script."var locres=$('#locationres_int');";
                    $script = $script."if(loc.val().length == 0){";
                    $script = $script."locres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(loc.val().length >= 1){";
                    $script = $script."locres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."locres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."loc.on('blur keyup focus', function() {";
                    $script = $script."if(loc.val().length == 0){";
                    $script = $script."locres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}else{";
                    $script = $script."if(loc.val().length >= 1){";
                    $script = $script."locres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    $script = $script."}else{";
                    $script = $script."locres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."}";
                    $script = $script."});";

                    // validate date
                    $script = $script."var checkdate = function(input){";
                    $script = $script."var validformat=/^\d{2}\/\d{2}\/\d{4}$/;";
                    $script = $script."if (!validformat.test(input.val())) {";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."var monthfield=input.val().split('/')[0];";
                    $script = $script."var dayfield=input.val().split('/')[1];";
                    $script = $script."var yearfield=input.val().split('/')[2];";
                    $script = $script."var dayobj = new Date(yearfield, monthfield-1, dayfield);";
                    $script = $script."if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {";
                    $script = $script."return false;";
                    $script = $script."}else{";
                    $script = $script."return true;";
                    $script = $script."}";
                    $script = $script."};";
                    // determine if browser supports date
                    $script = $script."var supportsHTML5Date = function() {";
                    $script = $script."var input = document.createElement('input');";
                    $script = $script."input.setAttribute('type','date');";
                    $script = $script."var notADateValue = 'not-a-date';";
                    $script = $script."input.setAttribute('value', notADateValue);";
                    $script = $script."return (input.value !== notADateValue);";
                    $script = $script."};";
                    // creation date validation for non html5 date supported browsers
                    $script = $script."var crdate=$('#date_int');";
                    $script = $script."var crdateres=$('#dateres_int');";
                    $script = $script."if (supportsHTML5Date() == false){";
                    $script = $script."if(crdate.val().length == 0){";
                    $script = $script."crdate.val(crdate.attr('data'));";
                    $script = $script."$('#viewCreationDate').html(crdate.val());";
                    $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."}";
                    $script = $script."if(checkdate(crdate)){";
                    $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                    $script = $script."var curDate = Date.now();";
                    $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    //$script = $script."if (selDate.getTime() <= curDate) {";
                    //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    //$script = $script."}else{";
                    //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    //$script = $script."}";
                    $script = $script."$('#viewCreationDate').html(crdate.val());";
                    $script = $script."}else{";
                    $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."$('#viewCreationDate').html('');";
                    $script = $script."}";
                    $script = $script."crdate.on('blur keyup focus', function() {";
                    $script = $script."if(crdate.val().length == 0){";
                    $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                    $script = $script."$('#viewCreationDate').html('');";
                    $script = $script."}else if(checkdate(crdate)){";
                    $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                    $script = $script."var curDate = Date.now();";
                    $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    //$script = $script."if (selDate.getTime() <= curDate) {";
                    //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                    //$script = $script."}else{";
                    //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    //$script = $script."}";
                    $script = $script."$('#viewCreationDate').html(crdate.val());";
                    $script = $script."}else{";
                    $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                    $script = $script."$('#viewCreationDate').html('');";
                    $script = $script."}";
                    $script = $script."});";
                    $script = $script."}";
                    //update bar with current date if date picker
                    $script = $script."if (supportsHTML5Date() == true){";
                    $script = $script."crdate.on('change blur keyup focus', function() {";
                    $script = $script."var dt=new Date(crdate.val());";
                    $script = $script."var m=(dt.getMonth()+1);";
                    $script = $script."if(m < 10){m=0+String(m)}";
                    $script = $script."var d=(dt.getDate()+1);";
                    $script = $script."if(d < 10){d=0+String(d)}";
                    $script = $script."var y=dt.getFullYear();";
                    $script = $script."var dtd=m+'/'+d+'/'+y;";
                    $script = $script."$('#viewCreationDate').html(dtd);";
                    $script = $script."});";
                    $script = $script."}";
                    // end validate

                    // save changes
                    $script = $script."$(document).on('click','#btnSaveEditInt',function(e){";
                        // interview_edit
                        $script = $script."var nme=name.val();";
                        $script = $script."var dsc=$('#desc_int').val();";
                        $script = $script."var dte=crdate.val();";
                        $script = $script."var intid=$(this).attr('data');";

                        $script = $script."var int_title=title.val();";
                        $script = $script."var int_emp=emp.val();";
                        $script = $script."var int_street=street.val();";
                        $script = $script."var int_city=city.val();";
                        $script = $script."var int_state=state.val();";
                        $script = $script."var int_zip=zip.val();";
                        $script = $script."var int_phone=phone.val();";

                        $script = $script."var int_dob=dob.val();";
                        $script = $script."var int_location=loc.val();";

                        $script = $script."var data = {iid:intid,name:nme,desc:dsc,dte_occured:dte,title:int_title,emp:int_emp,street:int_street,city:int_city,state:int_state,zip:int_zip,phone:int_phone,dob:int_dob,location:int_location};";
                        $script = $script."postData('".site_url("edit/interview_edit")."',data,null,function(resp){";
                        $script = $script."if(resp.result){";
                            // reload view
                            //$script = $script."var upBtn=$('.btnuploadadmin[data=\"'+id+'\"]');";
                            $script = $script."$('.ancintview[data=\"'+id+'\"]').click();";
                            // close window
                            $script = $script."$('#btnCloseWin').click();";
                        $script = $script."}else{";
                        $script = $script."$('#altMsgInt').addClass('alert-danger').html('An Error Occured: '+resp.msg).show();";
                        $script = $script."return false;";
                        $script = $script."}";
                        $script = $script."});";
                        // end edit
                    $script = $script."});";

                $script = $script."}else{";
                    $script = $script."alert('An Error Occured: '+resp.msg);";
                    $script = $script."return false;";
                $script = $script."}";

            $script = $script."});";
        $script = $script."});";
        // End Edit Interview

        // Interview Attachments
        $script = $script."$(document).on('click','.ancAttInt',function(e){";
            $script = $script."e.preventDefault();";
            $script = $script."var url=$(this).attr('href');";
            $script = $script."var name=$(this).html();";

            $script = $script."var winfrm='<div id=\"attintview\" class=\"responsive-doc\">Loading...</div>';";
            $script = $script."winfrm+='<button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>';";
            
            $script = $script."openModal('View '+name,winfrm);";

            $script = $script."var viewer='<iframe src=\"'+url+'\"></iframe>';";
            $script = $script."var ft=url.split('.');";
            $script = $script."if (ft.length > 0){";
            $script = $script."switch(ft[1]){";
            $script = $script."case 'png':";
            $script = $script."case 'jpg':";
            $script = $script."case 'jpeg': viewer='<iframe src=\"'+url+'\"></iframe>'; break;";
            $script = $script."case 'txt': viewer='<iframe src=\"'+url+'\"></iframe>'; break;";
            $script = $script."case 'xlsx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
            $script = $script."case 'docx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
            $script = $script."case 'pptx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
            $script = $script."case 'pdf': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
            $script = $script."case 'mp4': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
            $script = $script."case 'webm': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
            $script = $script."case 'ogv': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
            $script = $script."case 'mp3': viewer='<video controls><source src=\"'+url+'\" type=\"video/mp3\"></video>'; break;";
            $script = $script."}";
            $script = $script."}";
            $script = $script."$('#attintview').html(viewer);";

        $script = $script."});";

        // ATTACHMENTS
        $script = $script."$(document).on('click','.ancAtt',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var url=$(this).attr('href');";
        $script = $script."var name=$(this).html();";
        $script = $script."if(window.location.hash != '#attachments'){";
        $script = $script."window.location.hash = '#attachments';";
        $script = $script."}";
        // highlight selected row and update status text
        $script = $script."$('#tbodyAttMain').children('tr').removeClass('active');";
        $script = $script."$('#tbodyAtt').children('tr').removeClass('active');";
        $script = $script."$(this).parents('tr').addClass('active');";
        $script = $script."$('#attViewText').html('Loading...');";
        // determine file type
        $script = $script."var viewer='<iframe src=\"'+url+'\"></iframe>';";
        $script = $script."var ft=url.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'png':";
        $script = $script."case 'jpg':";
        $script = $script."case 'jpeg': viewer='<iframe src=\"'+url+'\"></iframe>'; break;";
        $script = $script."case 'txt': viewer='<iframe src=\"'+url+'\"></iframe>'; break;";
        $script = $script."case 'xlsx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'docx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'pptx': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'pdf': viewer='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'mp4': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
        $script = $script."case 'webm': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
        $script = $script."case 'ogv': viewer='<video controls><source src=\"'+url+'\" type=\"video/webm\"></video>'; break;";
        $script = $script."case 'mp3': viewer='<video controls><source src=\"'+url+'\" type=\"video/mp3\"></video>'; break;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."$('#attViewer').html(viewer);";
        $script = $script."$('#attViewText').html('View: '+name);";
        $script = $script."});";

        // ADMINISTRATIVE
        // handle click on doc
        $script = $script."$(document).on('click',\"[name='chkAdminDocs']\",function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var id=parseInt($(this).attr('data'));";
        $script = $script."var name=$(this).attr('value');";
        $script = $script."var is_other=false;";
        $script = $script."if(id==6 || id>=16){";
        $script = $script."is_other=true;";
        $script = $script."}";
        $script = $script."if($(this).is(':checked') == false){";
        // not checked
            $script = $script."var chk=$(this);";
            $script = $script."var docs=$(this).attr('docs');";
            $script = $script."var id=$(this).attr('data');";
            $script = $script."if(docs){";
            $script = $script."var data = {docid:docs};";
            $script = $script."postData('".site_url("edit/remove_document")."',data,null,function(res){";
            $script = $script."chk.removeAttr('checked');";
            $script = $script."chk.attr('docs','');";
            $script = $script."$('#spn_'+id).removeClass('badge').addClass('glyphicon glyphicon-ok text-success').html('');";
            $script = $script."});";
            $script = $script."}else{";
            $script = $script."chk.removeAttr('checked');";
            $script = $script."chk.attr('docs','');";
            $script = $script."}";
        $script = $script."}else{";
        // checked
            // determine if has file or not
            $script = $script."if($('#spn_'+id).hasClass('text-success') || $('#spn_'+id).hasClass('badge')){";
                $script = $script."var chk=$(this);";
                $script = $script."var avdocs=chk.attr('avdocs');";
                $script = $script."if(avdocs.includes(',')){";
                    // add multiple docs
                    $script = $script."var upBtn=$('.btnuploadadmin[data=\"'+id+'\"]');";
                    $script = $script."var cmid='".md5($company_id)."';";
                    $script = $script."var data={compid:cmid,doctypeid:id};";
                    $script = $script."postData('".site_url("data/available_docs")."',data,null,function(res){";
                    $script = $script."if(res.result){";
                    $script = $script."openModal('Select Document',buildMultipleAttach(name,res.docs,false));";
                    $script = $script."$('#altMsgMultAtt').hide();";
                    $script = $script."$('#btnUseMultAtt').on('click',function(){";
                    $script = $script."var userid='".md5($user_id)."';";
                    $script = $script."var cid='".$case_id."';";
                    $script = $script."var selDocs = '';";
                    $script = $script."var selCount = 0;";
                    $script = $script."$('#selAttFile :selected').each(function(i, selected){ ";
                    $script = $script."selCount = selCount+1;";
                    $script = $script."selDocs += $(selected).attr('id')+',';";
                    $script = $script."});";
                    $script = $script."selDocs = selDocs.replace(/,\s*$/, '');"; // remove last comma
                    $script = $script."if(!selDocs || selDocs == 0){";
                    $script = $script."$('#altMsgMultAtt').addClass('alert-danger').html('You must select a file').show();";
                    $script = $script."return false;";
                    $script = $script."}";
                    $script = $script."var data = {docid:selDocs,caseid:cid,uid:userid};";
                    $script = $script."postData('".site_url("add/attach_doc_to_case")."',data,$('#btnUseMultAtt'),function(res){";
                    $script = $script."chk.prop('checked', true);";
                    $script = $script."chk.attr('docs',selDocs);";
                    $script = $script."if(selCount > 1){";
                    $script = $script."$('#spn_'+id).removeClass('glyphicon glyphicon-ok text-success').addClass('badge').html(selCount);"; // display doc count
                    $script = $script."}";
                    $script = $script."$('#btnCloseWin').click();";
                    $script = $script."});";
                    $script = $script."});";
                    $script = $script."$('#btnUploadNew').on('click',function(){";
                    $script = $script."$('#btnCloseWin').click();";
                    $script = $script."upBtn.click();";
                    $script = $script."});";
                    $script = $script."}else{";
                    $script = $script."alert('An error occured. Unable to load documents');";
                    $script = $script."}";
                    $script = $script."});";
                $script = $script."}else{";
                    // add single doc
                    $script = $script."var userid='".md5($user_id)."';";
                    $script = $script."var cid='".$case_id."';";
                    $script = $script."var data = {docid:avdocs,caseid:cid,uid:userid};";
                    $script = $script."postData('".site_url("add/attach_doc_to_case")."',data,null,function(res){";
                    $script = $script."chk.prop('checked', true);";
                    $script = $script."chk.attr('docs',avdocs);";
                    $script = $script."});";
                $script = $script."}";
            $script = $script."}else{";
                // upload file
                $script = $script."openModal('Import A Document',buildDocUpload(name,id,is_other));";
                $script = $script."$('#altMsgImpDoc').hide();";
                $script = $script."$('#prgHolderDoc').hide();";
                $script = $script."$('#title_doc').on('blur keyup focus', function(){";
                $script = $script."var title=$('#title_doc');";
                $script = $script."var titleres=$('#titleres_doc');";
                $script = $script."if(title.val().length == 0){";
                $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                $script = $script."}else if(title.val().length >= 3){";
                $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                $script = $script."}else{";
                $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                $script = $script."}";
                $script = $script."});";
                $script = $script."$('#fleImpDoc').on('change', function(){";
                $script = $script."readURLFormDoc(this,id,function(ret){";
                $script = $script."if(ret['data']){";
                $script = $script."var elem = presentFile(ret['name']);";
                $script = $script."if(elem){";
                $script = $script."$('#fleDragDrop').html(elem);";
                // store file object to global
                $script = $script."curFileObj=ret;";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
                $script = $script."}";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
                $script = $script."}";
                $script = $script."});";
                $script = $script."});";
                $script = $script."$('#fleDragDrop').on('drop', function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."e.stopPropagation();";
                $script = $script."$(this).css('border', '1px solid #f9f9f9');";
                $script = $script."var files = e.originalEvent.dataTransfer.files;";
                $script = $script."readURLDropDoc(files,id,function(ret){";
                $script = $script."if(ret['data']){";
                $script = $script."var elem = presentFile(ret['name']);";
                // store file object to global
                $script = $script."curFileObj=ret;";
                $script = $script."if(elem){";
                $script = $script."$('#fleDragDrop').html(elem);";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
                $script = $script."}";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
                $script = $script."}";
                $script = $script."});";
                $script = $script."});";
                // save file
                $script = $script."$('#btnSaveImpDoc').on('click', function(){";
                $script = $script."var btn=$(this);";
                $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
                $script = $script."$('#altMsgImpDoc').removeClass('alert-danger').html('').hide();";
                $script = $script."var title=$('#title_dv').html();";
                $script = $script."var titleres=$('#titleres_doc');";
                $script = $script."if(is_other && titleres.hasClass('pw_strong')){";
                $script = $script."title=$('#title_doc').val();";
                $script = $script."if(!title){";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."}else if(is_other){";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (curFileObj)) {";
                // add title to object
                $script = $script."curFileObj.title=title;";
                // save file via AJAX
                $script = $script."var userid='".md5($user_id)."';";
                $script = $script."var cid='".$case_id."';";
                $script = $script."var cmid='".$company_id."';";
                $script = $script."var data = {compid:cmid,caseid:cid,uid:userid,file:curFileObj};";
                $script = $script."postData('".site_url("add/add_admin_doc")."',data,btn,function(res){";
                $script = $script."if(res.result){";
                // reload documents
                $script = $script."var cmid='".md5($company_id)."';";
                $script = $script."var data = {compid:cmid,caseid:cid};";
                $script = $script."postData('".site_url("data/documents")."',data,btn,function(res){";
                $script = $script."if(res.result){";
                // HANDLE REBUILD ROWS
                $script = $script."addDocumentRows($('#tblAdminDocs'),res.admin_docs);";
                $script = $script."$('#btnCloseWin').click();";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
                $script = $script."}else{";
                $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('A valid file is required').show();";
                $script = $script."btn.removeAttr('disabled').html('Add');";
                $script = $script."return false;";
                $script = $script."}";
                $script = $script."});";
            $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // manage upload button
        $script = $script."$(document).on('click','.btnuploadadmin',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var id=parseInt($(this).attr('data'));";
        $script = $script."var name=$('#chk_'+id).attr('value');";
        $script = $script."var is_other=false;";
        $script = $script."if(id==6 || id>=16){";
        $script = $script."is_other=true;";
        $script = $script."}";
        // upload file
        $script = $script."openModal('Import A Document',buildDocUpload(name,id,is_other));";
        $script = $script."$('#altMsgImpDoc').hide();";
        $script = $script."$('#prgHolderDoc').hide();";
        $script = $script."$('#title_doc').on('blur keyup focus', function(){";
        $script = $script."var title=$('#title_doc');";
        $script = $script."var titleres=$('#titleres_doc');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#fleImpDoc').on('change', function(){";
        $script = $script."readURLFormDoc(this,id,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."$('#fleDragDrop').on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDropDoc(files,id,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // save file
        $script = $script."$('#btnSaveImpDoc').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."btn.attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpDoc').removeClass('alert-danger').html('').hide();";
        $script = $script."var title=$('#title_dv').html();";
        $script = $script."var titleres=$('#titleres_doc');";
        $script = $script."if(is_other && titleres.hasClass('pw_strong')){";
        $script = $script."title=$('#title_doc').val();";
        $script = $script."if(!title){";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}else if(is_other){";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('Title required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (curFileObj)) {";
        // add title to object
        $script = $script."curFileObj.title=title;";
        // save file via AJAX
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var cid='".$case_id."';";
        $script = $script."var cmid='".$company_id."';";
        $script = $script."var data = {compid:cmid,caseid:cid,uid:userid,file:curFileObj};";
        $script = $script."postData('".site_url("add/add_admin_doc")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // reload documents
        $script = $script."var cmid='".md5($company_id)."';";
        $script = $script."var data = {compid:cmid,caseid:cid};";
        $script = $script."postData('".site_url("data/documents")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // HANDLE REBUILD ROWS
        $script = $script."addDocumentRows($('#tblAdminDocs'),res.admin_docs);";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpDoc').addClass('alert-danger').html('A valid file is required').show();";
        $script = $script."btn.removeAttr('disabled').html('Add');";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."});";
        // end upload
        $script = $script."});";
        // handle Add Other Row
        $script = $script."$('#btnAddAdminDocs').on('click',function(e){";
        $script = $script."e.preventDefault();";
        // get count of rows
        $script = $script."var ct = $('#tblAdminDocs').children('tr').length;";
        $script = $script."var nct=(ct+13);";
        $script = $script."if(nct >= 27){ $('#btnAddAdminDocs').hide() }";
        $script = $script."if(nct > 27){return false;}";
        $script = $script."var row='<tr>'";
        $script = $script."+'<td><input type=\"checkbox\" id=\"chk_'+nct+'\" avdocs=\"\" docs=\"\" name=\"chkAdminDocs\" data=\"'+nct+'\" value=\"Other\"></td>'";
        $script = $script."+'<td title=\"\">Other</td>'";
        $script = $script."+'<td><span class=\"glyphicon glyphicon-plus text-warning\" id=\"spn_'+nct+'\"></span></td>'";
        $script = $script."+'<td><button type=\"button\" class=\"btn btn-info btn-xs btnuploadadmin\" cat=\"2\" data=\"'+nct+'\">New</button></td>'";
        $script = $script."+'</tr>';";
        $script = $script."$('#tblAdminDocs').append(row);";
        $script = $script."});";
        // multiple file view
        $script = $script."$(document).on('click','.ancDocViewAdmin',function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."var ids=$(this).attr('docs');";
        $script = $script."var name=$(this).html();";
        $script = $script."var data = {docids:ids};";
        $script = $script."postData('".site_url("data/view_docs")."',data,null,function(res){";
        $script = $script."if(res.result){";
        $script = $script."openModal('Document',buildMultiViewDocument(name,res.docs));";
        $script = $script."}else{";
        $script = $script."alert('An Error Occured: '+res.msg);";
        $script = $script."}";
        $script = $script."});";

        
        $script = $script."});";
        // end Administration


        // end script
        $script = $script."});";

        return $script;
    }
}

if (!function_exists('new_case_script'))
{
    function new_case_script($company_id, $user_id, $team)
    {
        $script = "$(document).ready(function() {";

        $script = $script."$('#tableLeadSheet').hide();";
        $script = $script."$('#tableSupDocs').hide();";
        $script = $script."$('#altMsgMain').hide();";

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

        // global functions
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

        // handle leave page
        $script = $script."var isFormSaved = false;";
        $script = $script."window.onload = function() {";
        $script = $script."window.addEventListener('beforeunload', function(e){";
        $script = $script."if (isFormSaved) {";
        $script = $script."return undefined;";
        $script = $script."}";
        $script = $script."var confirmationMessage = 'All changes will be lost. Are You Sure?';";
        $script = $script."(e || window.event).returnValue = confirmationMessage;";
        // clear out any local storage if leaving page
        $script = $script."if(typeof(Storage)!=='undefined'){";
        $script = $script."localStorage.removeItem('le_".md5($user_id)."');";
        $script = $script."}";
        $script = $script."return confirmationMessage;";
        $script = $script."});";
        $script = $script."};";
        // handle cancel
        $script = $script."$('.edtcancel').on('click', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."window.location.href = '".site_url('mycases')."';";
        $script = $script."});";

        // panel and buttons for show hide of form
        $script = $script."var btnClientInfo = $('#btnClientInfo');";
        $script = $script."var pnlClientInfo = $('#pnlClientInfo');";
        $script = $script."var clClientInfo = $('#clClientInfo');";
        $script = $script."var btnCaseAdmin = $('#btnCaseAdmin');";
        $script = $script."var pnlCaseAdmin = $('#pnlCaseAdmin');";
        $script = $script."var clCaseAdmin = $('#clCaseAdmin');";
        $script = $script."var btnTeam = $('#btnTeam');";
        $script = $script."var pnlTeam = $('#pnlTeam');";
        $script = $script."var clTeam = $('#clTeam');";
        $script = $script."var btnCreation = $('#btnCreation');";
        $script = $script."var pnlCreation = $('#pnlCreation');";
        $script = $script."var clCreation = $('#clCreation');";
        $script = $script."var btnAttorney = $('#btnAttorney');";
        $script = $script."var pnlAttorney = $('#pnlAttorney');";
        $script = $script."var clAttorney = $('#clAttorney');";
        $script = $script."var btnCPA = $('#btnCPA');";
        $script = $script."var pnlCPA = $('#pnlCPA');";
        $script = $script."var clCPA = $('#clCPA');";
        $script = $script."var btnLeAgency = $('#btnLeAgency');";
        $script = $script."var pnlLeAgency = $('#pnlLeAgency');";
        $script = $script."var clLeAgency = $('#clLeAgency');";
        $script = $script."var btnDistAttorney = $('#btnDistAttorney');";
        $script = $script."var pnlDistAttorney = $('#pnlDistAttorney');";
        $script = $script."var clDistAttorney = $('#clDistAttorney');";
        $script = $script."var btnPredication = $('#btnPredication');";
        $script = $script."var pnlPredication = $('#pnlPredication');";
        $script = $script."var clPredication = $('#clPredication');";

        // hide all panels by default
        $script = $script."pnlClientInfo.hide();";
        $script = $script."pnlCaseAdmin.hide();";
        $script = $script."pnlTeam.hide();";
        $script = $script."pnlCreation.hide();";
        $script = $script."pnlAttorney.hide();";
        $script = $script."pnlCPA.hide();";
        $script = $script."pnlLeAgency.hide();";
        $script = $script."pnlDistAttorney.hide();";
        $script = $script."pnlPredication.hide();";

        // function to hide all
        $script = $script."var hideAllPanels = function(){";
        $script = $script."if(pnlClientInfo.is(':visible')){";
        $script = $script."btnClientInfo.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlClientInfo.hide();";
        $script = $script."$('#udClientInfo').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlCaseAdmin.is(':visible')){";
        $script = $script."btnCaseAdmin.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCaseAdmin.hide();";
        $script = $script."$('#udCaseAdmin').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlTeam.is(':visible')){";
        $script = $script."btnTeam.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlTeam.hide();";
        $script = $script."$('#udTeam').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlCreation.is(':visible')){";
        $script = $script."btnCreation.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCreation.hide();";
        $script = $script."$('#udCreation').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlAttorney.is(':visible')){";
        $script = $script."btnAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlAttorney.hide();";
        $script = $script."$('#udAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlCPA.is(':visible')){";
        $script = $script."btnCPA.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCPA.hide();";
        $script = $script."$('#udCPA').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlLeAgency.is(':visible')){";
        $script = $script."btnLeAgency.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlLeAgency.hide();";
        $script = $script."$('#udLeAgency').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlDistAttorney.is(':visible')){";
        $script = $script."btnDistAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlDistAttorney.hide();";
        $script = $script."$('#udDistAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."if(pnlPredication.is(':visible')){";
        $script = $script."btnPredication.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlPredication.hide();";
        $script = $script."$('#udPredication').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."};";

        // manage open and close of form panels
        $script = $script."btnClientInfo.on('click', function(){";
        $script = $script."if(pnlClientInfo.is(':visible')){";
        $script = $script."btnClientInfo.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlClientInfo.hide();";
        $script = $script."$('#udClientInfo').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnClientInfo.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlClientInfo.show();";
        $script = $script."$('#udClientInfo').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clClientInfo.on('click', function(){";
        $script = $script."btnClientInfo.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlClientInfo.hide();";
        $script = $script."$('#udClientInfo').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnCaseAdmin.on('click', function(){";
        $script = $script."if(pnlCaseAdmin.is(':visible')){";
        $script = $script."btnCaseAdmin.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCaseAdmin.hide();";
        $script = $script."$('#udCaseAdmin').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnCaseAdmin.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlCaseAdmin.show();";
        $script = $script."$('#udCaseAdmin').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clCaseAdmin.on('click', function(){";
        $script = $script."btnCaseAdmin.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCaseAdmin.hide();";
        $script = $script."$('#udCaseAdmin').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnTeam.on('click', function(){";
        $script = $script."if(pnlTeam.is(':visible')){";
        $script = $script."btnTeam.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlTeam.hide();";
        $script = $script."$('#udTeam').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnTeam.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlTeam.show();";
        $script = $script."$('#udTeam').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clTeam.on('click', function(){";
        $script = $script."btnTeam.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlTeam.hide();";
        $script = $script."$('#udTeam').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnCreation.on('click', function(){";
        $script = $script."if(pnlCreation.is(':visible')){";
        $script = $script."btnCreation.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCreation.hide();";
        $script = $script."$('#udCreation').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnCreation.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlCreation.show();";
        $script = $script."$('#udCreation').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clCreation.on('click', function(){";
        $script = $script."btnCreation.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCreation.hide();";
        $script = $script."$('#udCreation').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnAttorney.on('click', function(){";
        $script = $script."if(pnlAttorney.is(':visible')){";
        $script = $script."btnAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlAttorney.hide();";
        $script = $script."$('#udAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnAttorney.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlAttorney.show();";
        $script = $script."$('#udAttorney').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clAttorney.on('click', function(){";
        $script = $script."btnAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlAttorney.hide();";
        $script = $script."$('#udAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnCPA.on('click', function(){";
        $script = $script."if(pnlCPA.is(':visible')){";
        $script = $script."btnCPA.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCPA.hide();";
        $script = $script."$('#udCPA').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnCPA.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlCPA.show();";
        $script = $script."$('#udCPA').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clCPA.on('click', function(){";
        $script = $script."btnCPA.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlCPA.hide();";
        $script = $script."$('#udCPA').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnLeAgency.on('click', function(){";
        $script = $script."if(pnlLeAgency.is(':visible')){";
        $script = $script."btnLeAgency.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlLeAgency.hide();";
        $script = $script."$('#udLeAgency').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnLeAgency.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlLeAgency.show();";
        $script = $script."$('#udLeAgency').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clLeAgency.on('click', function(){";
        $script = $script."btnLeAgency.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlLeAgency.hide();";
        $script = $script."$('#udLeAgency').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnDistAttorney.on('click', function(){";
        $script = $script."if(pnlDistAttorney.is(':visible')){";
        $script = $script."btnDistAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlDistAttorney.hide();";
        $script = $script."$('#udDistAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnDistAttorney.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlDistAttorney.show();";
        $script = $script."$('#udDistAttorney').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clDistAttorney.on('click', function(){";
        $script = $script."btnDistAttorney.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlDistAttorney.hide();";
        $script = $script."$('#udDistAttorney').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        $script = $script."btnPredication.on('click', function(){";
        $script = $script."if(pnlPredication.is(':visible')){";
        $script = $script."btnPredication.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlPredication.hide();";
        $script = $script."$('#udPredication').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."}else{";
        $script = $script."btnPredication.addClass('list-group-item-info').removeClass('list-group-item-plain');";
        $script = $script."pnlPredication.show();";
        $script = $script."$('#udPredication').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down');";
        $script = $script."}";
        $script = $script."clPredication.on('click', function(){";
        $script = $script."btnPredication.removeClass('list-group-item-info').addClass('list-group-item-plain');";
        $script = $script."pnlPredication.hide();";
        $script = $script."$('#udPredication').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');";
        $script = $script."});";
        $script = $script."});";

        // form validation
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
        
        // validate case name
        $script = $script."var cname=$('#cname');";
        $script = $script."var cnameres=$('#cnameres');";
        $script = $script."if(cname.val().length == 0){";
        $script = $script."cnameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(cname.val().length >= 3){";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."cname.on('blur keyup focus', function(e) {";
        // handle tab and enter key open next
        $script = $script."if(e.which == 13 || e.which == 9){";
        $script = $script."e.preventDefault();";
        $script = $script."btnClientInfo.click();";
        $script = $script."company.focus();";
        $script = $script."}";
        $script = $script."if(cname.val().length == 0){";
        $script = $script."cnameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(cname.val().length >= 3){";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."cnameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";

        // validate company
        $script = $script."var company=$('#company');";
        $script = $script."var cmpres=$('#cmpres');";
        $script = $script."if(company.val().length == 0){";
        $script = $script."cmpres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."$('#viewClientInfo').html('');";
        $script = $script."}else if(company.val().length >= 3){";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."$('#viewClientInfo').html(company.val());";
        $script = $script."}else{";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewClientInfo').html('');";
        $script = $script."}";
        $script = $script."company.on('blur keyup focus', function(e) {";
        $script = $script."if(company.val().length == 0){";
        $script = $script."cmpres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."$('#viewClientInfo').html('');";
        $script = $script."}else if(company.val().length >= 3){";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."$('#viewClientInfo').html(company.val());";
        $script = $script."}else{";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewClientInfo').html('');";
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
        $script = $script."zip.on('blur keyup focus', function(e){";
        // handle tab and enter key open next
        $script = $script."if(e.which == 13 || e.which == 9){";
        $script = $script."e.preventDefault();";
        $script = $script."btnCaseAdmin.click();";
        $script = $script."}";
        $script = $script."if(zip.val().length == 0){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}else if(zip.val().length == 5 && Number.isInteger(filterInt(zip.val()))){";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_weak');";
        $script = $script."}";
        $script = $script."});";

        // load company from clients
        $script = $script."$('#selExistingClient').on('change',function(){";
        $script = $script."var id=$('#selExistingClient :selected').attr('data');";
        $script = $script."var nameval=$('#selExistingClient :selected').attr('name');";
        $script = $script."var streetval=$('#selExistingClient :selected').attr('street');";
        $script = $script."var cityval=$('#selExistingClient :selected').attr('city');";
        $script = $script."var stateval=$('#selExistingClient :selected').attr('state');";
        $script = $script."var zipval=$('#selExistingClient :selected').attr('zip');";
        $script = $script."if(id && nameval && streetval && cityval && stateval && zipval){";
        $script = $script."$('#viewClientInfo').html(nameval);";
        $script = $script."$('#company').attr('value',nameval);";
        $script = $script."$('#street').attr('value',streetval);";
        $script = $script."$('#city').attr('value',cityval);";
        $script = $script."$('#state').attr('value',stateval);";
        $script = $script."$('#zip').attr('value',zipval);";
        $script = $script."cmpres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."streetres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."cityres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak').addClass('br_strong');";
        $script = $script."}else{";
        $script = $script."$('#company').attr('value','');";
        $script = $script."$('#street').attr('value','');";
        $script = $script."$('#city').attr('value','');";
        $script = $script."$('#state').attr('value','');";
        $script = $script."$('#zip').attr('value','');";
        $script = $script."cmpres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."streetres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."state.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."zip.removeClass('br_strong').removeClass('br_weak');";
        $script = $script."}";
        $script = $script."});";
        // handle refresh page to bring back to default
        $script = $script."$('#selExistingClient').val('');";

        // validate creation date
        $script = $script."var checkdate = function(input){";
        $script = $script."var validformat=/^\d{2}\/\d{2}\/\d{4}$/;";
        $script = $script."if (!validformat.test(input.val())) {";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."var monthfield=input.val().split('/')[0];";
        $script = $script."var dayfield=input.val().split('/')[1];";
        $script = $script."var yearfield=input.val().split('/')[2];";
        $script = $script."var dayobj = new Date(yearfield, monthfield-1, dayfield);";
        $script = $script."if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return true;";
        $script = $script."}";
        $script = $script."};";
        // determine if browser supports date
        $script = $script."var supportsHTML5Date = function() {";
        $script = $script."var input = document.createElement('input');";
        $script = $script."input.setAttribute('type','date');";
        $script = $script."var notADateValue = 'not-a-date';";
        $script = $script."input.setAttribute('value', notADateValue);";
        $script = $script."return (input.value !== notADateValue);";
        $script = $script."};";
        // creation date validation for non html5 date supported browsers
        $script = $script."var crdate=$('#creation');";
        $script = $script."var crdateres=$('#creationres');";
        $script = $script."if (supportsHTML5Date() == false){";
        $script = $script."if(crdate.val().length == 0){";
        $script = $script."crdate.val(crdate.attr('data'));";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}";
        $script = $script."if(checkdate(crdate)){";
        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
        $script = $script."var curDate = Date.now();";
        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."if (selDate.getTime() <= curDate) {";
        //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."}else{";
        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}";
        $script = $script."crdate.on('blur keyup focus', function() {";
        $script = $script."if(crdate.val().length == 0){";
        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}else if(checkdate(crdate)){";
        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
        $script = $script."var curDate = Date.now();";
        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."if (selDate.getTime() <= curDate) {";
        //$script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."$('#viewCreationDate').html(crdate.val());";
        $script = $script."}else{";
        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}";
        //update bar with current date if date picker
        $script = $script."if (supportsHTML5Date() == true){";
        $script = $script."crdate.on('change blur keyup focus', function() {";
        $script = $script."var dt=new Date(crdate.val());";
        $script = $script."var m=(dt.getMonth()+1);";
        $script = $script."if(m < 10){m=0+String(m)}";
        $script = $script."var d=(dt.getDate()+1);";
        $script = $script."if(d < 10){d=0+String(d)}";
        $script = $script."var y=dt.getFullYear();";
        $script = $script."var dtd=m+'/'+d+'/'+y;";
        $script = $script."$('#viewCreationDate').html(dtd);";
        $script = $script."});";
        $script = $script."}";

        // validate predication
        /*
        $script = $script."var pred=$('#predication');";
        $script = $script."var predres=$('#predres');";
        $script = $script."if(pred.val().length == 0){";
        $script = $script."$('#viewPredication').html('');";
        $script = $script."predres.html('').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(pred.val().trim().indexOf(' ') != -1){";
        $script = $script."if(pred.val().length >= 25){";
        $script = $script."predres.html('Looks good. <span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."$('#viewPredication').html(pred.val().substring(0, 10)+'...');";
        $script = $script."}else{";
        $script = $script."predres.html('A little longer, this is a narrative. <span class=\"glyphicon glyphicon-warning-sign pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_moderate');";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."predres.html('This is required <span class=\"glyphicon glyphicon-remove pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."pred.on('blur keyup focus', function() {";
        $script = $script."if(pred.val().length == 0){";
        $script = $script."$('#viewPredication').html('');";
        $script = $script."predres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(pred.val().trim().indexOf(' ') != -1){";
        $script = $script."if(pred.val().length >= 15){";
        $script = $script."predres.html('Looks good. <span class=\"glyphicon glyphicon-ok pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."$('#viewPredication').html(pred.val().substring(0, 10)+'...');";
        $script = $script."}else{";
        $script = $script."$('#viewPredication').html('');";
        $script = $script."predres.html('A little longer, this is a narrative. <span class=\"glyphicon glyphicon-warning-sign pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_moderate');";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#viewPredication').html('');";
        $script = $script."predres.html('This is required  <span class=\"glyphicon glyphicon-remove pull-right\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_moderate').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        */
        
        // team and case manager
        $script = $script."var teamCount=1;";

        // case admin selection
        $script = $script."$('#hdCaseAdmin').val($('.rdoAdmin:checked').attr('value'));";
        $script = $script."$('.rdoAdmin').on('click', function(){";
        $script = $script."var name=$(this).attr('data');";
        $script = $script."var id=$(this).attr('value');";
        $script = $script."$('#viewCaseAdmin').html(name);";
        $script = $script."$('#hdCaseAdmin').val(id);";
        $script = $script."var tchk = $('.chkteam[value='+id+']');";
        $script = $script."if(!tchk.is(':checked')){";
        $script = $script."$('.chkteam[value='+id+']').prop('checked', true);";
        $script = $script."teamCount=$('.chkteam:checked').length;";
        $script = $script."$('#viewTeamCount').html(teamCount);";
        // open team if it is closed
        $script = $script."if(btnTeam.hasClass('list-group-item-plain')){";
        $script = $script."$('#btnTeam').click();";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";

        // team add / remove
        $script = $script."teamCount=$('.chkteam:checked').length;";
        $script = $script."$('#viewTeamCount').html(teamCount);";
        $script = $script."$('.chkteam').on('click', function(){";
        $script = $script."$('#msgTeam').html('');";
        $script = $script."var id=$(this).attr('value');";
        $script = $script."if(this.checked == true) {";
        $script = $script."teamCount=$('.chkteam:checked').length;";
        $script = $script."}else{";
        // ensure admin not removed from team
        $script = $script."var ardo = $('.rdoAdmin[value='+id+']');";
        $script = $script."if(ardo.is(':checked')){";
        $script = $script."$('#msgTeam').html('ERROR: You cannot remove the Case Admin.');";
        $script = $script."}else{";
        $script = $script."teamCount=$('.chkteam:checked').length;";
        $script = $script."if(teamCount == 0){";
        $script = $script."$('#msgTeam').html('ERROR: Case Admin is required on team.');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}";
        $script = $script."$('#viewTeamCount').html(teamCount);";
        $script = $script."});";

        // Supporting: handle attorney, cpa, LE Agency, District Attorney
        $script = $script."var buildSupporting = function(profession) {";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<p style=\"margin-top:5px;\">Complete the form to add a new '+profession+'.</p>'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgSup\" role=\"alert\"></div>'";
        $script = $script."+'<input type=\"hidden\" name=\"inv_comp_id\" id=\"inv_comp_id\" value=\"".md5($company_id)."\">'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_sup\" class=\"col-sm-2 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_sup\" id=\"name_sup\" value=\"\" placeholder=\"First and Last Name\">'";
        $script = $script."+'<span id=\"nameres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_sup\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_sup\" id=\"title_sup\" value=\"\" placeholder=\"Title\">'";
        $script = $script."+'<span id=\"titleres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"street_sup\" class=\"col-sm-2 control-label\">Street</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"street_sup\" id=\"street_sup\" value=\"\" placeholder=\"Street Address\">'";
        $script = $script."+'<span id=\"streetres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"city_sup\" class=\"col-sm-2 control-label\">City</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"city_sup\" id=\"city_sup\" value=\"\" placeholder=\"City\">'";
        $script = $script."+'<span id=\"cityres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<label for=\"state_sup\" class=\"col-sm-2 control-label\">State</label>'";
        $script = $script."+'<div class=\"col-sm-2\">'";
        $script = $script."+'<input type=\"text\" size=\"2\" class=\"form-control\" name=\"state_sup\" id=\"state_sup\" value=\"\" placeholder=\"State\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"zip_sup\" class=\"col-sm-2 control-label\">Zipcode</label>'";
        $script = $script."+'<div class=\"col-sm-6\">'";
        $script = $script."+'<input type=\"text\" size=\"5\" class=\"form-control\" name=\"zip_sup\" id=\"zip_sup\" value=\"\" placeholder=\"5 digit Zipcode\">'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"phone_sup\" class=\"col-sm-2 control-label\">Phone</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"phone_sup\" id=\"phone_sup\" value=\"\" placeholder=\"Telephone\">'";
        $script = $script."+'<span id=\"phoneres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"email_sup\" class=\"col-sm-2 control-label\">Email</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"email_sup\" id=\"email_sup\" value=\"\" placeholder=\"Email\">'";
        $script = $script."+'<span id=\"emailres_sup\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";

        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnAddNewSup\" class=\"btn btn-success\">Add</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";

        // validate supporting
        $script = $script."var validateSup = function() {";
        $script = $script."var stinp=document.getElementById('state_sup');";
        $script = $script."var statesel=new Awesomplete(stinp,{minChars:1,maxItems:5});";
        $script = $script."statesel.list=['AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','PR','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];";
        // validate name
        $script = $script."var name=$('#name_sup');";
        $script = $script."var nameres=$('#nameres_sup');";
        $script = $script."name.on('blur keyup focus', function() {";
        $script = $script."if(name.val().length == 0){";
        $script = $script."nameres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(name.val().length >= 1){";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."nameres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // validate title
        $script = $script."var title=$('#title_sup');";
        $script = $script."var titleres=$('#titleres_sup');";
        $script = $script."title.on('blur keyup focus', function() {";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 1){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        // validate street
        $script = $script."var street=$('#street_sup');";
        $script = $script."var streetres=$('#streetres_sup');";
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
        $script = $script."var city=$('#city_sup');";
        $script = $script."var cityres=$('#cityres_sup');";
        $script = $script."var state=$('#state_sup');";
        $script = $script."var zip=$('#zip_sup');";
        $script = $script."city.on('blur keyup focus change', function() {";
        $script = $script."if(city.val().length == 0){";
        $script = $script."cityres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(city.val().length >= 3){";
        // ajax load zip codes
        $script = $script."loadZipData('".site_url("data/zipcode")."', city.val(), state.val(), function(data){";
        $script = $script."if(data){";
        $script = $script."var zipList=JSON.parse(data);";
        $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
        $script = $script."new Awesomplete(document.querySelector('#zip_sup'),{ list: zipList });";
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
        // validate phone
        $script = $script."var phone=$('#phone_sup');";
        $script = $script."var phoneres=$('#phoneres_sup');";
        $script = $script."phone.on('blur keyup focus', function() {";
        $script = $script."if(phone.val().length == 0){";
        $script = $script."phoneres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(phone.val().length >= 10){";
        $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."phoneres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        // validate email
        $script = $script."var email=$('#email_sup');";
        $script = $script."var emailres=$('#emailres_sup');";
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
        $script = $script."};";

        // supplimental isvalid
        $script = $script."var isSupValid = function(nres,tres,sres,cres,stres,zres) {";
        $script = $script."var retvalue=false;";
        $script = $script."if(nres.hasClass('pw_strong') && tres.hasClass('pw_strong') && cres.hasClass('pw_strong') && stres.hasClass('br_strong') && zres.hasClass('br_strong')){";
        $script = $script."retvalue=true;";
        $script = $script."}";
        $script = $script."return retvalue;";
        $script = $script."};";

        // supplimental temp save
        $script = $script."var saveSup = function(name,title,street,city,state,zip,phone,email) {";
        $script = $script."var obj = {};";
        $script = $script."obj.name=name;";
        $script = $script."obj.title=title;";
        $script = $script."obj.street=street;";
        $script = $script."obj.city=city;";
        $script = $script."obj.state=state;";
        $script = $script."obj.zip=zip;";
        $script = $script."obj.phone=phone;";
        $script = $script."obj.email=email;";
        $script = $script."return obj;";
        $script = $script."};";

        // supplimental display radio
        $script = $script."var addSupRadio = function(view,pre,cont,rdoname,id,name,title,city,state) {";
        $script = $script."var rdo = '<div class=\"radio\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"radio\" name=\"'+rdoname+'\" id=\"'+pre+id+'\" value=\"'+id+'\">'";
        $script = $script."+''+name+' '+title+' ('+city+', '+state+')';";
        $script = $script."+'</label>'";
        $script = $script."+'</div>';";
        $script = $script."if(cont.html().includes('[No')){";
        $script = $script."cont.html(rdo);";
        $script = $script."}else{";
        $script = $script."cont.append(rdo);";
        $script = $script."}";
        $script = $script."$('#'+pre+id).prop('checked', true);";
        $script = $script."view.html(name);";
        $script = $script."};";

        //attorney
        $script = $script."var attorney=[];";
        $script = $script."var btnAddNewAttorney=$('#btnAddNewAttorney');";
        $script = $script."btnAddNewAttorney.on('click', function(){";
        $script = $script."openModal('Add New Attorney',buildSupporting('Attorney'));";
        $script = $script."$('#altMsgSup').removeClass('alert-danger').html('').hide();";
        $script = $script."validateSup();";
        $script = $script."$('#btnAddNewSup').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var btntxt = btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."if(isSupValid($('#nameres_sup'),$('#titleres_sup'),$('#streetres_sup'),$('#cityres_sup'),$('#state_sup'),$('#zip_sup'))) {";
        $script = $script."attorney.push(saveSup($('#name_sup').val(),$('#title_sup').val(),$('#street_sup').val(),$('#city_sup').val(),$('#state_sup').val(),$('#zip_sup').val(),$('#phone_sup').val(),$('#email_sup').val()));";
        $script = $script."var id=(attorney.length)-1;";
        $script = $script."addSupRadio($('#viewAttorney'),'at_',$('#selAttorney'),'rdoAttorney',id,$('#name_sup').val(),$('#title_sup').val(),$('#city_sup').val(),$('#state_sup').val());";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgSup').addClass('alert-danger').html('Invalid or empty fields. Please correct.').show();";
        $script = $script."}";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."});";
        // handle click change
        $script = $script."$('input[name=rdoAttorney]').on('click',function(){";
        $script = $script."var nme=$(this).parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewAttorney').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewAttorney').html(nme);";
        $script = $script."}";
        $script = $script."});";
        // handle selected
        $script = $script."if($('[name=rdoAttorney]:checked').length > 0){";
        $script = $script."var nme=$('[name=rdoAttorney]:checked').parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewAttorney').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewAttorney').html(nme);";
        $script = $script."}";
        $script = $script."}";

        //cpa
        $script = $script."var cpa=[];";
        $script = $script."var btnAddNewCPA=$('#btnAddNewCPA');";
        $script = $script."btnAddNewCPA.on('click', function(){";
        $script = $script."openModal('Add New CPA',buildSupporting('CPA'));";
        $script = $script."$('#altMsgSup').hide();";
        $script = $script."validateSup();";
        $script = $script."$('#btnAddNewSup').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var btntxt = btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."if(isSupValid($('#nameres_sup'),$('#titleres_sup'),$('#streetres_sup'),$('#cityres_sup'),$('#state_sup'),$('#zip_sup'))) {";
        $script = $script."cpa.push(saveSup($('#name_sup').val(),$('#title_sup').val(),$('#street_sup').val(),$('#city_sup').val(),$('#state_sup').val(),$('#zip_sup').val(),$('#phone_sup').val(),$('#email_sup').val()));";
        $script = $script."var id=(cpa.length)-1;";
        $script = $script."addSupRadio($('#viewCPA'),'cpa_',$('#selCPA'),'rdoCPA',id,$('#name_sup').val(),$('#title_sup').val(),$('#city_sup').val(),$('#state_sup').val());";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgSup').addClass('alert-danger').html('Invalid or empty fields. Please correct.').show();";
        $script = $script."}";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."});";
        // handle click change
        $script = $script."$('input[name=rdoCPA]').on('click',function(){";
        $script = $script."var nme=$(this).parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewCPA').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewCPA').html(nme);";
        $script = $script."}";
        $script = $script."});";
        // handle selected
        $script = $script."if($('[name=rdoCPA]:checked').length > 0){";
        $script = $script."var nme=$('[name=rdoCPA]:checked').parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewCPA').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewCPA').html(nme);";
        $script = $script."}";
        $script = $script."}";

        //LE Agency
        $script = $script."var leagency=[];";
        $script = $script."var btnAddLeAgency=$('#btnAddLeAgency');";
        $script = $script."btnAddLeAgency.on('click', function(){";
        $script = $script."openModal('Add New LE Agency',buildSupporting('LE Agency'));";
        $script = $script."$('#altMsgSup').hide();";
        $script = $script."validateSup();";
        $script = $script."$('#btnAddNewSup').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var btntxt = btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."if(isSupValid($('#nameres_sup'),$('#titleres_sup'),$('#streetres_sup'),$('#cityres_sup'),$('#state_sup'),$('#zip_sup'))) {";
        $script = $script."leagency.push(saveSup($('#name_sup').val(),$('#title_sup').val(),$('#street_sup').val(),$('#city_sup').val(),$('#state_sup').val(),$('#zip_sup').val(),$('#phone_sup').val(),$('#email_sup').val()));";
        $script = $script."var id=(leagency.length)-1;";
        $script = $script."addSupRadio($('#viewLeAgency'),'lea_',$('#selLeAgency'),'rdoLeAgency',id,$('#name_sup').val(),$('#title_sup').val(),$('#city_sup').val(),$('#state_sup').val());";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgSup').addClass('alert-danger').html('Invalid or empty fields. Please correct.').show();";
        $script = $script."}";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."});";
        // handle click change
        $script = $script."$('input[name=rdoLeAgency]').on('click',function(){";
        $script = $script."var nme=$(this).parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewLeAgency').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewLeAgency').html(nme);";
        $script = $script."}";
        $script = $script."});";
        // handle selected
        $script = $script."if($('[name=rdoLeAgency]:checked').length > 0){";
        $script = $script."var nme=$('[name=rdoLeAgency]:checked').parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewLeAgency').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewLeAgency').html(nme);";
        $script = $script."}";
        $script = $script."}";

        //district attorney
        $script = $script."var distatt=[];";
        $script = $script."var btnAddDistAttorney=$('#btnAddDistAttorney');";
        $script = $script."btnAddDistAttorney.on('click', function(){";
        $script = $script."openModal('Add New District Attorney',buildSupporting('District Attorney'));";
        $script = $script."$('#altMsgSup').hide();";
        $script = $script."validateSup();";
        $script = $script."$('#btnAddNewSup').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var btntxt = btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."if(isSupValid($('#nameres_sup'),$('#titleres_sup'),$('#streetres_sup'),$('#cityres_sup'),$('#state_sup'),$('#zip_sup'))) {";
        $script = $script."distatt.push(saveSup($('#name_sup').val(),$('#title_sup').val(),$('#street_sup').val(),$('#city_sup').val(),$('#state_sup').val(),$('#zip_sup').val(),$('#phone_sup').val(),$('#email_sup').val()));";
        $script = $script."var id=(distatt.length)-1;";
        $script = $script."addSupRadio($('#viewDistAttorney'),'da_',$('#selDistAttorney'),'rdoDistAttorney',id,$('#name_sup').val(),$('#title_sup').val(),$('#city_sup').val(),$('#state_sup').val());";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgSup').addClass('alert-danger').html('Invalid or empty fields. Please correct.').show();";
        $script = $script."}";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."});";
        // handle click change
        $script = $script."$('input[name=rdoDistAttorney]').on('click',function(){";
        $script = $script."var nme=$(this).parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewDistAttorney').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewDistAttorney').html(nme);";
        $script = $script."}";
        $script = $script."});";
        // handle selected
        $script = $script."if($('[name=rdoDistAttorney]:checked').length > 0){";
        $script = $script."var nme=$('[name=rdoDistAttorney]:checked').parents('label').text();";
        $script = $script."var nme_parts=nme.split(' ');";
        $script = $script."if(nme_parts.length > 0){";
        $script = $script."$('#viewDistAttorney').html(nme_parts[0]+' '+nme_parts[1]);";
        $script = $script."}else{";
        $script = $script."$('#viewDistAttorney').html(nme);";
        $script = $script."}";
        $script = $script."}";

        //lead sheet
        // view lead entry
        $script = $script."var viewLeadEntry=function(name,source,assto,dateass,notes,iscomp){";
        $script = $script."var ret_value='<div>'";
        $script = $script."+'<div><strong>Name:</strong> <span>'+name+'</span></div>'";
        $script = $script."+'<div><strong>Source:</strong> <span>'+source+'</span></div>'";
        $script = $script."+'<div><strong>Assigned To:</strong> <span>'+assto+'</span></div>'";
        $script = $script."+'<div><strong>Date Assigned:</strong> <span>'+dateass+'</span></div>'";
        $script = $script."+'<div><strong>Notes:</strong><p>'+notes+'</p></div>'";
        $script = $script."+'<div><strong>Is Complete:</strong> <span>'+iscomp+'</span></div>'";
        $script = $script."+'</div>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // create lead entry
        $script = $script."var buildLeadEntry=function(){";
        $script = $script."var ret_value='<form class=\"form-horizontal\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgLead\" role=\"alert\"></div>'";
        // name
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_le\" class=\"col-sm-3 control-label\">Name</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"name_le\" id=\"name_le\" value=\"\" placeholder=\"Lead Name\">'";
        $script = $script."+'<span id=\"nameres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // source
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"source_le\" class=\"col-sm-3 control-label\">Source</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"source_le\" id=\"source_le\" value=\"\" placeholder=\"Lead Source\">'";
        $script = $script."+'<span id=\"sourceres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // assigned_to
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"assignedto_le\" class=\"col-sm-4 control-label\">Assigned To</label>'";
        $script = $script."+'<div class=\"col-sm-8\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<select class=\"form-control\" id=\"assignedto_le\">'";
        $script = $script."+'<option value=\"\" data=\"\">Please Select</option>'";
if(isset($team) && count($team) > 0) {
    foreach ($team as $mem) {
        $script = $script."+'<option value=\"".md5($mem['id'])."\">".$mem['name']."</option>'";
    }
}
        $script = $script."+'</select>'";
        $script = $script."+'<span id=\"assignedtores_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // date_assigned
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"date_le\" class=\"col-sm-4 control-label\">Date Assigned</label>'";
        $script = $script."+'<div class=\"col-sm-8\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"date\" class=\"form-control\" name=\"dateass_le\" id=\"dateass_le\" value=\"\" max=\"".date('Y-m-d')."\" data=\"".date('m/d/Y')."\" placeholder=\"\">'";
        $script = $script."+'<span id=\"dateassres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // comments
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"notes\" class=\"col-sm-3 control-label\">Notes</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<textarea class=\"form-control\" name=\"notes_le\" id=\"notes_le\" value=\"\" placeholder=\"Notes and Comments\" rows=\"3\"></textarea>'";
        $script = $script."+'<span id=\"notesres_le\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        // is_completed
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"name_sup\" class=\"col-sm-3 control-label\">&nbsp;</label>'";
        $script = $script."+'<div class=\"col-sm-9\">'";
        $script = $script."+'<div class=\"checkbox\">'";
        $script = $script."+'<label>'";
        $script = $script."+'<input type=\"checkbox\" class=\"chkIsCompleted\" id=\"chkIsCompleted\" value=\"1\">'";
        $script = $script."+'Is Completed'";
        $script = $script."+'</label>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\" style=\"text-align:right;\">'";
        $script = $script."+'<button type=\"button\" id=\"btnAddNewLead\" class=\"btn btn-success\">Add</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // lead sheet array
        $script = $script."var leadsheet=[];";
        // lead sheet listing
        $script = $script."var loadLeadEntry = function(lsid){";
        $script = $script."if(!lsid){";
        $script = $script."return [];";
        $script = $script."}";
        $script = $script."if(typeof(Storage)!=='undefined'){";
        $script = $script."var entries=localStorage.getItem(lsid);";
        $script = $script."if (entries){";
        $script = $script."return JSON.parse(entries);";
        $script = $script."}else{";
        $script = $script."return [];";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."return leadsheet;";
        $script = $script."}";
        $script = $script."};";
        // lead sheet temp storage
        $script = $script."var storeLeadEntry = function(lsid,name,source,astoid,asto,asdt,notes,iscomp){";
        $script = $script."var entry={};";
        $script = $script."if(name && source && asto && asdt){";
        $script = $script."entry.name = name;";
        $script = $script."entry.source = source;";
        $script = $script."entry.asto = asto;";
        $script = $script."entry.astoid = astoid;";
        $script = $script."entry.asdt = asdt;";
        $script = $script."entry.notes = '';";
        $script = $script."if(notes){";
        $script = $script."entry.notes = notes;";
        $script = $script."}";
        $script = $script."if(iscomp && iscomp==true){";
        $script = $script."entry.iscomp=true";
        $script = $script."}else{";
        $script = $script."entry.iscomp=false;";
        $script = $script."}";
        $script = $script."leadsheet.push(entry);";
        // store to local storage if exists
        $script = $script."if(typeof(Storage)!=='undefined'){";
        $script = $script."if(lsid){";
        $script = $script."localStorage.setItem(lsid, JSON.stringify(leadsheet));";
        $script = $script."return true;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        // clear lead entry
        $script = $script."var clearLeadEntry = function(lsid){";
        $script = $script."if(typeof(Storage)!=='undefined'){";
        $script = $script."localStorage.removeItem(lsid);";
        $script = $script."leadsheet=[];";
        $script = $script."}else{";
        $script = $script."leadsheet=[];";
        $script = $script."}";
        $script = $script."};";
        // add to display table
        $script = $script."var addLeadRow = function(id,name,dateass){";
        $script = $script."var ret_value='<tr>'";
        $script = $script."+'<td>'+name+'</td>'";
        $script = $script."+'<td>'+dateass+'</td>'";
        $script = $script."+'<td><button type=\"button\" class=\"btn btn-default btn-xs leview\" data=\"'+id+'\" id=\"btned_'+id+'\">View</button>&nbsp;<button type=\"button\" class=\"btn btn-danger btn-xs leremove\" data=\"'+id+'\" id=\"btnrm_'+id+'\">Delete</button></td>'";
        $script = $script."+'</tr>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        //lead sheet open btnAddEntry
        $script = $script."var btnAddEntry=$('#btnAddEntry');";
        $script = $script."btnAddEntry.on('click',function(){";
        $script = $script."openModal('Add New Lead Sheet Entry',buildLeadEntry(''));";
        $script = $script."$('#altMsgLead').hide();";
        // validate name
        $script = $script."var name=$('#name_le');";
        $script = $script."var nameres=$('#nameres_le');";
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
        // validate source
        $script = $script."var source=$('#source_le');";
        $script = $script."var sourceres=$('#sourceres_le');";
        $script = $script."source.on('blur keyup focus', function() {";
        $script = $script."if(source.val().length == 0){";
        $script = $script."sourceres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if(source.val().trim().indexOf(' ') != -1){";
        $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."sourceres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // validate assigned to
        $script = $script."var assignedto=$('#assignedto_le');";
        $script = $script."var assignedtores=$('#assignedtores_le');";
        $script = $script."assignedto.on('change', function(){";
        $script = $script."if(assignedto.val().length == 0){";
        $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}else if (assignedto.val().length > 0){";
        $script = $script."assignedtores.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}";
        $script = $script."});";
        // validate date assigned
        $script = $script."var dateass=$('#dateass_le');";
        $script = $script."var dateassres=$('#dateassres_le');";
        $script = $script."if (supportsHTML5Date() == false){";
        $script = $script."if(dateass.val().length == 0){";
        $script = $script."dateass.val(dateass.attr('data'));";
        $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}";
        $script = $script."dateass.on('blur keyup focus', function() {";
        $script = $script."if(dateass.val().length == 0){";
        $script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."$('#viewCreationDate').html('');";
        $script = $script."}else if(checkdate(dateass)){";
        $script = $script."var selDate = new Date(dateass.val()+' UTC');";
        $script = $script."var curDate = Date.now();";
        $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."if (selDate.getTime() <= curDate) {";
        //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."}else{";
        $script = $script."dateassres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}";
        //handle date picker validation
        $script = $script."if (supportsHTML5Date() == true){";
        $script = $script."dateass.on('change blur keyup focus', function(){";
        $script = $script."var dt=new Date(dateass.val());";
        $script = $script."if(isNaN(dt.getTime())){";
        $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."var curDate = Date.now();";
        $script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."dateassres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        //$script = $script."if (dt.getTime() <= curDate) {";
        //$script = $script."dateassres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        //$script = $script."}else{";
        //$script = $script."dateassres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        //$script = $script."}";
        $script = $script."}";
        $script = $script."});";
        $script = $script."}";
        // validate notes
        $script = $script."var notes=$('#notes_le');";
        $script = $script."var notesres=$('#notesres_le');";
        $script = $script."notes.on('blur keyup focus', function(){";
        $script = $script."$('#altMsgLead').removeClass('alert-danger').html('').hide();";
        $script = $script."if(notes.val().length == 0){";
        $script = $script."notesres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else{";
        $script = $script."if((notes.val().trim().indexOf(' ') != -1) && (notes.val().length > 10)){";
        $script = $script."notesres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."notesres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."}";
        $script = $script."});";
        // add lead entry - save form to local storage for now
        $script = $script."$('#btnAddNewLead').on('click', function() {";
        $script = $script."var validName = nameres.hasClass('pw_strong');";
        $script = $script."var validSource = sourceres.hasClass('pw_strong');";
        $script = $script."var validAssTo = assignedtores.hasClass('pw_strong');";
        $script = $script."var validDateAss = dateassres.hasClass('pw_strong');";
        $script = $script."var validNotes = notesres.hasClass('pw_strong');";
        $script = $script."if (validName && validSource && validAssTo && validDateAss && validNotes) {";
        $script = $script."var assigned_sel=$('#assignedto_le option:selected');";
        // save to local store
        $script = $script."if (storeLeadEntry('le_".md5($user_id)."',name.val(),source.val(),assigned_sel.val(),assigned_sel.text(),dateass.val(),notes.val(),$('#chkIsCompleted:checked').val())){";
        $script = $script."var ct=leadsheet.length;";
        $script = $script."var id=(ct-1);";
        $script = $script."$('#tableLeadSheet').show();";
        $script = $script."var row=addLeadRow(id,name.val(),dateass.val());";
        $script = $script."$('#tblLeadSheet').append(row);";
        $script = $script."$('#dvLeadSheet').html(ct+' Lead Entries');";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgLead').addClass('alert-danger').html('Invalid entry. Please correct.').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgLead').addClass('alert-danger').html('Valid lead entry required.').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // load stored lead entry
        $script = $script."var leelems=loadLeadEntry('le_".md5($user_id)."');";
        $script = $script."if(leelems && leelems.length > 0){";
        $script = $script."$('#tableLeadSheet').show();";
        $script = $script."leadsheet=leelems;";
        $script = $script."var ct=leelems.length;";
        $script = $script."$('#dvLeadSheet').html(ct+' Lead Entries');";
        $script = $script."for(var i=0;i<ct;i++){";
        $script = $script."var elem=leelems[i];";
        $script = $script."var name=elem['name'];";
        $script = $script."var dateass=elem['asdt'];";
        $script = $script."var row=addLeadRow(i,name,dateass);";
        $script = $script."$('#tblLeadSheet').append(row);";
        $script = $script."}";
        $script = $script."}";
        // view lead entry
        $script = $script."$(document).on('click', '.leview', function(){";
        $script = $script."var ind=parseInt($(this).attr('data'));";
        $script = $script."if(leadsheet){";
        $script = $script."var elem=leadsheet[ind];";
        $script = $script."var name=elem['name'];";
        $script = $script."var source=elem['source'];";
        $script = $script."var assto=elem['asto'];";
        $script = $script."var dateass=elem['asdt'];";
        $script = $script."var notes=elem['notes'];";
        $script = $script."var iscomp=elem['iscomp'];";
        $script = $script."var footer='<button class=\"btn btn-default\" id=\"btnCloseWin\">Close</button>';";
        $script = $script."openModal('View Entry',viewLeadEntry(name,source,assto,dateass,notes,iscomp),footer);";
        $script = $script."}";
        $script = $script."});";
        // delete lead entry
        $script = $script."$(document).on('click', '.leremove', function(){";
        $script = $script."var ind=parseInt($(this).attr('data'));";
        $script = $script."openAlertPopUp('Delete Lead Entry','<span id=\"alert_text\">This cannot be undone. Are you sure?</span>');";
        $script = $script."var okbtn = $('#btnAltOk');";
        $script = $script."okbtn.on('click', function(){";
        $script = $script."if(leadsheet){";
        // due to slice issue with removal of one we must do a manual removal
        $script = $script."var newlead=[];";
        $script = $script."for(var i=0;i<leadsheet.length;i++){";
        $script = $script."if (i !== ind){";
        $script = $script."newlead.push(leadsheet[i]);";
        $script = $script."}";
        $script = $script."}";
        $script = $script."leadsheet = newlead;";
        // save back to local storage
        $script = $script."if(typeof(Storage)!=='undefined'){";
        $script = $script."localStorage.setItem('le_".md5($user_id)."', JSON.stringify(leadsheet));";
        $script = $script."}";
        // update display
        $script = $script."var ct=leadsheet.length;";
        $script = $script."$('#tblLeadSheet').html('');";
        $script = $script."$('#dvLeadSheet').html(ct+' Lead Entries');";
        $script = $script."for(var i=0;i<ct;i++){";
        $script = $script."var elem=leadsheet[i];";
        $script = $script."var name=elem['name'];";
        $script = $script."var dateass=elem['asdt'];";
        $script = $script."var row=addLeadRow(i,name,dateass);";
        $script = $script."$('#tblLeadSheet').append(row);";
        $script = $script."}";
        $script = $script."if(ct==0){ $('#tableLeadSheet').hide(); }";
        // close modal
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";

        // import file
        $script = $script."var curFileObj={};";
        $script = $script."var impFileList=[];";
        $script = $script."var buildImportFile = function() {";
        $script = $script."var ret_value=''";
        $script = $script."+'<form id=\"frmImpFile\" class=\"form-horizontal\" method=\"post\" enctype=\"multipart/form-data\">'";
        $script = $script."+'<div class=\"alert\" id=\"altMsgImpFile\" role=\"alert\"></div>'";
        $script = $script."+'<p style=\"margin-top:5px;\">Select a file (Word, Excel, PowerPoint, PDF, Image, or Video) and press Add to Case.</p>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<input type=\"file\" name=\"fleImpFile\" id=\"fleImpFile\" required>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div id=\"fleDragDrop\">'";
        $script = $script."+'<span>Or Drag File Here</span>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"progress\" id=\"prgHolder\">'";
        $script = $script."+'<div class=\"progress-bar progress-bar-success\" id=\"prgBar\" role=\"progressbar\" style=\"width:2em;\">'";
        $script = $script."+'&nbsp;0%'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"title_fle\" class=\"col-sm-2 control-label\">Title</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"title_fle\" id=\"title_fle\" value=\"\" placeholder=\"Document Title\">'";
        $script = $script."+'<span id=\"titleres_fle\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<label for=\"tags_fle\" class=\"col-sm-2 control-label\">Tags</label>'";
        $script = $script."+'<div class=\"col-sm-10\">'";
        $script = $script."+'<div class=\"input-group\">'";
        $script = $script."+'<input type=\"text\" class=\"form-control\" name=\"tags_fle\" id=\"tags_fle\" value=\"\" placeholder=\"Comma Seperated Tags\">'";
        $script = $script."+'<span id=\"tagsres_fle\" class=\"input-group-addon\"></span>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'<hr>'";
        $script = $script."+'<div class=\"form-group\">'";
        $script = $script."+'<div class=\"col-sm-12\">'";
        $script = $script."+'<button type=\"button\" id=\"btnSaveImpFile\" class=\"btn btn-success\">Add to Case</button>'";
        $script = $script."+' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Cancel</button>'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>'";
        $script = $script."+'</form>';";
        $script = $script."return ret_value;";
        $script = $script."};";
        // file present handler
        $script = $script."var readURLForm = function(input,notes,tags,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (input.files && input.files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=input.files[0].type;";
        $script = $script."retvalue.name=input.files[0].name;";
        $script = $script."reader.readAsDataURL(input.files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        $script = $script."var readURLDrop = function(files,callback) {";
        $script = $script."var retvalue = {};";
        $script = $script."if (files && files[0]) {";
        $script = $script."var reader = new FileReader();";
        $script = $script."reader.onload = function(e){";
        $script = $script."retvalue.data=e.target.result;";
        $script = $script."retvalue.size=e.total;";
        $script = $script."return callback(retvalue);";
        $script = $script."};";
        $script = $script."retvalue.type=files[0].type;";
        $script = $script."retvalue.name=files[0].name;";
        $script = $script."reader.readAsDataURL(files[0]);";
        $script = $script."}else{";
        $script = $script."return callback(retvalue);";
        $script = $script."}";
        $script = $script."};";
        // build file presentation icon
        $script = $script."var presentFile = function(name) {";
        $script = $script."if(name){";
        $script = $script."var ft=name.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."var img='';";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'png':";
        $script = $script."case 'jpg':";
        $script = $script."case 'jpeg': img='".base_url("img/icons/image.png")."'; break;";
        $script = $script."case 'txt': img='".base_url("img/icons/text.png")."'; break;";
        $script = $script."case 'xlsx': img='".base_url("img/icons/excel.png")."'; break;";
        $script = $script."case 'docx': img='".base_url("img/icons/word.png")."'; break;";
        $script = $script."case 'pptx': img='".base_url("img/icons/powerpoint.png")."'; break;";
        $script = $script."case 'pdf': img='".base_url("img/icons/pdf.png")."'; break;";
        $script = $script."case 'mp4':";
        $script = $script."case 'webm':";
        $script = $script."case 'ogv':";
        $script = $script."case 'mp3': img='".base_url("img/icons/video.png")."'; break;";
        $script = $script."}";
        $script = $script."if(img !== ''){";
        $script = $script."var pres='<img src=\"'+img+'\" class=\"flimg\"><span class=\"flname\">'+name+'</span>';";
        $script = $script."return pres;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        // add files
        $script = $script."$('#btnImportFile').on('click', function() {";
        $script = $script."openModal('Import A File',buildImportFile());";
        $script = $script."$('#prgHolder').hide();";
        // handle tags and title validation
        $script = $script."$('#title_fle').on('blur keyup focus', function(){";
        $script = $script."var title=$('#title_fle');";
        $script = $script."var titleres=$('#titleres_fle');";
        $script = $script."if(title.val().length == 0){";
        $script = $script."titleres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(title.val().length >= 3){";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."titleres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#tags_fle').on('blur keyup focus', function(){";
        $script = $script."var tags=$('#tags_fle');";
        $script = $script."var tagsres=$('#tagsres_fle');";
        $script = $script."if(tags.val().length == 0){";
        $script = $script."tagsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
        $script = $script."}else if(tags.val().trim().indexOf(',') != -1){";
        $script = $script."tagsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
        $script = $script."}else{";
        $script = $script."tagsres.html('comma required').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
        $script = $script."}";
        $script = $script."});";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        // handle file control add
        $script = $script."$('#fleImpFile').on('change', function(){";
        $script = $script."readURLForm(this,$('#notes_fle').val(),$('#tags_fle').val(),function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        // store file object to global
        //$script = $script."impFileList.push(ret);";
        $script = $script."curFileObj=ret;";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        $script = $script."});";
        // handle drop file
        $script = $script."$(document).on('dragover', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('drop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."});";
        $script = $script."$(document).on('dragover', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('dragenter', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '2px solid #777');";
        $script = $script."});";
        $script = $script."$(document).on('drop', '#fleDragDrop', function(e){";
        $script = $script."e.preventDefault();";
        $script = $script."e.stopPropagation();";
        $script = $script."$(this).css('border', '1px solid #f9f9f9');";
        $script = $script."var files = e.originalEvent.dataTransfer.files;";
        $script = $script."readURLDrop(files,function(ret){";
        $script = $script."if(ret['data']){";
        $script = $script."var elem = presentFile(ret['name']);";
        // store file object to global
        $script = $script."curFileObj=ret;";
        $script = $script."if(elem){";
        $script = $script."$('#fleDragDrop').html(elem);";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('Invalid file type. Limited to: .png, .jpg, .jpeg, .xlsx, .docx, .pptx, .pdf, .mp4, .webm, .ogv, .mp3').show();";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('file appears to be corrupted').show();";
        $script = $script."}";
        $script = $script."});";
        $script = $script."});";
        // build row for display
        $script = $script."var addFileUploadRow = function(id,name){";
        $script = $script."if(!id){";
        $script = $script."id=0;";
        $script = $script."}";
        $script = $script."if(name){";
        $script = $script."var ft=name.split('.');";
        $script = $script."if (ft.length > 0){";
        $script = $script."var img='';";
        $script = $script."switch(ft[1]){";
        $script = $script."case 'png':";
        $script = $script."case 'jpg':";
        $script = $script."case 'jpeg': img='".base_url("img/icons/image.png")."'; break;";
        $script = $script."case 'txt': img='".base_url("img/icons/text.png")."'; break;";
        $script = $script."case 'xlsx': img='".base_url("img/icons/excel.png")."'; break;";
        $script = $script."case 'docx': img='".base_url("img/icons/word.png")."'; break;";
        $script = $script."case 'pptx': img='".base_url("img/icons/powerpoint.png")."'; break;";
        $script = $script."case 'pdf': img='".base_url("img/icons/pdf.png")."'; break;";
        $script = $script."case 'mp4':";
        $script = $script."case 'webm':";
        $script = $script."case 'ogv':";
        $script = $script."case 'mp3': img='".base_url("img/icons/video.png")."'; break;";
        $script = $script."}";
        $script = $script."if(img !== ''){";
        $script = $script."var img='<img src=\"'+img+'\" class=\"flimg\">';";
        $script = $script."var ret_value='<tr>'";
        $script = $script."+'<td>'+img+'</td>'";
        $script = $script."+'<td>'+name+'</td>'";
        $script = $script."+'<td><button type=\"button\" class=\"btn btn-danger btn-xs flremove\" data=\"'+id+'\" id=\"btfl_'+id+'\">Delete</button></td>'";
        $script = $script."+'</tr>';";
        $script = $script."return ret_value;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";
        $script = $script."return false;";
        $script = $script."}else{";
        $script = $script."return false;";
        $script = $script."}";
        $script = $script."};";
        
        // handle store to local variable
        $script = $script."$(document).on('click', '#btnSaveImpFile', function(){";
        $script = $script."$(this).attr('disabled', 'disabled').html('Please Wait...');";
        $script = $script."$('#altMsgImpFile').removeClass('alert-danger').html('').hide();";
        $script = $script."if(curFileObj){";
        // add in description and tags
        $script = $script."curFileObj['title'] = $('#title_fle').val();";
        $script = $script."curFileObj['tags'] = $('#tags_fle').val();";
        $script = $script."impFileList.push(curFileObj);";
        $script = $script."curFileObj={};";
        $script = $script."}";
        $script = $script."if(($('#fleDragDrop').html() != '<span>Or Drag File Here</span>') && (impFileList && impFileList.length > 0)) {";
        $script = $script."$('#tableSupDocs').hide();";
        $script = $script."$('#tbodySupDocs').html('');";
        $script = $script."$('#prgHolder').show();";
        $script = $script."var inc=100/impFileList.length;";
        $script = $script."for(var i=0;i<impFileList.length;i++){";
        $script = $script."var pstep=inc+i;";
        $script = $script."$('#prgBar').attr('style','width:'+pstep+'%');";
        $script = $script."$('#prgBar').html('&nbsp;'+pstep+'%');";
        // create display table row
        $script = $script."var row=addFileUploadRow(i,impFileList[i]['name']);";
        $script = $script."$('#tbodySupDocs').append(row);";
        $script = $script."}";
        $script = $script."$('#prgHolder').hide();";
        $script = $script."var docText=impFileList.length+' Document(s) ';";
        $script = $script."$('#dvSupDocs').html(docText);";
        $script = $script."$('#tableSupDocs').show();";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."}else{";
        $script = $script."$('#altMsgImpFile').addClass('alert-danger').html('No file added. Please select or drop a file to Add to Case.').show();";
        $script = $script."}";
        $script = $script."$(this).removeAttr('disabled').html('Add to Case');";
        $script = $script."});";
        // handle file remove
        $script = $script."$(document).on('click', '.flremove', function(){";
        $script = $script."var id=$(this).attr('data');";
        $script = $script."openAlertPopUp('Delete Document','<span id=\"alert_text\">This cannot be undone. Are you sure?</span>');";
        $script = $script."var okbtn = $('#btnAltOk');";
        $script = $script."okbtn.on('click', function(){";
        $script = $script."if(impFileList.length > 0){";
        $script = $script."$('#tableSupDocs').hide();";
        $script = $script."$('#tbodySupDocs').html('');";
        $script = $script."var tempFileList={};";
        $script = $script."tempFileList=impFileList;";
        $script = $script."impFileList=[];";
        $script = $script."for(var i=0;i<tempFileList.length;i++){";
        $script = $script."if(i != id){";
        $script = $script."impFileList.push(tempFileList[i]);";
        $script = $script."var row=addFileUploadRow(i,tempFileList[i]['name']);";
        $script = $script."$('#tbodySupDocs').append(row);";
        $script = $script."}";
        $script = $script."}";
        $script = $script."var ct=0;";
        $script = $script."if(impFileList.length && impFileList.length > 0){";
        $script = $script."ct=impFileList.length;";
        $script = $script."var docText=ct+' Document(s)';";
        $script = $script."$('#dvSupDocs').html(docText);";
        $script = $script."$('#tableSupDocs').show();";
        $script = $script."}else{";
        $script = $script."var docText='0 Document(s)';";
        $script = $script."$('#dvSupDocs').html(docText);";
        $script = $script."$('#tableSupDocs').hide();";
        $script = $script."$('#tbodySupDocs').html('');";
        $script = $script."}";
        $script = $script."}else{";
        $script = $script."$('#tableSupDocs').hide();";
        $script = $script."$('#tbodySupDocs').html('');";
        $script = $script."}";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."});";
        $script = $script."});";

        // save case
        $script = $script."$('#btnSaveCase').on('click', function(){";
        $script = $script."var btn=$(this);";
        $script = $script."var btntxt=btn.html();";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."$('#altMsgMain').removeClass('alert-danger').html('').hide();";
        // ensure case name
        $script = $script."if(!cnameres.hasClass('pw_strong')){";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html('Valid Case Name Required').show();";
        $script = $script."cname.focus();";
        $script = $script."window.scrollTo(0, 50);";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."return false;";
        $script = $script."}";
        // ensure client info viewClientInfo
        $script = $script."if($('#viewClientInfo').html() == ''){";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html('Valid Client Information Required').show();";
        $script = $script."btnClientInfo.click();";
        $script = $script."company.focus();";
        $script = $script."window.scrollTo(0, 110);";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."return false;";
        $script = $script."}else if((!cmpres.hasClass('pw_strong')) || (!streetres.hasClass('pw_strong')) || (!cityres.hasClass('pw_strong')) || (!state.hasClass('br_strong')) || (!zip.hasClass('br_strong'))){";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html('All Client Information Fields Required').show();";
        $script = $script."btnClientInfo.click();";
        $script = $script."company.focus();";
        $script = $script."window.scrollTo(0, 110);";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."return false;";
        $script = $script."}";
        // ensure creation date
        $script = $script."if(!crdateres.hasClass('pw_strong')){";
        // determine if html5 date
        $script = $script."if (supportsHTML5Date() == true){";
            $script = $script."var cdt=$('#viewCreationDate').html();";
            $script = $script."var validformat=/^\d{2}\/\d{2}\/\d{4}$/;";
            $script = $script."if (!validformat.test(cdt)) {";
                $script = $script."btnCreation.click();";
                $script = $script."crdate.focus();";
                $script = $script."window.scrollTo(0, 170);";
                $script = $script."btn.removeAttr('disabled').html(btntxt);";
                $script = $script."return false;";
            $script = $script."}";
        $script = $script."}else{";
            $script = $script."$('#altMsgMain').addClass('alert-danger').html('Valid Creation Date Required').show();";
            $script = $script."btnCreation.click();";
            $script = $script."crdate.focus();";
            $script = $script."window.scrollTo(0, 170);";
            $script = $script."btn.removeAttr('disabled').html(btntxt);";
            $script = $script."return false;";
        $script = $script."}";
        $script = $script."}";

        // ensure predication
        /*
        $script = $script."if($('#viewPredication').html() == ''){";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html('A Valid Predication is Required').show();";
        $script = $script."btnPredication.click();";
        $script = $script."pred.focus();";
        $script = $script."window.scrollTo(0, 200);";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."return false;";
        $script = $script."}";
        */

        // test for attorney, cpa, leagency, da
        $script = $script."var hasAttorney=true;";
        $script = $script."var hasCPA=true;";
        $script = $script."var hasLeAgency=true;";
        $script = $script."var hasDistAttorney=true;";
        $script = $script."var alertText='';";
        // test for attorney
        $script = $script."if($('#viewAttorney').html() == ''){";
        $script = $script."hasAttorney=false;";
        $script = $script."alertText='Attorney,';";
        $script = $script."}";
        // test for cpa
        $script = $script."if($('#viewCPA').html() == ''){";
        $script = $script."hasCPA=false;";
        $script = $script."alertText=alertText+'CPA,';";
        $script = $script."}";
        // test for leagency
        $script = $script."if($('#viewLeAgency').html() == ''){";
        $script = $script."hasLeAgency=false;";
        $script = $script."alertText=alertText+'LE Agency,';";
        $script = $script."}";
        // test for district attorney
        $script = $script."if($('#viewDistAttorney').html() == ''){";
        $script = $script."hasDistAttorney=false;";
        $script = $script."alertText=alertText+'District Attorney,';";
        $script = $script."}";
        // close all panels
        $script = $script."hideAllPanels();";

        // save function
        $script = $script."var saveCase = function(callback){";
        $script = $script."function buildSaveDialog(){";
        $script = $script."var retvalue='<p id=\"pSaveDetails\">Saving New Case...</p>'";
        $script = $script."+'<div class=\"progress\">'";
        $script = $script."+'<div class=\"progress-bar progress-bar-success\" id=\"prgSaveBar\" role=\"progressbar\" style=\"width:2em;\">'";
        $script = $script."+'&nbsp;0%'";
        $script = $script."+'</div>'";
        $script = $script."+'</div>';";
        $script = $script."return retvalue;";
        $script = $script."}";
        $script = $script."openModal('Save Case',buildSaveDialog());";
        $script = $script."$('.close').hide();";
        // handle progress
        $script = $script."function updateProgress(val,msg){";
        $script = $script."if(msg && val){";
        $script = $script."$('#pSaveDetails').html(msg);";
        $script = $script."$('#prgSaveBar').attr('style','width:'+val+'%;');";
        $script = $script."$('#prgSaveBar').html('&nbsp;'+val+'%');";
        $script = $script."}";
        $script = $script."}";
        // is numeric
        $script = $script."function isNumeric(n){";
        $script = $script."return !isNaN(parseFloat(n)) && isFinite(n);";
        $script = $script."}";
        // save code here
        $script = $script."var cid = '".md5($company_id)."';";
        $script = $script."var case_id=0;";
        $script = $script."var client_id=0;";
        $script = $script."var attorney_id=0;";
        $script = $script."var cpa_id=0;";
        $script = $script."var leagency_id=0;";
        $script = $script."var distatt_id=0;";
        // client
        $script = $script."updateProgress(20,'Saving client data...');";
        $script = $script."var clname=$('#company').val();";
        $script = $script."var clstreet=$('#street').val();";
        $script = $script."var clcity=$('#city').val();";
        $script = $script."var clstate=$('#state').val();";
        $script = $script."var clzip=$('#zip').val();";
        $script = $script."var data = {comp_id:cid,image:'',name:clname,street:clstreet,city:clcity,state:clstate,zip:clzip};";
        $script = $script."postData('".site_url("add/add_client")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        // set id for client
        $script = $script."client_id=res.id;";
        // supporting roles
        $script = $script."updateProgress(40,'Updating support roles...');";
        $script = $script."var att_selected=$('[name=rdoAttorney]:checked').val();"; //input:checked
        $script = $script."var cpa_selected=$('[name=rdoCPA]:checked').val();";
        $script = $script."var lea_selected=$('[name=rdoLeAgency]:checked').val();";
        $script = $script."var da_selected=$('[name=rdoDistAttorney]:checked').val();";
        $script = $script."var att_json = false;";
        $script = $script."var cpa_json = false;";
        $script = $script."var lea_json = false;";
        $script = $script."var da_json = false;";
        $script = $script."if(isNumeric(att_selected)){";
        $script = $script."att_json = JSON.stringify(attorney[att_selected]);";
        $script = $script."}else{";
        $script = $script."attorney_id=att_selected;";
        $script = $script."}";
        $script = $script."if(isNumeric(cpa_selected)){";
        $script = $script."cpa_json = JSON.stringify(cpa[cpa_selected]);";
        $script = $script."}else{";
        $script = $script."cpa_id=cpa_selected;";
        $script = $script."}";
        $script = $script."if(isNumeric(lea_selected)){";
        $script = $script."lea_json = JSON.stringify(leagency[lea_selected]);";
        $script = $script."}else{";
        $script = $script."leagency_id=lea_selected;";
        $script = $script."}";
        $script = $script."if(isNumeric(da_selected)){";
        $script = $script."da_json = JSON.stringify(distatt[da_selected]);";
        $script = $script."}else{";
        $script = $script."distatt_id=da_selected;";
        $script = $script."}";
        $script = $script."data={comp_id:cid,att:att_json,cpa:cpa_json,leag:lea_json,datt:da_json};";
        $script = $script."postData('".site_url("add/add_multiple_supporting_roles")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."btn.attr('disabled','disabled').html('Please wait...');";
        $script = $script."if(res.attorney_id > 0){";
        $script = $script."attorney_id=res.attorney_id;";
        $script = $script."}";
        $script = $script."if(res.cpa_id > 0){";
        $script = $script."cpa_id=res.cpa_id;";
        $script = $script."}";
        $script = $script."if(res.leagency_id > 0){";
        $script = $script."leagency_id=res.leagency_id;";
        $script = $script."}";
        $script = $script."if(res.distatt_id > 0){";
        $script = $script."distatt_id=res.distatt_id;";
        $script = $script."}";
        // case
        $script = $script."updateProgress(60,'Saving case information...');";
        $script = $script."var case_name=$('#cname').val();";
        $script = $script."var case_pred=$('#predication').val();";
        $script = $script."var data = {comp_id:cid,name:case_name,pred:case_pred,cl_id:client_id,att_id:attorney_id,cpa_id:cpa_id,lea_id:leagency_id,da_id:distatt_id};";
        $script = $script."postData('".site_url("add/add_case")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."case_id=res.id;";
        // team members and case admin
        $script = $script."var case_admin_id=$('.rdoAdmin:checked').val();";
        $script = $script."var team = (function() {";
        $script = $script."var a = [];";
        $script = $script."$('.chkteam:checked').each(function() {";
        $script = $script."a.push(this.value);";
        $script = $script."});";
        $script = $script."return a;";
        $script = $script."})();";
        $script = $script."updateProgress(70,'Adding case team members...');";
        $script = $script."var data = {caseid:case_id,adminid:case_admin_id,team_mems:team};";
        $script = $script."postData('".site_url("add/add_case_team")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        $script = $script."";
        // lead entry
        $script = $script."updateProgress(80,'Checking lead sheet...');";
        $script = $script."var data = {caseid:case_id,leads:leadsheet};";
        $script = $script."postData('".site_url("add/add_lead_entries")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // attachments
        $script = $script."updateProgress(90,'Checking documents...');";
        $script = $script."var userid='".md5($user_id)."';";
        $script = $script."var data = {caseid:case_id,uid:userid,fls:impFileList};";
        $script = $script."postData('".site_url("add/add_supporting_docs")."',data,btn,function(res){";
        $script = $script."if(res.result){";
        // finished
        $script = $script."updateProgress(100,'Finished');";
        // close and return
        $script = $script."isFormSaved = true;";
        $script = $script."window.location = '".site_url("mycases")."'";

        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";

        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";

        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";

        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";
        
        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";

        $script = $script."}else{";
        $script = $script."$('#altMsgMain').addClass('alert-danger').html(res.msg).show();";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."$('.close').click();";
        $script = $script."}";
        $script = $script."});";
        // end save case
        $script = $script."};";

        // handle missing unrequired with alert
        $script = $script."if(alertText !== ''){";
        $script = $script."var cleanAlertText = alertText.replace(/(^,)|(,$)/g, '');";
        $script = $script."openAlertPopUp('Missing Information','<span id=\"alert_text\">Missing:['+cleanAlertText+']. Save Anyway?</span>');";
        $script = $script."var okbtn = $('#btnAltOk');";
        $script = $script."okbtn.on('click', function(){";
        $script = $script."$('#btnCloseWin').click();";
        $script = $script."saveCase(function(res){";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."});";
        $script = $script."}else{";
        $script = $script."saveCase(function(res){";
        $script = $script."btn.removeAttr('disabled').html(btntxt);";
        $script = $script."});";
        $script = $script."}";

        // end save
        $script = $script."});";

        // end of script
        $script = $script."});";

        return $script;
    }
}
