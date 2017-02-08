
	<div class="row">
        <h1 class="top">Account: <small><?php echo $name; ?></small></h1>

        <ul class="nav nav-tabs">
            <li id="tabProfile" role="presentation"><a href="#profile">Profile</a></li>
<?php
// only show company edit if is admin
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
?>
            <li id="tabCollab" role="presentation"><a href="#collaborators">Collaborators</a></li>
            <li id="tabAgency" role="presentation"><a href="#agency">Agency</a></li>
<?php
}
?>
        </ul>

        <div id="pnlMain" class="pnl row">

            <div id="pnlProfile">

                <div class="col-xs-12" style="margin-top:20px;margin-bottom:20px;">
                    <p>Make changes to your personal preferences.</p>
                </div>
                <div class="col-xs-12 col-md-6">
                    <!-- Profile -->
                    <div class="panel panel-default">
                        <div class="panel-heading">Profile</div>
                        <div class="panel-body">
                            <div class="col-xs-4 col-md-2">
                                <div class="image_box_holder" id="dvProfImage">
                                    <div class="image_box">
                                        <?php echo $image; ?>
                                    </div>
                                    <a href="#" id="aChangeProfImg">Change</a>
                                </div>
                            </div>
                            <input type="hidden" value="<?php echo $name; ?>" id="hdPName">
                            <input type="hidden" value="<?php echo $title; ?>" id="hdPTitle">
                            <div class="col-xs-8 col-md-10 panel_content" id="edtProfile">
                                <p id="pr_name"><?php echo $name; ?></p>
                                <p id="pr_title"><?php echo $title; ?></p>
        <?php
        // only show company edit if is admin
        if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
        ?>
                                <strong>Administrator</strong>
        <?php
        }
        ?>
                            </div>
                        </div>
                        <div class="panel-footer" style="text-align:right;"><button class="btn btn-default" id="btnEditProfile">Edit</button></div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <!-- Settings -->
                    <div class="panel panel-default">
                        <input type="hidden" value="<?php echo $email; ?>" id="hdAEmail">
                        <div class="panel-heading">Account</div>
                        <div class="panel-body" id="edtAccount">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-10">
                                        <p id="aemail" class="form-control-static"><?php echo $email; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Password</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static">*****</p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="panel-footer" style="text-align:right;"><button class="btn btn-default" id="btnEditAccount">Edit</button></div>
                    </div>
                </div>
            </div>

<?php
// only show company edit if is admin
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
?>
            <div id="pnlCollab">
                <div class="col-xs-12" style="margin-top:20px;margin-bottom:20px;">
                    <p>Invite and manage your collaborators.</p>
                </div>
                <div class="col-xs-12">

                    <!-- Team -->
                    <div class="panel panel-default">
                        <div class="panel-heading">Collaborators</div>
                        <div class="panel-body">
                            <h3 style="margin-bottom:0;">Registered</h3>
                            <div class="table-responsive">
                            <table class="table table-hover" style="margin-top:20px;margin-bottom:20px;">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Profile</th>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
        <?php
        $team_count = count($team);
        if (isset($team) && count($team) > 0){
            foreach ($team as $mem) {
                $admin_text = "<strong>Collaborator</strong>";
                $role = 0;
                if ($mem['is_admin'] == 1 || $mem['is_admin'] == TRUE) {
                    $role = 1;
                    $admin_text = "<strong>Admin</strong>";
                }
                $team_user_image = "";
                if (strlen($mem['image']) > 0 && $mem['image'] !== 'NULL') {
                    $team_user_image = "<img src=\"".base_url('img/user')."/".$mem['image']."\" class=\"img-thumbnail\" style=\"width:28px;height:28px;;\">";
                } else {
                    $team_user_image = "<img src=\"".base_url('img/user/profile.png')."\" class=\"img-thumbnail\" style=\"width:28px;height:28px;\">";
                }
                $is_me = FALSE;
                if ($user_id == $mem['id']) {
                    $is_me = TRUE;
                }
        ?>
                                    <tr id="row_<?php echo $mem['id']; ?>" title="<?php echo $mem['name']; ?>">
        <?php
        // only show company edit if is admin
        if ($is_me){
        ?>
                                        <td>&nbsp;</td>
        <?php
        }else{
        ?>
                                        <td><button class="btn btn-danger btn-sm usr_remove" data="<?php echo $mem['id'].",".$mem['name']; ?>" title="Remove User"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>&nbsp;<button class="btn btn-info btn-sm usr_edit" data="<?php echo $mem['id'].",".$mem['name'].",".$role; ?>" title="Change Role"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></button></td>
        <?php
        }
        ?>
                                        <td><?php echo $team_user_image; ?></td>
                                        <td><?php echo $mem['name']; ?></td>
                                        <td><?php echo $mem['title']; ?></td>
                                        <td id="role_<?php echo $mem['id']; ?>"><?php echo $admin_text; ?></td>
                                    </tr>
        <?php
            }
        }
        ?>
                                </tbody>
                            </table>
                            </div>
                            <hr>
                            <h3 style="margin-bottom:0;">Invitations</h3>
                            <div class="table-responsive">
                            <table class="table table-hover" id="tblInvites" style="margin-top:20px;margin-bottom:20px;">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Number Sent</th>
                                        <th>Last Sent On</th>
                                    </tr>
                                </thead>
                                <tbody id="tbInvitedBody">
        <?php
        $invite_count = count($invites);
        if (isset($invites) && count($invites) > 0){
            foreach ($invites as $inv) {
        ?>
                                    <tr title="<?php echo $inv['name']; ?>">
                                        <td><button class="btn btn-primary btn-sm reinvite" data="<?php echo $inv['name'].",".$inv['email']; ?>" title="Resend"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button></td>
                                        <td><?php echo $inv['name']; ?></td>
                                        <td><?php echo $inv['email']; ?></td>
                                        <td class="tdcount"><?php echo $inv['num_tries']; ?></td>
                                        <td class="tddate"><?php echo date('m/d/Y', strtotime($inv['last_sent'])); ?></td>
                                    </tr>
        <?php
            }
        }
        ?>
                                </tbody>
                            </table>
                            </div>
                            <button class="btn btn-success" id="btnInvite">Invite Collaborators</button>
                        </div>
                        <div class="panel-footer" style="text-align:right;"><?php echo $team_count; ?> Collaborator(s)</div>
                    </div>
                </div>
<?php
//}
?>
            </div>

            <div id="pnlAgency">
                <div class="col-xs-12 col-md-12" style="margin-top:20px;margin-bottom:20px;">
                    <p>Modify your agency settings.</p>
                </div>
                <div class="col-xs-12 col-md-6">
                    <!-- Payment -->
                    <div class="panel panel-default">
                        <input type="hidden" value="<?php echo $plan; ?>" id="hdPPlan">
                        <div class="panel-heading">Payment</div>
                        <div class="panel-body" id="edtPayment">
                            <form class="form-horizontal">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Plan</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"><?php echo $plan; ?></p>
                                    </div>
                                </div>
        <?php
            if (isset($cc_type) && $cc_type > 0) {
        ?>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Payment</label>
                                    <div class="col-sm-10">
                                        <div class="col-sm-2">
        <?php
            switch ($cc_type) {
                case 0:
                case 1:
                    echo "<span class=\"cc visa\"></span>";
                    break;
                case 2:
                    echo "<span class=\"cc mc\"></span>";
                    break;
                case 3:
                    echo "<span class=\"cc disc\"></span>";
                    break;
                case 4:
                    echo "<span class=\"cc amex\"></span>";
                    break;
            }
        ?>
                                            
                                        </div>
                                        <div class="col-sm-10">
                                            <span class="ccnumber"> Ending in <?php echo $cc_last_four ?>&nbsp;Expires on:<?php echo $cc_exp_date; ?></span>
                                        </div>
                                    </div>
                                </div>
        <?php
            }
        ?>
                            </form>
                        </div>
                        <div class="panel-footer" style="text-align:right;"><button class="btn btn-default" id="btnEditPayment">Edit</button></div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <!-- Agency Settings -->
                    <div class="panel panel-default">
                        <div class="panel-heading">Agency</div>
                        <div class="panel-body">
                            <div class="image_box_holder" id="dvCompImage">
                                <div class="image_box_logo">
                                    <?php echo $company_image; ?>
                                </div>
                                <a href="#" id="aChangeCompImg">Change</a>
                                <input type="hidden" value="<?php echo $company_name; ?>" id="hdCompName">
                                <input type="hidden" value="<?php echo $street; ?>" id="hdStreet">
                                <input type="hidden" value="<?php echo $city; ?>" id="hdCity">
                                <input type="hidden" value="<?php echo $state; ?>" id="hdState">
                                <input type="hidden" value="<?php echo $zip; ?>" id="hdZip">
                            </div>
                            <div class="panel_content" id="edtCompany" style="margin-top:10px;">
                                <p id="ag_name"><?php echo $company_name; ?></p>
                                <p id="ag_street"><?php echo $street; ?></p>
                                <p id="ag_city_state_zip"><?php echo $city; ?>,<?php echo $state; ?>&nbsp;<?php echo $zip; ?></p>
                            </div>
                        </div>
                        <div class="panel-footer" style="text-align:right;"><button class="btn btn-default" id="btnEditCompany">Edit</button></div>
                    </div>
                </div>
            </div>
<?php
}
?>
        </div>
    </div>
    <div id="modal_container"></div>