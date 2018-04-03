<?php /* * *******************************************************************
  The MIT License (MIT)

  Copyright (c) 2018 Intuz

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * ******************************************************************* */ ?>
<?php
require("config/configuration.php");

if (!$gnrl->checkMemLogin()) {
    $gnrl->redirectTo("login?msg=logfirst");
}
$member_id = $_SESSION['custid'];

extract($_GET);

$ge = $_POST;
$str = '';
if (is_array($ge) && !empty($ge)) {
    $str = http_build_query($ge, '', '&');
}

extract($_POST);

include(INC . "header.php");
include INC . "left_sidebar.php";

$url_add = $url;
$url_add .= "?script=" . $_GET['script'];

if ($_GET['script'] == 'edit' && $_GET['id'] != '') {
    $url_add .= "&id=" . $_GET['id'];
}

//SET LIMIT FOR PAGING
if (isset($_REQUEST['pageno'])) {
    $limit = $_REQUEST['pageno'];
} else {
    $limit = $gnrl->getMember_Settings('sRecordperpage', $member_id);
}

$limit = 15;
$form = 'frm';

if (isset($_REQUEST['limitstart'])) {
    $limitstart = $_REQUEST['limitstart'];
} else {
    $limitstart = 0;
}

if (isset($_REQUEST['button4']) || $_REQUEST['button4'] == "Search") {
    $limit = $gnrl->getMember_Settings('sRecordperpage', $member_id);
    $limitstart = 0;
}
if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
    $keyword = mysql_real_escape_string(trim($_REQUEST['keyword']));
    $wh .= " AND (video_name like '%$keyword%')";
}

$ssql = "SELECT agent_id, DATE(login_time) AS startdate, TIME_TO_SEC(TIMEDIFF(logout_time,login_time)) AS timediff FROM tbl_checkin_checkout where 1 AND session_id = 'N' AND parent_id = '" . $_SESSION['custid'] . "' GROUP BY startdate,agent_id ORDER BY startdate DESC $wh";

$sortby = ($_REQUEST['sb'] == '') ? 'intid' : $_REQUEST['sb'];
$sorttype = ($_REQUEST['st'] == '0') ? 'ASC' : 'DESC';

$nototal = $dclass->numRows($ssql);

$sqltepm = $ssql . " ORDER BY $sortby $sorttype LIMIT $limitstart,$limit";

$restepm = $dclass->query($sqltepm);
?><!-- Right side column. Contains the navbar and content of the page -->

<aside class="right-side">
    <!-- Content Header (Page header) -->
    <div class="col-xs-12 col-md-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="file-excel"><a href="exportexcel" target="_blank"><i class="fa fa-file-text"></i> Export to Excel</a></div>
            <div class="box-body table-responsive access-log_table" id="promote-table_wrapper">
                <table id="example1" class="table table-bordered table-striped classtablenew agents-tables activet_logs">
                    <thead>
                        <tr>
                            <th class="name-role">User</th>
                            <th class="username_box">Date</th>
                            <th class="username_box">IP Address</th>
                            <th class="username_box">Session</th>
                            <th class="email_box">Description</th>
                            <th class="edit_box">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody id="showdata"></tbody>
                </table>
                <div class='navigation' id="navview"></div>
            </div>
        </div>
        <!-- /.box-body --> 
    </div>
</aside>
<!-- /.right-side --> 
</div>
<!-- ./wrapper -->
<?php include(INC . "footer.php"); ?>

<script src="js/setting-hover.js"></script>
<script type="text/javascript" src="js/process.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        if (Modernizr.touch) {
            // show the close overlay button
            $(".close-overlay").removeClass("hidden");

            // handle the adding of hover class when clicked
            $(".img").click(function (e) {
                if (!$(this).hasClass("hover")) {
                    $(this).addClass("hover");
                }
            });

            // handle the closing of the overlay
            $(".close-overlay").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                if ($(this).closest(".img").hasClass("hover")) {
                    $(this).closest(".img").removeClass("hover");
                }
            });
        } else {
            // handle the mouseenter functionality
            $(".img").mouseenter(function () {
                $(this).addClass("hover");
            })
                    // handle the mouseleave functionality
                    .mouseleave(function () {
                        $(this).removeClass("hover");
                    });
        }
    });
</script> 

<script type="text/javascript">
    $(function () {
        var ajaxRunning = false;
        $("body").ajaxStart(function () {
            ajaxRunning = true;
        }).ajaxStop(function () {
            ajaxRunning = false;
        });

        document.onscroll = function () {
            $.ias({
                container: '.classtablenew',
                scrollContainer: $('.classtablenew'),
                item: '.item',
                pagination: '#promote-table_wrapper .navigation',
                next: '.next-posts a',
                loader: '<div class="view_more_loader"><img src="img/ajax-loader.gif"/></div>',
                triggerPageThreshold: 0,
                trigger: 'View More',
                history: false,
                onRenderComplete: function () {
                    remove_loader();
                },
                beforePageChange: function (curScrOffset, urlnext) {
                    spliturl_paging(urlnext, ['agent_id'], 'bef');
                    remove_loader();
                },
                onPageChange: function (pageNum, pageUrl, scrollOffset) {
                    spliturl_paging(pageUrl, ['agent_id'], 'onp');
                    remove_loader();
                }
            });
        }
    });
    search('', 'tbl_access_log', ['agent_id'],<?php echo $limitstart ?>,<?php echo $limit ?>);
</script>
<script type="text/javascript" src="js/plugins/ias/jquery-ias.js"></script>
</body>
</html>