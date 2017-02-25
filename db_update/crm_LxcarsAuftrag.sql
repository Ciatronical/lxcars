 var unit;
     var artNrZaehler = 0;
     var service = true;
-    var instruction = false;
+    var isInstruction = false;
     var steuerSatz;
     var newOrdNr = $.urlParam( 'id' );

 @@ -112,7 +112,7 @@ namespace('kivi', function(k){
             *if part exist
             ***************************************************/
             insertRow( rsp.instruction );
-            alert( 'If Instruction: ' + rsp.instruction );
+            //alert( 'If Instruction: ' + rsp.instruction );

             var sellprice = parseFloat(rsp.sellprice).toFixed(2);
             //console.log(sellprice);
 @@ -201,8 +201,10 @@ namespace('kivi', function(k){
             url: 'ajax/order.php',
             data: { action: "insertRow", data: posObject },
             type: "POST",
-            success: function(result){
-                $('.newOrderPos').children('.posID').text( result );
+            success: function( result ){
+                alert( 'Result' + result );
+                $( '.newOrderPos' ).children( '.posID' ).val( result );
+                alert( 'insertRow() newOrderPos: ' + $( '.newOrderPos' ).children( '.posID' ).val() );
                     //$('#pos__' + ( i +  1 ) + '__elem__9').text(result);
             },
             error:   function(){
 @@ -286,10 +288,12 @@ namespace('kivi', function(k){
         })

         $( '.rmv' ).click( function (){
-            var posToDel = $(this).parent().children('.posID').text();
+            //var posToDel = $(this).parent().children('.posID').text();
+                //alert( $( this ).parent().hasClass( 'instruction' ) );
                 $.ajax({
                     url: 'ajax/order.php',
-                    data: { action: "delPosition", data: posToDel },
+                    data: { action: "delPosition", data: { 'posToDel': $(this).parent().children( '.posID' ).text(), 'instruction':  $( this ).parent().hasClass( 'instruction' ) } },
+                    dataType: 'text',//important, when before set as JSON
                     type: "POST",
                     success: function(){
                         //alert('Gesendet');
 @@ -364,7 +368,6 @@ namespace('kivi', function(k){

                         $.ajax({
                             url: 'ajax/order.php',
-                            //data: { action: "updatePositions", data: JSON.stringify(updateDataJSON)},
                             data: { action: "updateOrder", data: updateDataJSON },
                             type: "POST",
                                 success: function(){
 @@ -411,6 +414,7 @@ namespace('kivi', function(k){
         //clearTimeout( timer );
         //timer = setTimeout( function(){   //calls click event after a certain time
             //alert( 'updatePosions(()' );
+            alert( 'updatePositions() pos_id: ' + $( this ).children( '.posID' ).val() );
             var updateDataJSON = new Array;
             $( 'ul#sortable > li' ).each( function(){
                 updateDataJSON.push({
 @@ -425,13 +429,14 @@ namespace('kivi', function(k){
                     "pos_total": $( this ).children( '.total' ).val().replace(',', '.'),
                     "pos_emp": $( this ).children( '.mechanics' ).val(),
                     "pos_status": $( this ).children( '.status' ).val(),
-                    "pos_id": $( this ).children( '.posID' ).text(),
+                    "pos_id": $( this ).children( '.posID' ).val(),
                     "partID": $( this ).children( '.partID' ).text(),
                     "pos_instruction": $( this ).hasClass( 'instruction' )
                 });
             })
             updateDataJSON.pop();
             //console.log(updateDataJSON);
+            alert( 'updatePostins() posID: ' + $( this ).children( '.posID' ).text() );
             $.ajax({
                 url: 'ajax/order.php',
                 data: { action: "updatePositions", data: updateDataJSON },
 @@ -654,7 +659,8 @@ namespace('kivi', function(k){
                             text: 'Artikel anlegen',
                             id: 'insertArtikel',
                             click: function (){
-                                insertRow( $( '#instructionCheckbox' ).is( ":checked" ) );
+                                isInstruction = $( '#instructionCheckbox' ).is( ":checked" );
+                                insertRow( isInstruction );
                                 var artObject = {};
                                 artObject['part'] = $('#txtArtAnlArtikelNr').val();
                                 artObject['description'] = $('#txtArtAnlBeschreibung').val();
 @@ -674,21 +680,24 @@ namespace('kivi', function(k){
                                         $( '#add_item_parts_id_name' ).val( artObject['description'] );
                                         $( '[id$=elem__4]:last' ).val( artObject['quantity'].replace( '.', ',' ) );
                                         $( '.orderPos' ).children( 'img' ).css({ 'visibility' : 'visible' }); //show del-image and move-image
+                                        //alert( 'test' + isInstruction ) ;
+                                        if( isInstruction ) $( '.newOrderPos' ).css({ 'background-color': 'blue'  }).addClass( 'instruction' );
                                         $( '.newOrderPos' ).children( '.unity' ).val( artObject['unit'] );
                                         $( '.newOrderPos' ).children( '.price' ).val( ( artObject['sellprice'] ).replace( '.', ',') );
                                         $( '.newOrderPos' ).children( '.discount' ).val( '0' );
                                         $( '.newOrderPos' ).children( '.partID' ).text( data );
                                         $( '.newOrderPos' ).children( '.total' ).val( ( artObject['quantity'] * artObject['sellprice'] ).toFixed( 2 ).replace( '.', ',' ) );
                                         $( '.newOrderPos' ).children().children( '.description' ).addClass( 'descrNewPos' );
                                         $( '.orderPos' ).removeClass( 'oP' );
-                                        $( '.newOrderPos' ).clone().insertBefore( '.newOrderPos' ).removeClass( 'newOrderPos' ).addClass( 'orderPos oP' );
+                                        $( '.newOrderPos' ).clone().insertBefore( '.newOrderPos' ).removeClass( 'newOrderPos' ).addClass( 'orderPos oP' )
                                         $( '[id$=elem__4]:last' ).val( '' ); //cloned quantity
                                         $( '[id$=elem__7]:last' ).val( '0'  );
                                         $( '<input name="pos_description" type="text" class="ui-widget-content ui-corner-all description oPdescription elem">' ).insertAfter( $( '.oP' ).children( '.itemNr' ) );
                                         $( '<input name="pos_unit" type="text" class="ui-widget-content ui-corner-all unity oPunity elem" autocomplete="off">' ).insertAfter( $( '.oP' ).children( '.description' ) );
                                         //oPunity
                                         $( '.oP' ).children( '.oPdescription' ).val( artObject['description'] );
                                         $( '.oP' ).children( '.oPunity' ).val( artObject['unit'] );
+                                        $( '.newOrderPos' ).removeClass( 'instruction' ).css({ 'background-color': '' });
                                         newPosition();
                                         saveLastArticleNumber();
                                         newOrderTotalPrice();
