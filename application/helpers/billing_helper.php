<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// javascript for billing
if (!function_exists('billing_script'))
{
    function billing_script($case_id,$user_id,$company_id,$is_admin)
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

            // load rows of data
            $script = $script."var loadRows = function(uid,cid,m,y){";
                $script = $script."var data = {userid:uid,caseid:cid,month:m,year:y};";
                $script = $script."postData('".site_url("data/expenses")."',data,null,function(res){";
                $script = $script."if(res.result){";
                    $script = $script."$('#tblBilling').html('');";
                    // reload data
                    $script = $script."var rows='';";
                    $script = $script."var exps=res.expenses;";
                    $script = $script."var ct=exps.length;";
                    $script = $script."for(var i=0;i<ct;i++){";
                        $script = $script."var exp=exps[i];";
                        $script = $script."rows+='<tr>';";
                        $script = $script."var dt=new Date(exp.date_occured);";
                        $script = $script."var options={timeZone:'UTC'};";

                        $script = $script."rows+='<td>'+dt.toLocaleDateString('en-US',options)+'</td>';";
                        $script = $script."rows+='<td>'+exp.case_name+'</td>';";

                        $script = $script."if(exp.interview_name){";
                            $script = $script."rows+='<td>'+exp.interview_name+'</td>';";
                        $script = $script."}else if (exp.attachment_name){";
                            $script = $script."rows+='<td>'+exp.attachment_name+'</td>';";
                        $script = $script."}else{";
                            $script = $script."rows+='<td>Invalid: Not Part of Case</td>';";
                        $script = $script."}";

                        $script = $script."rows+='<td>'+exp.item_name+'</td>';";
                        $script = $script."rows+='<td>'+exp.desc+'</td>';";
                        $script = $script."rows+='<td style=\"text-align:right;\">'+exp.amount+'</td>';";

                        $script = $script."if(exp.need_calc == 'true'){";
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)) {
                            $script = $script."rows+='<td style=\"text-align:right;\"><input type=\"text\" value=\"'+exp.rate+'\" style=\"width:60px;\" class=\"ratebox\" id=\"txt_rate_'+exp.id+'\" expid=\"'+exp.id+'\" item=\"'+exp.item+'\" data=\"'+exp.amount+'\" uid=\"'+exp.user_id+'\"><span class=\"btnholder\"></span></td>';";
}else{
                            $script = $script."rows+='<td style=\"text-align:right;\">'+exp.rate+'</td>';";
}
                        $script = $script."}else{";
                            $script = $script."rows+='<td style=\"text-align:right;\">0</td>';";
                        $script = $script."}";

                        $script = $script."if(exp.need_calc == 'true'){";
                            $script = $script."var tot=parseInt(exp.amount)*parseInt(exp.rate);";
                        $script = $script."}else{";
                            $script = $script."var tot=exp.amount;";
                        $script = $script."}";

                        $script = $script."rows+='<td style=\"text-align:right;\" id=\"tot_'+exp.id+'\">'+tot+'</td>';";
                        $script = $script."rows+='<td><button class=\"btn btn-sm btn-danger delbtn\" id=\"delbtn_'+exp.id+'\" uid=\"'+exp.user_id+'\" data=\"'+exp.id+'\" title=\"Delete Expense\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button> <button class=\"btn btn-sm btn-info edtbtn\" id=\"edtbtn_'+exp.id+'\" uid=\"'+exp.user_id+'\" data=\"'+exp.id+'\" title=\"Edit Expense\"><span class=\"glyphicon glyphicon-pencil\" aria-hidden=\"true\"></span></button></td>';";
                        $script = $script."rows+='</tr>';";
                    $script = $script."}";
                    $script = $script."$('#tblBilling').html(rows);";
                $script = $script."}else{";
                    $script = $script."alert('No expenses for given options');";
                $script = $script."}";
                $script = $script."});";
            $script = $script."};";
            // initial load
            $script = $script."var mnt=$('#selMonth :selected').attr('value');";
            $script = $script."var yer=$('#selYear :selected').attr('value');";
            $script = $script."var selid=$('#selAgent :selected').attr('value');";
            $script = $script."var cid='".$case_id."';";
            $script = $script."if(!selid){";
                $script = $script."selid='".$user_id."';";
            $script = $script."}";
            $script = $script."loadRows(selid,cid,mnt,yer);";

            // expense html
            $script = $script."var buildNewExpForm = function(cats,atts,ints){";
                $script = $script."var retValue='';";
                $script = $script."retValue='<form id=\"frmNewExp\" class=\"form-horizontal\" method=\"post\">';";
                $script = $script."retValue+='<p>Enter expense details and press Add Expense.</p>';";
                $script = $script."retValue+='<div class=\"alert\" id=\"altMsgNewExp\" role=\"alert\"></div>';";
                
                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"edate\" class=\"col-sm-3 control-label\">Date</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";
                $script = $script."retValue+='<input type=\"date\" class=\"form-control\" name=\"edate\" id=\"edate\" value=\"\" max=\"".date('Y-m-d')."\" data=\"".date('m/d/Y')."\" placeholder=\"\">';";
                $script = $script."retValue+='<span id=\"edateres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"cname\" class=\"col-sm-3 control-label\">Engagment</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";

                    $script = $script."retValue+='<select class=\"form-control\" name=\"eng\" id=\"eng\">';";
                    $script = $script."retValue+='<option value=\"0\">Please Select</option>';";
                    $script = $script."var ct=atts.length;";
                    $script = $script."for(var i=0;i<ct;i++){";
                        $script = $script."var att=atts[i];";
                        $script = $script."retValue+='<option value=\"'+att.id+'\" type=\"att\">'+att.name+' ['+att.number+']</option>';";
                    $script = $script."}";
                    $script = $script."var ct=ints.length;";
                    $script = $script."for(var i=0;i<ct;i++){";
                        $script = $script."var int=ints[i];";
                        $script = $script."retValue+='<option value=\"'+int.id+'\" type=\"int\">'+int.name+' [interview]</option>';";
                    $script = $script."}";
                    $script = $script."retValue+='</select>';";

                $script = $script."retValue+='<span id=\"engres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<hr>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"act\" class=\"col-sm-3 control-label\">Act/Expense</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";

                $script = $script."if(!cats){";
                    
                    $script = $script."retValue+='<input type=\"text\" class=\"form-control\" name=\"act\" id=\"act\" value=\"\" placeholder=\"Activity / Expense\">';";
                    
                $script = $script."}else{";

                    $script = $script."retValue+='<select class=\"form-control\" name=\"act\" id=\"act\">';";
                    $script = $script."retValue+='<option value=\"0\">Please Select</option>';";
                    $script = $script."var ct=cats.length;";
                    $script = $script."for(var i=0;i<ct;i++){";
                        $script = $script."var cat=cats[i];";
                        $script = $script."retValue+='<option value=\"'+cat.id+'\">'+cat.name+'</option>';";
                    $script = $script."}";
                    $script = $script."retValue+='</select>';";

                $script = $script."}";
                $script = $script."retValue+='<span id=\"actres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"tamt\" class=\"col-sm-3 control-label\">Time/Amount</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";
                $script = $script."retValue+='<input type=\"text\" class=\"form-control\" name=\"tamt\" id=\"tamt\" value=\"\" placeholder=\"Time / Amount\">';";
                $script = $script."retValue+='<span id=\"tamtres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<label for=\"comments\" class=\"col-sm-3 control-label\">Comments</label>';";
                $script = $script."retValue+='<div class=\"col-sm-9\">';";
                $script = $script."retValue+='<div class=\"input-group\">';";
                $script = $script."retValue+='<input type=\"text\" class=\"form-control\" name=\"comments\" id=\"comments\" value=\"\" placeholder=\"Comment Line\">';";
                $script = $script."retValue+='<span id=\"commentsres\" class=\"input-group-addon\"></span>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='<hr>';";

                $script = $script."retValue+='<div class=\"form-group\">';";
                $script = $script."retValue+='<div class=\"col-sm-12\" style=\"text-align:right;\">';";
                $script = $script."retValue+='<button type=\"button\" id=\"btnSaveNewExp\" class=\"btn btn-success\">Add Expense</button>';";
                $script = $script."retValue+=' <button type=\"button\" id=\"btnCloseWin\" class=\"btn btn-default\">Close</button>';";
                $script = $script."retValue+='</div>';";
                $script = $script."retValue+='</div>';";

                $script = $script."retValue+='</form>';";
                $script = $script."return retValue;";
            $script = $script."};";

            // ratebox
            $script = $script."$(document).on('blur keyup', '.ratebox', function(){";
                $script = $script."var uid=$(this).attr('uid');";
                $script = $script."var itm=$(this).attr('item');";
                $script = $script."var amt=$(this).val();";
                $script = $script."var dat=$(this).attr('data');";
                $script = $script."var expid=$(this).attr('expid');";
                // ensure numeric
                $script = $script."if(isNumeric(amt)){";
                $script = $script."var btn='<button class=\"btnsave btn btn-small btn-success\" style=\"margin-top:-5px;\" expid=\"'+expid+'\" amt=\"'+amt+'\" data=\"'+dat+'\" uid=\"'+uid+'\" item=\"'+itm+'\" title=\"Save\"><span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span></button>';";
                $script = $script."}else{";
                $script = $script."var btn='<span class=\"pw_weak\" style=\"padding:5px;\">Invalid</span>';";
                $script = $script."}";
                $script = $script."$(this).parent('td').children('.btnholder').html(btn);";
            $script = $script."});";

            // edit button
            $script = $script."$(document).on('click', '.edtbtn', function(){";
                $script = $script."var eid=$(this).attr('data');"; //expense id
                $script = $script."var uid=$(this).attr('uid');";
                $script = $script."var btn=$(this);";

                // build edit modal
                $script = $script."var cmid='".$company_id."';";
                $script = $script."var data = {userid:uid,compid:cmid};";
                $script = $script."postData('".site_url("data/cases_cats_atts_ints")."',data,null,function(res){";
                    $script = $script."if(res.result){";

                        $script = $script."var cases=res.cases;";
                            $script = $script."var cats=res.cats;";
                            $script = $script."var atts=res.atts;";
                            $script = $script."var ints=res.interviews;";

                        // load expense to show data
                        // expense_single
                        $script = $script."var data = {expid:eid};";
                        $script = $script."postData('".site_url("data/expense_single")."',data,null,function(res){";
                        $script = $script."if(res.result){";

                            // open modal
                            $script = $script."openModal('Add New Expense',buildNewExpForm(cats,atts,ints));";
                            $script = $script."$('#altMsgNewExp').hide();";

                            // set current values
                            $script = $script."var dt=new Date(res.expenses.date_occured);";
                            $script = $script."var options={timeZone:\"UTC\", year: \"numeric\", month: \"2-digit\", day: \"2-digit\"};";
                            $script = $script."";
                            $script = $script."$('#btnSaveNewExp').html('Edit Expense').attr('id','btnEditExp').attr('eid',eid);";
                            $script = $script."$('#edate').val(dt.toLocaleDateString('en-US',options));";
                            $script = $script."$('#tamt').val(res.expenses.amount);";
                            $script = $script."$('#comments').val(res.expenses.desc);";
                            $script = $script."$(\"#eng option[value='\"+res.expenses.attachment_id+\"']\").attr('selected','selected');";
                            $script = $script."$(\"#act option[value='\"+res.expenses.item+\"']\").attr('selected','selected');";
                            
                            // validate
                            // validate engagment
                            $script = $script."var eng=$('#eng');";
                            $script = $script."var engres=$('#engres');";
                            $script = $script."if(eng.val() == 0){";
                            $script = $script."engres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."engres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}";
                            $script = $script."eng.on('blur keyup focus change', function() {";
                            $script = $script."if(eng.val() == 0){";
                            $script = $script."engres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."engres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}";
                            $script = $script."});";

                            // validate activity act
                            $script = $script."var act=$('#act');";
                            $script = $script."var actres=$('#actres');";
                            $script = $script."if(act.val() == 0){";
                            $script = $script."actres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."actres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}";
                            $script = $script."act.on('blur keyup focus change', function() {";
                            $script = $script."if(act.val() == 0){";
                            $script = $script."actres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."actres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}";
                            $script = $script."});";

                            // validate amount - tamt tamtres
                            //$script = $script."function isNumeric(n){";
                            $script = $script."var tamt=$('#tamt');";
                            $script = $script."var tamtres=$('#tamtres');";
                            $script = $script."if(tamt.val().length == 0){";
                            $script = $script."tamtres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            $script = $script."}else if(isNumeric(tamt.val())){";
                            $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}";
                            $script = $script."tamt.on('blur keyup focus change', function() {";
                            $script = $script."if(tamt.val().length == 0){";
                            $script = $script."tamtres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            $script = $script."}else if(isNumeric(tamt.val())){";
                            $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}";
                            $script = $script."});";
        
                            // validate comments - comments commentsres
                            $script = $script."var comments=$('#comments');";
                            $script = $script."var commentsres=$('#commentsres');";
                            $script = $script."if(comments.val().length == 0){";
                            $script = $script."commentsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."if(comments.val().length >= 3){";
                            $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}";
                            $script = $script."}";
                            $script = $script."comments.on('blur keyup focus', function() {";
                            $script = $script."if(comments.val().length == 0){";
                            $script = $script."commentsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            $script = $script."}else{";
                            $script = $script."if(comments.val().length >= 3){";
                            $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
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
                            $script = $script."var crdate=$('#edate');";
                            $script = $script."var crdateres=$('#edateres');";
                            $script = $script."if (supportsHTML5Date() == false){";
                            $script = $script."if(crdate.val().length == 0){";
                            $script = $script."crdate.val(crdate.attr('data'));";
                            //$script = $script."$('#viewCreationDate').html(crdate.val());";
                            $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            $script = $script."}";
                            $script = $script."if(checkdate(crdate)){";
                            $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                            $script = $script."var curDate = Date.now();";
                            $script = $script."if (selDate.getTime() <= curDate) {";
                            $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}";
                            //$script = $script."$('#viewCreationDate').html(crdate.val());";
                            $script = $script."}else{";
                            $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            //$script = $script."$('#viewCreationDate').html('');";
                            $script = $script."}";
                            $script = $script."crdate.on('blur keyup focus', function() {";
                            $script = $script."if(crdate.val().length == 0){";
                            $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                            //$script = $script."$('#viewCreationDate').html('');";
                            $script = $script."}else if(checkdate(crdate)){";
                            $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                            $script = $script."var curDate = Date.now();";
                            $script = $script."if (selDate.getTime() <= curDate) {";
                            $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                            $script = $script."}else{";
                            $script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            $script = $script."}";
                            //$script = $script."$('#viewCreationDate').html(crdate.val());";
                            $script = $script."}else{";
                            $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                            //$script = $script."$('#viewCreationDate').html('');";
                            $script = $script."}";
                            $script = $script."});";
                            $script = $script."}";
                            // end validate

                            // edit button action
                            // btnEditExp
                            $script = $script."$('#btnEditExp').on('click', function(e){";
                                // edit action

                                $script = $script."$('#altMsgNewExp').removeClass('alert-danger').html('').hide();";

                                $script = $script."var btn=$(this);";
                                $script = $script."var eid=btn.attr('eid');";
                                $script = $script."var engres=$('#engres');";
                                $script = $script."var actres=$('#actres');";
                                $script = $script."var tamtres=$('#tamtres');";
                                $script = $script."var commentsres=$('#commentsres');";
                                $script = $script."var crdateres=$('#edateres');";

                                $script = $script."if(engres.hasClass('pw_weak') || actres.hasClass('pw_weak') || tamtres.hasClass('pw_weak') || crdateres.hasClass('pw_weak')){";
                                    $script = $script."$('#altMsgNewExp').addClass('alert-danger').html('Missing required information.').show();";
                                $script = $script."}else{";
                                    
                                    // save expense
                                    $script = $script."var itm=$('#act :selected').attr('value');";
                                    $script = $script."var dsc=$('#comments').val();";
                                    $script = $script."var dte=$('#edate').val();";
                                    $script = $script."var amt=$('#tamt').val();";
                                    $script = $script."var intattid=$('#eng :selected').attr('value');";
                                    $script = $script."var intatttype=$('#eng :selected').attr('type');";
                                    $script = $script."var inid=0;";
                                    $script = $script."var atid=0;";
                                    $script = $script."if(intatttype == 'att') {";
                                        $script = $script."atid=intattid;";
                                    $script = $script."}else{";
                                        $script = $script."inid=intattid;";
                                    $script = $script."}";

                                    $script = $script."var data = {exp_id:eid,item:itm,desc:dsc,dte_occured:dte,amount:amt,intid:inid,attid:atid};";
                                    $script = $script."postData('".site_url("edit/edit_expense")."',data,btn,function(res){";
                                    $script = $script."if(res.result){";

                                        // reload data
                                        $script = $script."var uid='".$user_id."';";
                                        $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                                        $script = $script."var yer=$('#selYear :selected').attr('value');";
                                        $script = $script."var cid='".$case_id."';";
                                        $script = $script."loadRows(uid,cid,mnt,yer);";
                                        // close modal
                                        $script = $script."$('#btnCloseWin').click();";

                                    $script = $script."}else{";
                                        $script = $script."$('#altMsgNewExp').addClass('alert-danger').html('Unable to save expense.').show();";
                                    $script = $script."}";
                                    $script = $script."});";
                                $script = $script."}";

                                // end edit action
                            $script = $script."});";  

                        $script = $script."}else{";
                            $script = $script."alert('Unable to load expense.');";
                            $script = $script."return false;";
                        $script = $script."}";
                        $script = $script."});";

                    $script = $script."}else{";
                        $script = $script."alert('Unable to load expense.');";
                        $script = $script."return false;";
                    $script = $script."}";
                $script = $script."});";

            $script = $script."});";

            // delete button
            $script = $script."$(document).on('click', '.delbtn', function(){";
                $script = $script."var cnfdel = confirm('Delete: Are you sure?');";
                $script = $script."if (cnfdel == true) {";

                    $script = $script."var eid=$(this).attr('data');";
                    $script = $script."var uid=$(this).attr('uid');";
                    $script = $script."var btn=$(this);";
                    
                    $script = $script."var data = {exp_id:eid};";
                    $script = $script."postData('".site_url("edit/delete_expense")."',data,btn,function(res){";
                    $script = $script."if(res.result){";

                        // reload rows
                        $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                        $script = $script."var yer=$('#selYear :selected').attr('value');";
                        $script = $script."var cid='".$case_id."';";
                        $script = $script."loadRows(uid,cid,mnt,yer);";

                    $script = $script."}else{";
                    $script = $script."alert('Unable to delete expense.');";
                    $script = $script."}";
                    $script = $script."});";

                $script = $script."}";

            $script = $script."});";

            // update rate
            $script = $script."$(document).on('click', '.btnsave', function(){";
                $script = $script."var uid=$(this).attr('uid');";
                $script = $script."var itm=$(this).attr('item');";
                $script = $script."var amt=$(this).attr('amt');";
                $script = $script."var dat=$(this).attr('data');";
                $script = $script."var expid=$(this).attr('expid');";
                $script = $script."var btn=$(this);";
                // save value
                $script = $script."var data = {catid:itm,userid:uid,amount:amt};";
                $script = $script."postData('".site_url("add/add_edit_rate")."',data,btn,function(res){";
                $script = $script."if(res.result){";

                    // calculate
                    $script = $script."var tot=parseFloat(dat)*parseFloat(amt);";
                    $script = $script."$('#tot_'+expid).html(tot);";

                    // remove button
                    $script = $script."btn.parent('.btnholder').html('');";

                $script = $script."}else{";
                $script = $script."alert('Unable to save expense.');";
                $script = $script."}";
                $script = $script."});";
                
            $script = $script."});";

            // update view from combo box change
            $script = $script."$(document).on('change', '#selAgent,#selMonth,#selYear', function(){";
                // reload rows
                $script = $script."var uid=$('#selAgent :selected').attr('value');";
                $script = $script."if(!uid){";
                    $script = $script."uid='".$user_id."';";
                $script = $script."}";

                $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                $script = $script."var yer=$('#selYear :selected').attr('value');";
                $script = $script."var cid='".$case_id."';";
                $script = $script."loadRows(uid,cid,mnt,yer);";
            $script = $script."});";

            // invoice button
            $script = $script."$('#btnSendInvoice').on('click', function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                $script = $script."var yer=$('#selYear :selected').attr('value');";
                $script = $script."var selid=0;"; //invoice for all users
                $script = $script."var cid='".$case_id."';";

                $script = $script."var data = {caseid:cid,month:mnt,year:yer};";
                $script = $script."postData('".site_url("data/send_invoice")."',data,null,function(res){";
                $script = $script."if(res.result){";

                    $script = $script."alert('Invoice has been sent.');";

                $script = $script."}else{";
                $script = $script."alert('Unable to send invoice.');";
                $script = $script."}";
                $script = $script."});";

            $script = $script."});";

            // csv button
            $script = $script."$('#btnGenCSV').on('click', function(e){";
                $script = $script."e.preventDefault();";
                $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                $script = $script."var yer=$('#selYear :selected').attr('value');";
                $script = $script."var selid=$('#selAgent :selected').attr('value');";
                $script = $script."var cid='".$case_id."';";
                $script = $script."if(!selid){";
                    $script = $script."selid='".$user_id."';";
                $script = $script."}";

                $script = $script."var form='';";

                $script = $script."form = '<form id=\"bil_csv\" action=\"".site_url("data/billing_csv")."\" method=\"post\">';";
                $script = $script."form +='<input type=\"hidden\" name=\"userid\" value=\"'+selid+'\">';";
                $script = $script."form +='<input type=\"hidden\" name=\"caseid\" value=\"'+cid+'\">';";
                $script = $script."form +='<input type=\"hidden\" name=\"month\" value=\"'+mnt+'\">';";
                $script = $script."form +='<input type=\"hidden\" name=\"year\" value=\"'+yer+'\">';";
                $script = $script."form +='</form>';";

                $script = $script."var mdlCont = $('#modal_container');";
                $script = $script."mdlCont.html(form);";
                $script = $script."$('#bil_csv').submit();";

            $script = $script."});";

            // add new expense
            $script = $script."$('#btnAddNewExpense').on('click', function(e){";
                $script = $script."e.preventDefault();";

                $script = $script."var cid='".$case_id."';";
                $script = $script."var uid='".$user_id."';";
                $script = $script."var cmid='".$company_id."';";
                $script = $script."var data = {userid:uid,compid:cmid,caseid:cid};";
                $script = $script."postData('".site_url("data/cases_cats_atts_ints")."',data,null,function(res){";
                    $script = $script."if(res.result){";

                        $script = $script."var cats=res.cats;";
                        $script = $script."var atts=res.atts;";
                        $script = $script."var ints=res.interviews;";

                        $script = $script."openModal('Add New Expense',buildNewExpForm(cats,atts,ints));";
                        $script = $script."$('#altMsgNewExp').hide();";

                        // validate case
                        $script = $script."var cse=$('#cname');";
                        $script = $script."var cseres=$('#cnameres');";
                        $script = $script."cse.on('blur keyup focus change', function() {";
                        $script = $script."if(cse.val() == 0){";
                        $script = $script."cseres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}else{";
                        $script = $script."cseres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}";
                        $script = $script."});";

                        // validate engagment
                        $script = $script."var eng=$('#eng');";
                        $script = $script."var engres=$('#engres');";
                        $script = $script."eng.on('blur keyup focus change', function() {";
                        $script = $script."if(eng.val() == 0){";
                        $script = $script."engres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}else{";
                        $script = $script."engres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}";
                        $script = $script."});";

                        // validate activity act
                        $script = $script."var act=$('#act');";
                        $script = $script."var actres=$('#actres');";
                        $script = $script."act.on('blur keyup focus change', function() {";
                        $script = $script."if(act.val() == 0){";
                        $script = $script."actres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}else{";
                        $script = $script."actres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}";
                        $script = $script."});";

                        // validate amount - tamt tamtres
                        //$script = $script."function isNumeric(n){";
                        $script = $script."var tamt=$('#tamt');";
                        $script = $script."var tamtres=$('#tamtres');";
                        $script = $script."tamt.on('blur keyup focus change', function() {";
                        $script = $script."if(tamt.val().length == 0){";
                        $script = $script."tamtres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                        $script = $script."}else if(isNumeric(tamt.val())){";
                        $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}else{";
                        $script = $script."tamtres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}";
                        $script = $script."});";
    
                        // validate comments - comments commentsres
                        $script = $script."var comments=$('#comments');";
                        $script = $script."var commentsres=$('#commentsres');";
                        $script = $script."comments.on('blur keyup focus', function() {";
                        $script = $script."if(comments.val().length == 0){";
                        $script = $script."commentsres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                        $script = $script."}else{";
                        $script = $script."if(comments.val().length >= 3){";
                        $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}else{";
                        $script = $script."commentsres.html('<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
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
                        $script = $script."var crdate=$('#edate');";
                        $script = $script."var crdateres=$('#edateres');";
                        $script = $script."if (supportsHTML5Date() == false){";
                        $script = $script."if(crdate.val().length == 0){";
                        $script = $script."crdate.val(crdate.attr('data'));";
                        //$script = $script."$('#viewCreationDate').html(crdate.val());";
                        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                        $script = $script."}";
                        $script = $script."if(checkdate(crdate)){";
                        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                        $script = $script."var curDate = Date.now();";
                        $script = $script."if (selDate.getTime() <= curDate) {";
                        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}else{";
                        $script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}";
                        //$script = $script."$('#viewCreationDate').html(crdate.val());";
                        $script = $script."}else{";
                        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        //$script = $script."$('#viewCreationDate').html('');";
                        $script = $script."}";
                        $script = $script."crdate.on('blur keyup focus', function() {";
                        $script = $script."if(crdate.val().length == 0){";
                        $script = $script."crdateres.html('').removeClass('pw_strong').removeClass('pw_weak');";
                        //$script = $script."$('#viewCreationDate').html('');";
                        $script = $script."}else if(checkdate(crdate)){";
                        $script = $script."var selDate = new Date(crdate.val()+' UTC');";
                        $script = $script."var curDate = Date.now();";
                        $script = $script."if (selDate.getTime() <= curDate) {";
                        $script = $script."crdateres.html('<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_strong');";
                        $script = $script."}else{";
                        $script = $script."crdateres.html('Today or earlier').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        $script = $script."}";
                        //$script = $script."$('#viewCreationDate').html(crdate.val());";
                        $script = $script."}else{";
                        $script = $script."crdateres.html('MM/DD/YYYY').removeClass('pw_strong').removeClass('pw_weak').addClass('pw_weak');";
                        //$script = $script."$('#viewCreationDate').html('');";
                        $script = $script."}";
                        $script = $script."});";
                        $script = $script."}";

                        //btnSaveNewExp
                        $script = $script."$('#btnSaveNewExp').on('click', function(e){";

                            $script = $script."$('#altMsgNewExp').removeClass('alert-danger').html('').hide();";

                            $script = $script."var btn=$(this);";
                            $script = $script."var engres=$('#engres');";
                            $script = $script."var actres=$('#actres');";
                            $script = $script."var tamtres=$('#tamtres');";
                            $script = $script."var commentsres=$('#commentsres');";
                            $script = $script."var crdateres=$('#edateres');";

                            $script = $script."if(engres.hasClass('pw_weak') || actres.hasClass('pw_weak') || tamtres.hasClass('pw_weak') || crdateres.hasClass('pw_weak')){";
                                $script = $script."$('#altMsgNewExp').addClass('alert-danger').html('Missing required information.').show();";
                            $script = $script."}else{";
                                
                                // save expense
                                $script = $script."var uid='".$user_id."';";
                                $script = $script."var cid='".$case_id."';";
                                $script = $script."var itm=$('#act :selected').attr('value');";
                                $script = $script."var dsc=$('#comments').val();";
                                $script = $script."var dte=$('#edate').val();";
                                $script = $script."var amt=$('#tamt').val();";
                                $script = $script."var intattid=$('#eng :selected').attr('value');";
                                $script = $script."var intatttype=$('#eng :selected').attr('type');";
                                $script = $script."var inid=0;";
                                $script = $script."var atid=0;";
                                $script = $script."if(intatttype == 'att') {";
                                    $script = $script."atid=intattid;";
                                $script = $script."}else{";
                                    $script = $script."inid=intattid;";
                                $script = $script."}";

                                $script = $script."var data = {caseid:cid,userid:uid,item:itm,desc:dsc,dte_occured:dte,amount:amt,intid:inid,attid:atid};";
                                $script = $script."postData('".site_url("add/add_expense")."',data,btn,function(res){";
                                $script = $script."if(res.result){";

                                    // reload data
                                    $script = $script."var mnt=$('#selMonth :selected').attr('value');";
                                    $script = $script."var yer=$('#selYear :selected').attr('value');";
                                    $script = $script."var cid='".$case_id."';";
                                    $script = $script."loadRows(uid,cid,mnt,yer);";
                                    // close modal
                                    $script = $script."$('#btnCloseWin').click();";

                                $script = $script."}else{";
                                $script = $script."$('#altMsgNewExp').addClass('alert-danger').html('Unable to save expense.').show();";
                                $script = $script."}";
                                $script = $script."});";
                            $script = $script."}";
                        $script = $script."});";
                    $script = $script."}else{";
                        $script = $script."alert('An Error Occured: '+res.msg);";
                        $script = $script."return false;";
                    $script = $script."}";
                $script = $script."});";

            $script = $script."});";

        $script = $script."});";

        return $script;
    }
}