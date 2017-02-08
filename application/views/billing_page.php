
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb" id="brdMain">
                <li><a href="<?php echo site_url('mycases'); ?>">Cases</a></li>
                <li><a href="<?php echo site_url('mycases/view_case')."/".$case_id;?>"><?php echo $case_name; ?></a></li>
                <li class="active">Billing</li>
            </ol>
        </div>
    </div>
	<div class="row">
        <div class="col-xs-6">
            <h2 class="top">Billing</h2>
        </div>
        <div class="col-xs-6" style="text-align:right;">
            <div class="leftcommand" role="nav">
                <a href="#" id="btnAddNewExpense" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add New Expense</a>
<?php
// only show options if is admin
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
?>                
                <a href="#" id="btnGenCSV" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span> Export to CSV</a>
                <a href="#" id="btnSendInvoice" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Send Invoice</a>
<?php
}
?>            
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="pnlControl" style="margin-bottom:10px;background-color:#fff;padding:10px;">
                
<?php
// only show options if is admin
if (isset($is_admin) && ($is_admin == 1 || $is_admin == TRUE)){
?>
                    Agent: <select id="selAgent"><?php echo $agents; ?></select>&nbsp;
<?php
}else{
?>
                    Agent: <strong><?php echo $user_name; ?></stron>&nbsp;
<?php
}
?>
                
                    Month: <select id="selMonth"><?php echo $months; ?></select> Year: <select id="selYear"><?php echo $years; ?></select>
                
            </div>
            <div id="pnlMain" class="pnl row">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Case</th>
                            <th>Engagement</th>
                            <th>Activity / Expense</th>
                            <th>Comments</th>
                            <th>Time / Amount</th>
                            <th>Rate</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tblBilling">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="modal_container"></div>