
	<div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#" class="edtcancel">Cases</a></li>
                <li class="active">New Case</li>
            </ol>
        </div>
        <div class="col-xs-6">
            <h2 class="top">New Case</h2>
        </div>
        <div class="col-xs-6" style="text-align:right;">
            <div class="leftcommand" role="nav">
                 <button type="button" id="btnCancel" class="btn btn-default edtcancel"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Cancel</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <p>Here you can create a new case. Some of the fields may be pre-populated based upon your existing selections.</p>
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Lead Sheet</div>
                <div class="panel-body" id="dvLeadSheet">
                    0 Lead Entries
                </div>
                <table class="table table-striped" id="tableLeadSheet">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Assinged On</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tblLeadSheet">
                        
                    </tbody>
                </table>
                <div class="panel-footer" style="text-align:right;">
                    <button type="submit" id="btnAddEntry" class="btn btn-success">Add Entry</button>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Supporting Documents</div>
                <div class="panel-body" id="dvSupDocs">
                    0 Document(s)
                </div>
                <table class="table table-striped" id="tableSupDocs">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbodySupDocs">
                        
                    </tbody>
                </table>
                <div class="panel-footer" style="text-align:right;">
                    <button type="submit" id="btnImportFile" class="btn btn-success">Import File</button>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-8">
            <form class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-heading">Case Details</div>
                    <div class="panel-body">
                        <p>Enter the details of your case. When finished press 'Save Case' below.</p>
                        <div id="altMsgMain" class="alert alert-danger" role="alert"></div>
                    </div>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="form-group" style="margin-bottom:0;">
                            <label for="name" class="col-sm-2 control-label">Case Name</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="cname" id="cname" value="" placeholder="Case Name" required>
                                    <span id="cnameres" class="input-group-addon"></span>
                                </div>
                            </div>
                        </div>
                        </div>
                        <button type="button" id="btnClientInfo" class="list-group-item list-group-item-plain selform"><span id="udClientInfo" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Client Information <span id="viewClientInfo" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlClientInfo">
                            <button type="button" id="clClientInfo" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            
                            <div style="margin-top:10px;margin-bottom:10px;">

                                
<?php
if (isset($clients) && count($clients) > 0){
?>
                                <p>Select an existing client:</p>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <select id="selExistingClient" class="form-control">
                                            <option value="" data="">Select an Existing Client</option>
<?php
foreach ($clients as $clt) {
?>
                                            <option data="<?php echo $clt['id']; ?>" name="<?php echo $clt['name']; ?>" street="<?php echo $clt['street']; ?>" city="<?php echo $clt['city']; ?>" state="<?php echo $clt['state']; ?>" zip="<?php echo $clt['zip']; ?>"><?php echo $clt['name']; ?></option>
<?php
}
?>
                                        </select>
                                    </div>
                                </div>
                                <p>Or Enter new client data below:</p>
<?php
} else {
?>
                                <p>Enter new client data below:</p>
<?php
}
?>
                                    <div class="form-group">
                                        <label for="company" class="col-sm-2 control-label">Organization</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="company" id="company" value="" placeholder="Org Name" required>
                                                <span id="cmpres" class="input-group-addon"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="street" class="col-sm-2 control-label">Street</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="street" id="street" value="" placeholder="Street Address" required>
                                                <span id="streetres" class="input-group-addon"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="city" class="col-sm-2 control-label">City</label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="city" id="city" value="" placeholder="City" required>
                                                <span id="cityres" class="input-group-addon"></span>
                                            </div>
                                        </div>
                                        <label for="state" class="col-sm-2 control-label">State</label>
                                        <div class="col-sm-2">
                                            <input type="text" size="2" class="form-control" name="state" value="" id="state" placeholder="State" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="zip" class="col-sm-2 control-label">Zipcode</label>
                                        <div class="col-sm-6">
                                                <input type="text" size="5" class="form-control" name="zip" value="" id="zip" placeholder="5 digit Zipcode" required>
                                        </div>
                                    </div>

                            </div>
                        </div>
                        <button type="button" id="btnCaseAdmin" class="list-group-item list-group-item-plain selform"><span id="udCaseAdmin" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Case Administrator <span id="viewCaseAdmin" class="pull-right"><?php echo $name; ?></span></button>
                        <div class="list-group-item" id="pnlCaseAdmin">
                            <button type="button" id="clCaseAdmin" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <input type="hidden" id="hdCaseAdmin" value="">
                            <div style="margin-top:10px;margin-bottom:10px;">

                                <p>Select a Case Administrator.</p>
                <?php
                    $team_html = "";
                    $team_count = count($team);
                    if (isset($team) && $team_count > 0){
                        foreach ($team as $mem) {
                            $admin_text = "<strong>Collaborator</strong>";
                            $team_user_image = "";
                            if (strlen($mem['image']) > 0 && $mem['image'] !== 'NULL') {
                                $team_user_image = "<img src=\"".base_url('img/user')."/".$mem['image']."\" class=\"img-thumbnail\" style=\"width:28px;height:28px;;\">";
                            } else {
                                $team_user_image = "<img src=\"".base_url('img/user/profile.png')."\" class=\"img-thumbnail\" style=\"width:28px;height:28px;\">";
                            }
                            // select admin currently using app by default
                            $selected = "";
                            $disabled = "";
                            if ($user_id == $mem['id']) {
                                $selected = " checked";
                                $disabled = " disabled";
                            }
                            // only give option for admins
                            if ($mem['is_admin'] == 1 || $mem['is_admin'] == TRUE) {
                ?>
                                <div class="radio">
                                  <label>
                                    <input type="radio" class="rdoAdmin" name="rdoCaseAdmin" data="<?php echo $mem['name']; ?>" id="ca_<?php echo $mem['id']; ?>" value="<?php echo md5($mem['id']); ?>"<?php echo $selected; ?>>
                                    <?php echo $team_user_image; ?> <?php echo $mem['name']; ?>
                                  </label>
                                </div>
                <?php
                            }

                            // build team html here so as to reduce iterations
                            $team_html = $team_html."<div class=\"checkbox\">";
                            $team_html = $team_html."<label>";
                            $team_html = $team_html."<input type=\"checkbox\" class=\"chkteam\" id=\"tm_".$mem['id']."\" value=\"".md5($mem['id'])."\"".$selected.">";
                            $team_html = $team_html." ".$team_user_image." ".$mem['name'];
                            $team_html = $team_html."</label>";
                            $team_html = $team_html."</div>";

                        }
                    }else{
                ?>
                                <div class="radio">
                                  <label>
                                    <input type="radio" class="rdoAdmin" name="rdoCaseAdmin" data="<?php echo $mem['name']; ?>" id="ca_<?php echo $user_id; ?>" value="<?php echo md5($user_id); ?>" checked>
                                    <?php echo $user_image; ?> <?php echo $name; ?>
                                  </label>
                                </div>
                <?php
                            $team_html = $team_html."<div class=\"checkbox\">";
                            $team_html = $team_html."<label>";
                            $team_html = $team_html."<input type=\"checkbox\" class=\"chkteam\" id=\"tm_".$user_id."\" value=\"".md5($user_id)."\" checked>";
                            $team_html = $team_html." ".$user_image." ".$name;
                            $team_html = $team_html."</label>";
                            $team_html = $team_html."</div>";

                    }
                ?>
                            </div>

                        </div>
                        <button type="button" id="btnTeam" class="list-group-item list-group-item-plain selform"><span id="udTeam" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Team Members <span id="viewTeamCount" class="badge pull-right">1</span></button>
                        <div class="list-group-item" id="pnlTeam">
                            <button type="button" id="clTeam" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select Team Members <span id="msgTeam" class="text-danger pull-right"></span></p>
                                <?php echo $team_html; ?>
                            </div>
                        </div>
                        <button type="button" id="btnCreation" class="list-group-item list-group-item-plain selform"><span id="udCreation" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Creation Date <span id="viewCreationDate" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlCreation">
                            <button type="button" id="clCreation" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select Creation Date</p>
                                <div class="form-group">
                                    <label for="creation" class="col-sm-2 control-label">Creation</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <input type="date" class="form-control" name="creation" id="creation" max="<?php echo date('Y-m-d'); ?>" value="" data="<?php echo date('m/d/Y'); ?>" required>
                                            <span id="creationres" class="input-group-addon">MM/DD/YYYY</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    $attorney_html = "";
                    $cpa_html = "";
                    $leagency_html = "";
                    $distatt_html = "";
                    
                    if (isset($supporting) && count($supporting) > 0){
                        foreach ($supporting as $sup) {

                            $id = $sup['id'];
                            $name = $sup['name'];
                            $title = $sup['title'];
                            $city = $sup['city'];
                            $state = $sup['state'];
                            
                            // only give option for admins
                            switch (strtolower($sup['profession'])) {
                                case 'attorney':
                                    $attorney_html = $attorney_html."<div class=\"radio\">";
                                    $attorney_html = $attorney_html."<label>";
                                    $attorney_html = $attorney_html."<input type=\"radio\" name=\"rdoAttorney\" id=\"at_".md5($id)."\" value=\"".md5($id)."\">";
                                    $attorney_html = $attorney_html."".$name." ".$title." (".$city.", ".$state.")";
                                    $attorney_html = $attorney_html."</label>";
                                    $attorney_html = $attorney_html."</div>";
                                    break;
                                case 'cpa':
                                    $cpa_html = $cpa_html."<div class=\"radio\">";
                                    $cpa_html = $cpa_html."<label>";
                                    $cpa_html = $cpa_html."<input type=\"radio\" name=\"rdoCPA\" id=\"cpa_".md5($id)."\" value=\"".md5($id)."\">";
                                    $cpa_html = $cpa_html."".$name." ".$title." (".$city.", ".$state.")";
                                    $cpa_html = $cpa_html."</label>";
                                    $cpa_html = $cpa_html."</div>";
                                    break;
                                case 'le agency':
                                    $leagency_html = $leagency_html."<div class=\"radio\">";
                                    $leagency_html = $leagency_html."<label>";
                                    $leagency_html = $leagency_html."<input type=\"radio\" name=\"rdoLeAgency\" id=\"lea_".md5($id)."\" value=\"".md5($id)."\">";
                                    $leagency_html = $leagency_html."".$name." ".$title." (".$city.", ".$state.")";
                                    $leagency_html = $leagency_html."</label>";
                                    $leagency_html = $leagency_html."</div>";
                                    break;
                                case 'district attorney':
                                    $distatt_html = $distatt_html."<div class=\"radio\">";
                                    $distatt_html = $distatt_html."<label>";
                                    $distatt_html = $distatt_html."<input type=\"radio\" name=\"rdoDistAttorney\" id=\"da_".md5($id)."\" value=\"".md5($id)."\">";
                                    $distatt_html = $distatt_html."".$name." ".$title." (".$city.", ".$state.")";
                                    $distatt_html = $distatt_html."</label>";
                                    $distatt_html = $distatt_html."</div>";
                                    break;
                            }
                        }
                    }

                    if (strlen($attorney_html) == 0) {
                        $attorney_html = "[No Attorney Found]";
                    }
                    
                    if (strlen($cpa_html) == 0) {
                        $cpa_html = "[No CPA Found]";
                    }
                    
                    if (strlen($leagency_html) == 0) {
                        $leagency_html = "[No Le Agency Found]";
                    }
                    
                    if (strlen($distatt_html) == 0) {
                        $distatt_html = "[No District Attorney Found]";
                    }
                    
                ?>
                        <button type="button" id="btnAttorney" class="list-group-item list-group-item-plain selform"><span id="udAttorney" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Attorney <span id="viewAttorney" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlAttorney">
                            <button type="button" id="clAttorney" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select Attorney</p>
                                <div id="selAttorney">
                                <?php echo $attorney_html; ?>
                                </div>
                                <div style="margin-top:10px;text-align:right;">
                                    <button type="button" class="btn btn-success" id="btnAddNewAttorney">Add New Attorney</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnCPA" class="list-group-item list-group-item-plain selform"><span id="udCPA" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> CPA <span id="viewCPA" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlCPA">
                            <button type="button" id="clCPA" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select CPA</p>
                                <div id="selCPA">
                                <?php echo $cpa_html; ?>
                                </div>
                                <div style="margin-top:10px;text-align:right;">
                                    <button type="button" class="btn btn-success" id="btnAddNewCPA">Add New CPA</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnLeAgency" class="list-group-item list-group-item-plain selform"><span id="udLeAgency" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> LE Agency <span id="viewLeAgency" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlLeAgency">
                            <button type="button" id="clLeAgency" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select LE Agency</p>
                                <div id="selLeAgency">
                                <?php echo $leagency_html; ?>
                                </div>
                                <div style="margin-top:10px;text-align:right;">
                                    <button type="button" class="btn btn-success" id="btnAddLeAgency">Add New LE Agency</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnDistAttorney" class="list-group-item list-group-item-plain selform"><span id="udDistAttorney" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> District Attorney <span id="viewDistAttorney" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlDistAttorney">
                            <button type="button" id="clDistAttorney" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Select District Attorney</p>
                                <div id="selDistAttorney">
                                <?php echo $distatt_html; ?>
                                </div>
                                <div style="margin-top:10px;text-align:right;">
                                    <button type="button" class="btn btn-success" id="btnAddDistAttorney">Add New District Attorney</button>
                                </div>
                            </div>
                        </div>
                        <!--
                        <button type="button" id="btnPredication" class="list-group-item list-group-item-plain selform"><span id="udPredication" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Predication <span id="viewPredication" class="pull-right"></span></button>
                        <div class="list-group-item" id="pnlPredication">
                            <button type="button" id="clPredication" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div style="margin-top:10px;margin-bottom:10px;">
                                <p>Enter a Predication Narrative.</p>
                                <textarea class="form-control" id="predication" rows="5"></textarea>
                            </div>
                            <span id="predres"></span>
                        </div>
                        -->
                    </div>
                    <div class="panel-footer" style="text-align:right;">
                        <button type="button" id="btnSaveCase" class="btn btn-success">Save Case</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="modal_container"></div>