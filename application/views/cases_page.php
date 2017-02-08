
	<div class="row">
        <div class="col-xs-6">
            <h2 class="top">Cases</h2>
        </div>
        <div class="col-xs-6" style="text-align:right;">
<?php
// only show options if is admin
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
?>
            <div class="leftcommand" role="nav">
                <a href="<?php echo site_url('mycases/new_case'); ?>" class="btn btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New Case</a>
            </div>
<?php
}
?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <ul class="nav nav-tabs">
                <li class="" id="tabAll" role="presentation"><a href="#all">All <span id="allCt" class="badge">0</span></a></li>
                <li class="blue" id="tabOpen" role="presentation"><a href="#open">Open <span id="openCt" class="badge">0</span></a></li>
                <li class="green" id="tabClosed" role="presentation"><a href="#closed">Closed <span id="closedCt" class="badge">0</span></a></li>
            </ul>
            <div id="pnlMain" class="pnl row">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Case</th>
                            <th>Engagement Date</th>
                            <th>Last Activity</th>
                        </tr>
                    </thead>
                    <tbody id="tblCases">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="modal_container"></div>