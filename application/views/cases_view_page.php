
	<div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb" id="brdMain">
                <li><a href="<?php echo site_url('mycases'); ?>">Cases</a></li>
                <li class="active"><?php echo $case_name; ?></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h2 class="top">
                <span id="dvTitle"><?php echo $case_name; ?></span>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                <button type="button" id="btnEditCaseName" data="<?php echo $case_name; ?>" class="btn btn-default btn-sm" title="Edit Case Name"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></button></a>
<?php
}
?>
            </h2>
        </div>
        <div class="col-xs-12 col-md-6" style="text-align:right;">
            <div class="leftcommand" role="nav">
                <button type="button" id="btnIntInd" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Interview Index</button>
                <button type="button" id="btnAttInd" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> Attachment Index</button>
                <button type="button" id="btnLeadSheet" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Lead Sheet</button>
                <a href="<?php echo site_url("mybilling")."/".$case_id ?>" id="btnBilling" class="btn btn-sm btn-success">Billing</a>
                <a href="<?php echo site_url("myreports")."/".$case_id ?>" id="btnReport" class="btn btn-sm btn-success">Report</a>
            </div>
        </div>
    </div>
    <div class="row" id="contents">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="active" id="tabCase" role="presentation"><a href="#case">Case</a></li>
                <li class="" id="tabSynopsis" role="presentation"><a href="#synopsis">Synopsis</a></li>
                <li class="" id="tabInterviews" role="presentation"><a href="#interviews">Interviews</a></li>
                <li class="" id="tabAttachments" role="presentation"><a href="#attachments">Attachments</a></li>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                <li class="" id="tabAdministrative" role="presentation"><a href="#administrative">Administrative</a></li>
<?php
}
?>
            </ul>
            <div id="pnlMainCase" class="pnl row" style="padding-top:20px;">
                <div class="col-xs-12 col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Contents:</div>
                        <table class="table">
                                <tr>
                                    <td><strong>Last Updated</strong></td>
                                    <td><?php echo date('m/d/Y H:i:s', strtotime($case_modified)); ?></td>
                                    <td>&nbsp;</td>
                                <tr>
<?php
if(isset($client) && count($client) > 0) {
?>
                                <tr>
                                    <td><strong>Client</strong></td>
                                    <td id="td_client"><?php echo $client['name']; ?></td>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                                    <td><button type="button" data="<?php echo $client['id']; ?>" class="btn btn-default btn-xs" id="btnEditClient"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td>
<?php
}
?>
                                <tr>
<?php
}
if (isset($supporting) && count($supporting) > 0) {
    foreach ($supporting as $sup) {
        $type = $sup['profession'];
        $name = $sup['name'];
?>
                                <tr>
                                    <td><strong><?php echo $type; ?></strong></td>
                                    <td id="<?php echo "td_".$sup['id']; ?>"><?php echo $name; ?></td>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                                    <td><button type="button" data="<?php echo $sup['id']; ?>" sup="<?php echo $type; ?>" class="btn btn-default btn-xs btnedtdetail"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td>
<?php
}
?>
                                <tr>
<?php
    }
}
?>
                            </table>
<?php
$intCount = 0;
if (isset($interviews) && count($interviews) > 0){
    $intCount = count($interviews);
}
?>
                        <button type="button" id="btnInterviews" class="list-group-item list-group-item-plain selform"><span id="udInterviews" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Interviews <span id="viewInterviews" class="badge pull-right"><?php echo $intCount; ?></span></button>
                        <div class="list-group-item" id="pnlInterviews">
                            <table class="table table-hover" id="tableIntMain">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Agent</th>
                                        <th>Date</th>
                                        <th>Approved</th>
                                    </tr>
                                </thead>
                                <tbody id="tblIntListTab">
<?php
if (isset($interviews) && count($interviews) > 0){
    foreach ($interviews as $int) {
?>
                                    <tr>
                                        <td><a href="#" class="ancintview" data="<?php echo $int['id'] ?>"><?php echo $int['name']; ?></a></td>
                                        <td><?php echo $int['user_name']; ?></td>
                                        <td><?php echo date('m/d/Y', strtotime($int['date_occured'])); ?></td>
<?php
        $is_approved = '<input id="chk_intapp_'.$int['id'].'" class="chk_intapp" date="'.date('m/d/Y', strtotime($int['date_occured'])).'" username="'.$int['user_name'].'" name="'.$int['name'].'" data="'.$int['id'].'" value="1" type="checkbox">';
        if($int['is_approved'] == '1') {
            $is_approved = '<input id="chk_intapp_'.$int['id'].'" class="chk_intapp" date="'.date('m/d/Y', strtotime($int['date_occured'])).'" username="'.$int['user_name'].'" name="'.$int['name'].'" data="'.$int['id'].'" value="1" type="checkbox" checked>';
        }
?>
                                        <td style="text-align:right;"><?php echo $is_approved ?></td>
                                    </tr>
<?php
    }
}else{
?>
                                    <tr>
                                        <td colspan="4">No Interviews</td>
                                    </tr>
<?php
}
?>
                                </tbody>
                            </table>
                            <div class="" style="text-align:right;">
                                <button type="button" id="btnAddInterview" class="btn btn-success">Add Interview</button>
                            </div>
                        </div>
<?php
$attCount = 0;
if (isset($attachments) && count($attachments) > 0){
    $attCount = count($attachments);
}
?>
                        <button type="button" id="btnAttachments" class="list-group-item list-group-item-plain selform"><span id="udAttachments" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Attachments <span id="numAttachments" class="badge pull-right"><?php echo $attCount; ?></span></button>
                        <div class="list-group-item" id="pnlAttachments">
                            <table class="table table-striped" id="tableAttSub">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Type</th>
                                        <th>Number</th>
                                        <th>Obtained</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAtt">
<?php
if (isset($attachments) && count($attachments) > 0){
    foreach ($attachments as $doc) {

        $cntrl_text = "&nbsp;";

        if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
            $cntrl_text = "<button type=\"button\" class=\"btn btn-danger btn-xs delatt\" dataid=\"".$doc['id']."\" dataname=\"".$doc['number']."\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button>";
        }
?>
                                    <tr>
                                        <td><?php echo $cntrl_text; ?></td>
                                        <td><img src="<?php echo $doc['icon']; ?>" style="width:24px;height:24px;"> <?php echo $doc['postfix']; ?></td>
                                        <td><a href="<?php echo $doc['url']; ?>" class="ancAtt"><?php echo $doc['number']; ?></a></td>
                                        <td><?php echo date('m/d/Y', strtotime($doc['created'])); ?></td>
                                    </tr>
<?php
    }
}else{
?>
                                    <tr>
                                        <td colspan="4">No Attachments</td>
                                    </tr>
<?php
}
?>
                                </tbody>
                            </table>
                            <div class="" style="text-align:right;">
                                <button type="button" id="btnAddAtt" class="btn btn-success">Add Attachment</button>
                            </div>
                        </div>
<?php
$teamCount = 0;
if (isset($team) && count($team) > 0){
    $teamCount = count($team);
}
?>
                        <button type="button" id="btnTeam" class="list-group-item list-group-item-plain selform"><span id="udTeam" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Team <span id="viewTeam" class="badge pull-right"><?php echo $teamCount; ?></span></button>
                        <div class="list-group-item" id="pnlTeam">
                            <table class="table table-striped" id="tableSupDocsPanel">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySupDocsPanel">
<?php
if (isset($team) && count($team) > 0){
    foreach ($team as $mem) {

        $role = "Collaborator";

        if ($mem['is_case_admin']) {
            $role = "Lead";
        }
?>
                                    <tr>
                                        <td><img src="<?php echo base_url('img/user')."/".$mem['image']; ?>" style="width:24px;height:24px;"> <?php echo $mem['name']; ?></td>
                                        <td><?php echo $mem['title']; ?></td>
                                        <td><?php echo $role;?></td>
                                    </tr>
<?php
    }
}else{
?>
                                    <tr>
                                        <td colspan="3">No Team</td>
                                    </tr>
<?php
}
?>
                                </tbody>
                            </table>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                            <div class="" style="text-align:right;">
                                <button type="button" id="btnAddTeam" class="btn btn-success">Edit Team</button>
                            </div>
<?php
}
?>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-7">
                    <p class="lead">
                        Welcome to the main control center for your case. From here you can view, add, or edit all case information. Choose from the selection below:
                    </p>
                    <a href="#interviews" class="menu"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Interview</a>
                    <a href="#" id="ancAddAttachments" class="menu"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Attachment</a>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?> 
                    <a href="#" id="ancAddTeamMember" class="menu"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Team Member</a>
<?php
}
?>        
                    <a id="ancLeadSheet" href="#" class="menu"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Lead</a>
<?php
// only show options if is admin or team lead
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?> 
                    <br>
                    <div style="padding:10px;width:350px;">
                        <strong>Mark case as:</strong>&nbsp;
<?php
$openClass = "";
$closedClass = "";
if(isset($case_closed)) {
    if ($case_closed == TRUE || $case_closed == 1) {
        $openClass = "";
        $closedClass = " active";
    } else {
        $openClass = " active";
        $closedClass = "";
    }
}
?>
                        <button id="btnOpen" class="btn btn-default<?php echo $openClass ?>" type="submit">Open</button>&nbsp;<button id="btnClosed" class="btn btn-default<?php echo $closedClass ?>" type="submit">Closed</button>
                    </div>
<?php
}
?> 
                </div>
            </div>
            <div id="pnlMainSynopsis" class="pnl row" style="padding-top:20px;">
                <div class="col-xs-12 col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Confidentiality Documents:</div>
                        <div class="panel-body">
                            Select documents to include in synopsis. Click to view.
                        </div>
<?php
$conf_html_docs = "";
$admin_html_docs = "";
if (isset($admin_docs_cats) && count($admin_docs_cats) > 0) {

    foreach ($admin_docs_cats as $doc) {
        
        $row = "";
        $id = intval($doc['id']);
        $name = $doc['name'];
        $cat = $doc['category'];

        $chk_name = "";

        if ($cat == 1) {
            $chk_name = "chkConfDocs";
        } else if ($cat == 2) {
            $chk_name = "chkAdminDocs";
        }

        $glph_class = "glyphicon glyphicon-plus text-warning";
        $file_url = "";
        $file_userid = "";
        $doc_count = 0;
        $disp_text = "";
        $name_disp = $name;
        $checked = "";
        $doc_ids = "";
        $av_doc_ids = "";
        $show_row = TRUE;
        $btnClass = "btnupload";
        $btnValue = "New";

        // see if checked
        if (isset($admin_docs[$id][0]) && is_array($admin_docs[$id][0])) {
            $doc_count = count($admin_docs[$id]);

            // get all admin doc ids
            foreach ($admin_docs[$id] as $ad) {
                $doc_ids = $doc_ids.",".$ad['id'];
            }

            // remove extra commas
            $doc_ids = trim($doc_ids, ",");

            $name_other = $name;
            // handle display text if other
            if($id == 6 || $id>=16) {
                $title = $admin_docs[$id][0]['title'];
                $name_other = $title;
            }

            $icon = $admin_docs[$id][0]['icon'];
            $filename = $admin_docs[$id][0]['filename'];
            $date_added = $admin_docs[$id][0]['date_added'];
            $date_added_format = date('m/d/Y', strtotime($date_added));

            $glph_class = "glyphicon glyphicon-ok text-success";
            if($cat == 1) {
                $name_disp = "<a href=\"#\" data=\"".$id."\" added=\"".$date_added_format."\" icon=\"".$icon."\" fname=\"".$filename."\" docs=\"".$doc_ids."\" class=\"ancDocView\">".$name_other."</a>";
            } else if ($cat == 2) {
                $name_disp = "<a href=\"#\" data=\"".$id."\" added=\"".$date_added_format."\" icon=\"".$icon."\" fname=\"".$filename."\" docs=\"".$doc_ids."\" class=\"ancDocViewAdmin\">".$name_other."</a>";
            }
            $checked = "checked=true";
        }

        if (isset($available_docs[$id]) && is_array($available_docs[$id]) ){

            // get all admin doc ids
            foreach ($available_docs[$id] as $ad) {
                $av_doc_ids = $av_doc_ids.",".$ad['id'];
            }

            // remove extra commas
            $av_doc_ids = trim($av_doc_ids, ",");

            $glph_class = "glyphicon glyphicon-ok text-success";
        }

        if ($doc_count > 1 && $cat == 2) {
            $glph_class = "badge";
            $disp_text = $doc_count;
        }

        if ($cat == 2) {
            $btnClass = "btnuploadadmin";
            $btnValue = "Add";
        }

        $row = $row."<tr>";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $row = $row."<td><input type=\"checkbox\" id=\"chk_".$id."\" avdocs=\"".$av_doc_ids."\" docs=\"".$doc_ids."\" name=\"".$chk_name."\" data=\"".$id."\" value=\"".$name."\" ".$checked."></td>";
}else{
        $row = $row."<td><span class=\"".$glph_class."\" id=\"spn_".$id."\">".$disp_text."</span></td>";
}
        $row = $row."<td title=\"".$name."\">".$name_disp."</td>";
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $row = $row."<td><span class=\"".$glph_class."\" id=\"spn_".$id."\">".$disp_text."</span></td>";
}else{
        $row = $row."<td>&nbsp;</td>";
}
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
        $row = $row."<td><button type=\"button\" class=\"btn btn-info btn-xs ".$btnClass."\" cat=\"".$cat."\" data=\"".$id."\">".$btnValue."</button></td>";
}else{
        $row = $row."<td>&nbsp;</td>";
}
        $row = $row."</tr>";

        if($name_disp == 'Other' && $id >= 17) {
            $show_row = FALSE;
        }

        if($show_row) {
            if ($cat == 1) {
                $conf_html_docs = $conf_html_docs.$row;
            } else if ($cat == 2) {
                $admin_html_docs = $admin_html_docs.$row;
            }
        }
        
    }

}
?>
                        <table class="table table-hover">
                            <tbody id="tblConfDocs">
                            <?php echo $conf_html_docs; ?>
                            </tbody>
                        </table>
                        <div class="panel-footer">
                            <button type="button" class="btn btn-default" title="Add More Doc Types" id="btnAddConfDocs"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
<?php
$doc_view = "<div id=\"edSynopsis\" style=\"height:350px;\"></div>";
$last_update_text = "Type or import your Synopsis: ";
$import_text = "Import";
$btn_save_text = "Save Synopsis";
if (isset($synopsis) && count($synopsis) > 0) {

    $last_updated = date('m/d/Y H:i:s', strtotime($synopsis['modified']));
    $last_update_text = "<strong>Last Updated On</strong>: ".$last_updated.": ";
    $import_text = "Import New";
    $btn_save_text = "Save Synopsis";

    if (strlen($synopsis['contents']) > 0 ) {
        if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
            $doc_view = "<div id=\"edSynopsis\" style=\"height:350px;\">".$synopsis['contents']."</div>";
        }else{
            $doc_view = "<div style=\"padding:5px;border:1px solid #ddd;height:445px;overflow:scroll;\">".$synopsis['contents']."</div>";
        }
    } else if (strlen($synopsis['location'] > 0)) {

        // determine viewer
        $script = $script."switch(ft[1]){";
        $script = $script."case 'txt': vw +='<iframe src=\"'+url+'\"></iframe>'; break;";
        $script = $script."case 'docx': vw +='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."case 'pdf': vw +='<iframe src=\"http://docs.google.com/gview?url='+encodeURI(url)+'&embedded=true\"></iframe>'; break;";
        $script = $script."}";

        $disp_url = "";
        $ft = explode(".", $synopsis['url']);
        if (count($ft) > 0) {
            switch (strtolower($ft[1])) {
                case 'txt':
                    $disp_url = "<iframe src=\"".$synopsis['url']."\"></iframe>";
                    break;
                case 'docx':
                    $disp_url = "<iframe src=\"http://docs.google.com/gview?url=".urlencode($synopsis['url'])."&embedded=true\"></iframe>";
                    break;
                case 'pdf':
                    $disp_url = "<iframe src=\"http://docs.google.com/gview?url=".urlencode($synopsis['url'])."&embedded=true\"></iframe>";
                    break;
                default:
                    $disp_url = "<iframe src=\"".$synopsis['url']."\"></iframe>";
                    break;
            }
        }

        $doc_view = "<div class=\"responsive-doc\" id=\"attViewer\">";
        $doc_view = $doc_view."<iframe src=\""+$disp_url+"\"></iframe>";
        $doc_view = $doc_view."</div>";

        $btn_save_text = "Type New Synopsis";
    }
}
?>
                <div class="col-xs-12 col-md-7">
                    <h3 style="margin-top:0px;">Synopsis</h3>
<?php
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                    <div style="background-color:#f2f2f2;margin-bottom:10px;text-align:right;padding:10px;">
                        <?php echo $last_update_text; ?><button type="button" class="btn btn-info" title="Import Synopsis" id="btnImportSynopsis"><?php echo $import_text; ?></button>
                    </div>
<?php
}
?>
                    <div id="docSynopsis">
                        <?php echo $doc_view; ?>
                    </div>
<?php
if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
?>
                    <div style="background-color:#f2f2f2;margin-top:10px;margin-bottom:10px;text-align:right;padding:10px;">
                        <button type="button" class="btn btn-success" title="<?php echo $btn_save_text; ?>" id="btnSaveSynopsis"><?php echo $btn_save_text; ?></button>
                    </div>
<?php
}
?>
                </div>
            </div>
            <div id="pnlMainInterviews" class="pnl row" style="padding-top:20px;">
                <div class="col-xs-12 col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Interviews:</div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-success" title="Add Interview" id="btnAddInterview">Add New</button>
                        </div>
                        <table class="table table-hover" id="tableIntMain">
                            <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Agent</td>
                                    <td>Date</td>
                                    <td>Approved</td>
                                </tr>
                            </thead>
                            <tbody id="tblIntList">
<?php
$intCount = 0;
if (isset($interviews) && count($interviews) > 0){
    $intCount = count($interviews);
    foreach ($interviews as $int) {
?>
                                    <tr>
                                        <td><a href="#" class="ancintview" data="<?php echo $int['id'] ?>"><?php echo $int['name']; ?></a></td>
                                        <td><?php echo $int['user_name']; ?></td>
                                        <td><?php echo date('m/d/Y', strtotime($int['date_occured'])); ?></td>
<?php
    if($is_admin) {
        $is_approved = '<input id="chk_intapp_'.$int['id'].'" class="chk_intapp" date="'.date('m/d/Y', strtotime($int['date_occured'])).'" username="'.$int['user_name'].'" name="'.$int['name'].'" data="'.$int['id'].'" value="1" type="checkbox">';
        if($int['is_approved'] == '1') {
            $is_approved = '<input id="chk_intapp_'.$int['id'].'" class="chk_intapp" date="'.date('m/d/Y', strtotime($int['date_occured'])).'" username="'.$int['user_name'].'" name="'.$int['name'].'" data="'.$int['id'].'" value="1" type="checkbox" checked>';
        }
    } else {
        $is_approved = 'False';
        if($int['is_approved'] == '1') {
            $is_approved = 'True';
        }
    }
?>
                                        <td style="text-align:right;"><?php echo $is_approved ?></td>
                                    </tr>
<?php
    }
}else{
?>
                                    <tr>
                                        <td colspan="4">No Interviews</td>
                                    </tr>
<?php
}
?>                                
                            </tbody>
                        </table>
                        <div class="panel-footer" style="text-align:right;" id="intMainCount">
                            <span id="intCount"><?php echo $intCount; ?></span> Interview(s)
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-7">
                    <h3 style="margin-top:0;">View Interview</h3>
                    <div id="dvIntBanner" style="background-color:#f2f2f2;margin-top:10px;margin-bottom:10px;padding:10px;" id="intViewText">
                        Select an interview to view.
                    </div>
                    <div id="dvIntView">
                    </div>
                </div>
            </div>
            <div id="pnlMainAttachments" class="pnl row" style="padding-top:20px;">
                <div class="col-xs-12 col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">Attachments:</div>
                        <div class="panel-body">
                            <button type="button" class="btn btn-success" title="Add Attachment" id="btnAddAttachment">Add New</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tableAttMain">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Type</th>
                                        <th>Number</th>
                                        <th>Obtained</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyAttMain">
<?php
if (isset($attachments) && count($attachments) > 0){
    foreach ($attachments as $doc) {

        $cntrl_text = "&nbsp;";

        if ($is_team_lead || (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE))) {
            $cntrl_text = "<button type=\"button\" class=\"btn btn-danger btn-xs delatt\" dataid=\"".$doc['id']."\" dataname=\"".$doc['number']."\" aria-label=\"Delete Attachment\" title=\"Delete Attachment\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button>";
        }
?>
                                    <tr>
                                        <td><?php echo $cntrl_text; ?></td>
                                        <td><img src="<?php echo $doc['icon']; ?>" style="width:24px;height:24px;"> <?php echo $doc['postfix']; ?></td>
                                        <td><a href="<?php echo $doc['url']; ?>" class="ancAtt"><?php echo $doc['number']; ?></a></td>
                                        <td><?php echo date('m/d/Y', strtotime($doc['created'])); ?></td>
                                    </tr>
<?php
    }
}else{
?>
                                    <tr>
                                        <td colspan="4">No Attachments</td>
                                    </tr>
<?php
}
?>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer" style="text-align:right;" id="attMainCount">
                            <?php echo $attCount; ?> Attachment(s)
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-7">
                    <h3 style="margin-top:0;">View Attachment</h3>
                    <div style="background-color:#f2f2f2;margin-top:10px;margin-bottom:10px;padding:10px;" id="attViewText">
                        Select an attachment to view.
                    </div>
                    <div class="responsive-doc" id="attViewer">
                        <iframe src=""></iframe>
                    </div>
                </div>
            </div>
            <div id="pnlMainAdministrative" class="pnl row" style="padding-top:20px;">
                <div class="col-xs-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Administrative Documents:</div>
                        <div class="panel-body">
                            Select documents to include in report. Click to view.
                        </div>
                        <table class="table table-hover">
                            <tbody id="tblAdminDocs">
                            <?php echo $admin_html_docs; ?>
                            </tbody>
                        </table>
                        <div class="panel-footer">
                            <button type="button" class="btn btn-default" title="Add More Doc Types" id="btnAddAdminDocs"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Team:</div>
                        <div class="panel-body">
                            <button type="button" id="btnAddNewTeam" class="btn btn-success">Edit Team</button>
                        </div>
                        <table class="table table-striped" id="tableSupDocsMain">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySupDocsMain">
<?php
if (isset($team) && count($team) > 0){
    foreach ($team as $mem) {

        $role = "Collaborator";

        if ($mem['is_case_admin']) {
            $role = "Lead";
        }
?>
                                <tr>
                                    <td><img src="<?php echo base_url('img/user')."/".$mem['image']; ?>" style="width:24px;height:24px;"> <?php echo $mem['name']; ?></td>
                                    <td><?php echo $mem['title']; ?></td>
                                    <td><?php echo $role;?></td>
                                </tr>
<?php
    }
}else{
?>
                                <tr>
                                    <td colspan="3">No Team</td>
                                </tr>
<?php
}
?>
                            </tbody>
                        </table>
                        <div class="panel-footer" style="text-align:right;" id="memCount">
                            <?php echo $teamCount; ?> Member(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="modal_container"></div>
    <script src="https://cdn.quilljs.com/1.1.5/quill.js"></script>