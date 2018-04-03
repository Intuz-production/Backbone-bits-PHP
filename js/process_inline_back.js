//var url = 'http://localhost/App-tools/PHP/cladmin/';

function update(id,table_name){
     //alert(id); 
     $('#noty_topCenter_layout_container').remove();
     var data = $('#'+id).serializefiles();
     data.append("table_name", table_name);

    
$.ajax({
            type: "POST",
            url: "process-update.php",
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
//            beforeSend: function() {
//                btn.button('loading');
//
//            },
            success: function(data) {
                //alert(data);
 if(data['output']=='S'){
                    message(data['msg'],'success');
                }else if(data['output']=='F'){
                    message(data['msg'],'error');
                }


            }
        });
        
        
}

function add(id,table_name){
     //alert(id); 
     //$('#noty_topCenter_layout_container').remove();
     var data = $('#'+id).serializefiles();
     data.append("table_name", table_name);

    
$.ajax({
            type: "POST",
            url: "process-add.php",
            data: data,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
//            beforeSend: function() {
//                btn.button('loading');
//
//            },
            success: function(data) {
                //alert(data);
                if(table_name=='tblmember'){
                    search('','tblmember',['fname','lname','email','company'],data['limitstart'],data['limit']);
                   
                
                }
 if(data['output']=='S'){
                    message(data['msg'],'success');
                }else if(data['output']=='F'){
                    message(data['msg'],'error');
                }


            }
        });
        
        
}

function search(keyword,tablename,columnName,limitstart,limit,noempty,type){

    if(noempty!='noempty'){
   $('.classtablenew tbody').empty();
   $('#navview').empty();
   
    }
    $.ajax({
            type: "POST",
            url: "process-search.php",
            async:false,
            //dataType: "json",
            data: {keyword:keyword,tablename:tablename,columnName:columnName,limitstart:limitstart,limit:limit,type:type}, 
            success: function(data) {
              
              if(tablename=='tblmember'){
                var dat = JSON.parse(data);           
                $('#navview').empty();          
                $('.classtablenew tbody').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
              }
              else if(tablename=='tbltransactions'){
                  //alert(data);
                var dat = JSON.parse(data);           
                
                $('#navview').empty();          
                $('.classtablenew tbody').append(dat['htmld']);
                $('#navview').append(dat['htmld2']);
              }
                
            }
        });
}

function delete_agent(id) {
                     $.prompt("", {
                         title: "Are you sure you want to delete this user?",
                         buttons: {"Yes, I'm Ready": true, "No, Lets Wait": false},
                         submit: function(e, v, m, f) {
                             // use e.preventDefault() to prevent closing when needed or return false. 
                             // e.preventDefault(); 

                             //console.log("Value clicked was: "+ v);
                            
                             if (v == false) {
                                 //e.preventDefault(); 
                             } else {

                                 var data = "ajax=1&action=delete_user&intid=" + id;
                                 //alert(data); return false;
                                 request = $.ajax({
                                     type: "POST",
                                     url: "ajax.php",
                                     data: data,
                                     dataType: 'json',
                                     cache: false,
                                     beforeSend: function() {
                                         //$('.'+status).text('Saving..');
//
//                                         $("#ovelays").show();
//                                         $('#loading-image').show();
//                                         $('.preload-bg').show();
                                     },
                                     success: function(data) {
                                 //alert(data);
//                                         $("#ovelays").hide();
//                                         $('#loading-image').hide();
//                                         $('.preload-bg').hide();

                                         if (data['output'] == 'S') {
                                             $('#editdisplay_'+id).remove();
                                             message(data['msg'],'success');
               
                                              //location.href = '<?php echo $url.$get;?>' ;
                                         }
                                         else if (data['output'] == 'F') {
                                             message(data['msg'], 'error');
                                         }


                                     }
                                 });

                             }
                         }
                     });
                 }


function spliturl_paging(url,columns,type){
                var spliturl = url.split('&');
                            //alert(spliturl);
                            var limitstart = spliturl[0].split('=');
                            //alert(limitstart[0]);
                            var keyword = spliturl[1].split('=');
                            
                            var tablename = spliturl[2].split('=');
                            
                            var limit = spliturl[3].split('=');
                            
                            search(keyword[1],tablename[1],columns,limitstart[1],limit[1],'noempty',type);
           }