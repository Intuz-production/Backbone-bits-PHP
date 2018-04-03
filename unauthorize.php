<?php 
/*********************************************************************
    The MIT License (MIT)

    Copyright (c) 2018 Intuz

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*********************************************************************/ 

require("config/configuration.php");
include(INC . "header.php"); 
include INC . "left_sidebar.php";

?><!-- Right side column. Contains the navbar and content of the page -->
<aside class="right-side">  
    <!-- Main content -->
    <section class="content">
        <div class="row index_page">
            <div class="col-xs-12 col-md-12">
                <div class="blank_page">
        	       <h1>Opps ! You Are not Authorized to access this Page</h1>
                    <a class="go_desh" href="dashboard">Go To Dashboard</a>
                </div>
            </div>
        </div>  
    </section>
    <!-- /.content --> 
</aside>
<!-- /.right-side -->

<?php include(INC."footer.php"); ?>
