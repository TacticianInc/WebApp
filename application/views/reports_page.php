
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb" id="brdMain">
                <li><a href="<?php echo site_url('mycases'); ?>">Cases</a></li>
                <li><a href="<?php echo site_url('mycases/view_case')."/".$case_id;?>"><?php echo $case_name; ?></a></li>
                <li class="active">Reports</li>
            </ol>
        </div>
    </div>
	<div class="row">
        <div class="col-xs-6">
            <h2 class="top">Reports</h2>
        </div>
        <div class="col-xs-6" style="text-align:right;">
            <div class="leftcommand" role="nav">
<?php
if (isset($is_admin) && intval($is_admin) === 1) {
?>
                <a href="#" id="btnAddNewReport" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New Report</a>
<?php
}
?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="pnlMain" class="pnl row">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Created</th>
                            <th>Name</th>
                            <th>Author</th>
                            <th>Team Shared</th>
                            <th>Redacted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tblReports">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="modal_container"></div>