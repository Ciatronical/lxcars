namespace('kivi', function(k){
  k.PartPicker = function($real, options) {
    // short circuit in case someone double inits us
    if ($real.data("part_picker"))
      return $real.data("part_picker");

    var KEY = {
      ESCAPE: 27,
      ENTER:  13,
      TAB:    9,
      LEFT:   37,
      RIGHT:  39,
      PAGE_UP: 33,
      PAGE_DOWN: 34,
    };
    var CLASSES = {
      PICKED:       'partpicker-picked',
      UNDEFINED:    'partpicker-undefined',
      FAT_SET_ITEM: 'partpicker_fat_set_item',
    }
    var o = $.extend({
      limit: 20,
      delay: 50,
      fat_set_item: $real.hasClass(CLASSES.FAT_SET_ITEM),
    }, options);
    var STATES = {
      PICKED:    CLASSES.PICKED,
      UNDEFINED: CLASSES.UNDEFINED
    }
    var real_id = $real.attr('id');
    var $dummy  = $('#' + real_id + '_name');
    var $type   = $('#' + real_id + '_type');
    var $unit   = $('#' + real_id + '_unit');
    var $convertible_unit = $('#' + real_id + '_convertible_unit');
    var state   = STATES.PICKED;
    var last_real = $real.val();
    var last_dummy = $dummy.val();
    var timer;
    var defaults_id;
    var unit;
    var artNrZaehler = 0;
    var service = true;
    var steuerSatz;
    var newOrdNr = $.urlParam( 'id' );

    function open_dialog () {
      k.popup_dialog({
        url: '../../controller.pl?action=Part/part_picker_search',
        data: $.extend({
          real_id: real_id,
        }, ajax_data($dummy.val())),
        id: 'part_selection',
        dialog: {
          title: k.t8('Part picker'),
          width: 800,
          height: 800,
        }
      });
      window.clearTimeout(timer);
      return true;
    }

    function ajax_data(term) {
      var data = {
        'filter.all:substr:multi::ilike': term,
        'filter.obsolete': 0,
        'filter.unit_obj.convertible_to': $convertible_unit && $convertible_unit.val() ? $convertible_unit.val() : '',
        no_paginate:  $('#no_paginate').prop('checked') ? 1 : 0,
        current:  $real.val(),
      };

      if ($type && $type.val())
        data['filter.type'] = $type.val().split(',');

      if ($unit && $unit.val())
        data['filter.unit'] = $unit.val().split(',');

      return data;
    }

    function set_item (item) {
      if (item.id) {
        $real.val(item.id);
        // autocomplete ui has name, use the value for ajax items, which contains displayable_name
        $dummy.val(item.name ? item.name : item.value);
      } else {
        $real.val('');
        $dummy.val('');
      }
      state = STATES.PICKED;
      last_real = $real.val();
      last_dummy = $dummy.val();
      last_unverified_dummy = $dummy.val();
      $real.trigger('change');

      if (o.fat_set_item && item.id) {
        $.ajax({
          url: '../../controller.pl?action=Part/show.json',
          data: { id: item.id },
          success: function(rsp) {
            $real.trigger('set_item:PartPicker', rsp);
            //console.log(rsp);

/***************************************************/
    /*******************************************/
        /***********************************/
            /***************************/

            /***************************************************
            *if part exist
            ***************************************************/

            var sellprice = parseFloat(rsp.sellprice).toFixed(2);
            //console.log(sellprice);

            $('.newOrderPos').children('.itemNr').val(rsp.partnumber);
            $('.newOrderPos').children().children('.description').val(rsp.description);

            $.ajax({
                url: 'ajax/order.php',
                data: { action: "getQty", data: rsp.description },
                type: "POST",
                dataType: 'text',
                success: function( qty ){
                    $( '.oP' ).children( '.number' ).val( qty.replace( '.', ',' ) );
                    var price = $( '.oP' ).children( '.price' ).val().replace( ',', '.' );
                    $( '.oP' ).children( '.total' ).val( ( ( qty * price ) ).toFixed(2).replace( '.', ',' ) );
                      newOrderTotalPrice();
                },
                error:   function( err, exception ){
                    alert('Error: ' + err.responseText );
                    //console.log(  err );
                    console.log( exception );
                }
            });

            if( rsp.description.match(/Hauptuntersuchung/g) ) {
                //alert(rsp.description);
                $.ajax({
                    url: 'ajax/order.php',
                    data: { action: "setHuAuDate", data: window.car },
                    type: "POST",
                    dataType: 'text',
                    error:   function( err, exception ){
                        alert('Error: ' + err.responseText );
                        //console.log(  err );
                        console.log( exception );
                    }
                });
            }

            $('.newOrderPos').children('.unity').val(rsp.unit);
            $('.newOrderPos').children('.price').val((sellprice).replace('.', ','));
            $('.newOrderPos').children('.discount').val(rsp.not_discountable);
            $('.newOrderPos').children('.partID').text(rsp.id);
            $('.newOrderPos').children().children('.description').addClass('descrNewPos');

            $('.orderPos').removeClass('oP');
            $('.newOrderPos').clone().insertBefore('.newOrderPos').removeClass('newOrderPos').addClass('orderPos oP');

            $('<input name="pos_description" type="text" class="ui-widget-content ui-corner-all description oPdescription elem">').insertAfter($('.oP').children('.itemNr'));
            $('<input name="pos_unit" type="text" class="ui-widget-content ui-corner-all unity oPunity elem" autocomplete="off">').insertAfter($('.oP').children('.description'));
            //oPunity

            $('.oP').children('.oPdescription').val(rsp.description);
            $('.oP').children('.oPunity').val(rsp.unit);
            //$('.op').children('.posID').text($('.newOrderPos').children('.posID').text());
            $( '.orderPos' ).children( 'img' ).css({ 'visibility' : 'visible' });

            newPosition();

          },
        });
      } else {
        $real.trigger('set_item:PartPicker', item);
      }
      annotate_state();
    }

    function newPosition() {
        //console.log( orderID );
        //alert( 'newPosition');

        $('.orderPos').children('.part_picker').remove();
        $('.orderPos').children('.nPunity').remove();

        /***************************************************
        *set values to empty string
        ***************************************************/

        $('.newOrderPos').children('.itemNr').val('');
        $('.newOrderPos').children().children('#add_item_parts_id_name').val('');
        $('.newOrderPos').children('.unity').val('');
        $('.newOrderPos').children('.price').val('');
        $('.newOrderPos').children('.discount').val('');
        $('.newOrderPos').children('.partID').text('');
        $('.newOrderPos').children('.posID').text('');

        zaehler();

        /***************************************************
        *add classes for positioning
        ***************************************************/

        $('.orderPos').children('.description').addClass('description2');
        $('.orderPos').children('.unity').addClass('unity2');
        $('.orderPos').children('.number').addClass('number2');
        $('.orderPos').children('.price').addClass('price2');
        $('.orderPos').children('.discount').addClass('discount2');
        $('.orderPos').children('.total').addClass('total2');
        $('.orderPos').children('.mechanics').addClass('mechanics2');
        $('.orderPos').children('.status').addClass('status2');
        $('.orderPos').children('.posID').addClass('posID2');
        $('.orderPos').children('.partID').addClass('partID2');
        $('.orderPos').children('.pos-instruction').addClass('pos-instruction2');

        updateDatabase();

        /***************************************************
        *add function and
        *mathematical calculations to elements
        ***************************************************/

        $('.elem').change(function (e) {
            updateDatabase();
        }).on( 'keyup', function(){
            /***************************************************
            *Wird benötigt um der ".elem" für das INSERT das Value zu zuweisen
            ***************************************************/
            var y = $(this).val();
            $(this).attr('value', y);
            updateDatabase();
        } )

        $( '.number, .price, .discount' ).on( 'keyup', function (){
            //Calculate
            if ($(this).parent('.positions').hasClass('orderPos')) {
                var number = $( this ).parent( '.orderPos' ).children( '.number' ).val().replace(',', '.');
                var price = $( this ).parent( '.orderPos' ).children( '.price' ).val().replace(',', '.');
                var discount = ( 1 - ($( this ).parent( '.orderPos' ).children( '.discount' ).val() / 100) );
                //console.log('NUMBERCHANGE = ' + price + ', ' + number + ', ' + discount);
                //var z = ( x * y );
                $( this ).parent( '.orderPos' ).children( '.total' ).val( ((number * price) * discount).toFixed(2).replace('.', ',') );
                //console.log( (number * price) * discount );
                newOrderTotalPrice();
                updateOrderDatabase();
                updatePositionDatabase();
            }
        })

        $( '.rmv' ).click( function (){
            var posToDel = $(this).parent().children('.posID').text();
                $.ajax({
                    url: 'ajax/order.php',
                    data: { action: "delPosition", data: posToDel },
                    type: "POST",
                    success: function(){
                        //alert('Gesendet');
                    },
                    error:   function(){
                        alert('löschen der Daten fehlgeschlagen!');
                    }
                });
            $( this ).parent().remove();
            zaehler();
            updateDatabase();
            newOrderTotalPrice();
            updateOrderDatabase();
        });

        $('#sortable').sortable({
            update: function() {
                zaehler();
                updateDatabase();
            }
        });

        $('#add_item_parts_id_name').focus();

        newOrderTotalPrice();
    }

    function calculateRow(){

    }


    function newOrderTotalPrice(){
        //Calculate
        var nOTP = parseFloat(0);
        var nOTPBr = parseFloat(0);
        $( 'ul#sortable > li' ).each( function(){
            nOTP = (nOTP + parseFloat( ($(this).children('.total').val()).replace(',', '.')) );
        })
            //console.log(nOTP);
            $('#orderTotalNetto').val((nOTP).toFixed(2).replace('.', ','));
            nOTPBr = ( nOTP * 1.19 );
            $('#orderTotalBrutto').val((nOTPBr).toFixed(2).replace('.', ','));
            //console.log(nOTPBr);
    }

    function updateOrderDatabase() {
        $.ajax({
            url: 'ajax/order.php?action=getOrderID&data='+newOrdNr,
            type: "GET",
                success: function ( data ) {
                    //console.log( data );
                    var netamount = $('#orderTotalNetto').val().replace(',', '.');
                    var amount = $('#orderTotalBrutto').val().replace(',', '.');
                    //console.log( netamount );
                    clearTimeout( timer );
                    timer = setTimeout( function(){   //calls click event after a certain time
                        var updateDataJSON = new Array;
                        updateDataJSON.push({
                            //"Bezeichnung des Arrays": Inhalt der zu Speichern ist
                            "ordnumber": data[0].auftrags_id,
                            "km_stnd": $('#head08').val(),
                            "netamount": netamount,
                            "amount": amount,
                            "status": $('#head18').val(),
                            "finish_time": $('#head06').val(),
                            "car_status": $('#head14').val()
                        });
                        //console.log(updateDataJSON);

                        //updateDataJSON.pop();

                        $.ajax({
                            url: 'ajax/order.php',
                            //data: { action: "updatePositions", data: JSON.stringify(updateDataJSON)},
                            data: { action: "updateOrder", data: updateDataJSON },
                            type: "POST",
                                success: function(){
                                    //alert( 'send all posdata' );
                                },
                                error:  function(){
                                    alert( 'Update des Auftrages fehlgeschlagen' );
                                }
                        });

                    }, 800 );
                },
                error: function () {
                    //console.log( 'autoc_part holen fehlgeschlagen ...' );
                }
        });
    }

    /***************************************************
    *count all positions and set the value of
    *position-nr to his count
    ***************************************************/

    function zaehler() {
        $( '.positions' ).each( function( cnt, list ){
            $( list ).attr( 'id', 'pos__' + ( cnt + 1 ) ).children( '.pos' ).val( ( cnt + 1 ) ).attr('value', (cnt + 1));
            /***************************************************
            *For-Schleife um in den einzelnen ListenElementen die Felder
            *durch zu zählen und zu nummerieren
            ***************************************************/
            var elements = $( this ).children( '.elem' );
            for( var i = 0; i < elements.length; i++ ){
                elements[i].id = $( list ).attr( 'id' ) + '__elem__' + ( i + 1 );
            }
        })
    }

    /***************************************************
    *updated database if changes in each position
    ***************************************************/

    function updateDatabase() {
        //clearTimeout( timer );
        //timer = setTimeout( function(){   //calls click event after a certain time
            var updateDataJSON = new Array;
            $( 'ul#sortable > li' ).each( function(){
                updateDataJSON.push({
                    //"Bezeichnung des Arrays": Inhalt der zu Speichern ist
                    "order_nr": $( this ).children( '.pos' ).val(),
                    "item_nr": $( this ).children( '.itemNr' ).val(),
                    "pos_description": $( this ).children( '.description' ).val(),
                    "pos_unit": $( this ).children( '.unity' ).val(),
                    "pos_qty": $( this ).children( '.number' ).val().replace(',', '.'),
                    "pos_price": $( this ).children( '.price' ).val().replace(',', '.'),
                    "pos_discount": $( this ).children( '.discount' ).val(),
                    "pos_total": $( this ).children( '.total' ).val().replace(',', '.'),
                    "pos_emp": $( this ).children( '.mechanics' ).val(),
                    "pos_status": $( this ).children( '.status' ).val(),
                    "pos_id": $( this ).children( '.posID' ).text(),
                    "partID": $( this ).children( '.partID' ).text()
                });
            })
            updateDataJSON.pop();
            //console.log(updateDataJSON);
            $.ajax({
                url: 'ajax/order.php',
                data: { action: "updatePositions", data: updateDataJSON },
                type: "POST",
                    success: function(){
                        //alert( 'send all posdata' );
                        updateOrderDatabase();
                    },
                    error:  function(){
                        alert( 'error sending posdata' );
                    }
            });
       //    }, 800 );
    }

            /***************************/
        /***********************************/
    /*******************************************/
/***************************************************/

    function make_defined_state () {
      if (state == STATES.PICKED) {
        annotate_state();
        return true
      } else if (state == STATES.UNDEFINED && $dummy.val() == '')
        set_item({})
      else {
        last_unverified_dummy = $dummy.val();
        set_item({ id: last_real, name: last_dummy })
      }
      annotate_state();
    }

    function annotate_state () {
      if (state == STATES.PICKED)
        $dummy.removeClass(STATES.UNDEFINED).addClass(STATES.PICKED);
      else if (state == STATES.UNDEFINED && $dummy.val() == '')
        $dummy.removeClass(STATES.UNDEFINED).addClass(STATES.PICKED);
      else {
        last_unverified_dummy = $dummy.val();
        $dummy.addClass(STATES.UNDEFINED).removeClass(STATES.PICKED);
      }
    }

    function update_results () {
      $.ajax({
        url: '../../controller.pl?action=Part/part_picker_result',
        data: $.extend({
            'real_id': $real.val(),
        }, ajax_data(function(){ var val = $('#part_picker_filter').val(); return val === undefined ? '' : val })),
        success: function(data){ $('#part_picker_result').html(data) }
      });
    };

    function result_timer (event) {
      if (!$('no_paginate').prop('checked')) {
        if (event.keyCode == KEY.PAGE_UP) {
          $('#part_picker_result a.paginate-prev').click();
          return;
        }
        if (event.keyCode == KEY.PAGE_DOWN) {
          $('#part_picker_result a.paginate-next').click();
          return;
        }
      }
      window.clearTimeout(timer);
      timer = window.setTimeout(update_results, 100);
    }

    function close_popup() {
      $('#part_selection').dialog('close');
    };

    function handle_changed_text(callbacks) {
      $.ajax({
        url: '../../controller.pl?action=Part/ajax_autocomplete',
        dataType: "json",
        data: $.extend( ajax_data($dummy.val()), { prefer_exact: 1 } ),
        success: function (data) {
          if (data.length == 1) {
            set_item(data[0]);
            if (callbacks && callbacks.match_one) callbacks.match_one(data[0]);
          } else if (data.length > 1) {
            state = STATES.UNDEFINED;
            if (callbacks && callbacks.match_many) callbacks.match_many(data);
          } else {
            state = STATES.UNDEFINED;
            if (callbacks &&callbacks.match_none) callbacks.match_none();
          }
          annotate_state();
        }
      });
    };

    $dummy.autocomplete({
      source: function(req, rsp) {
        $.ajax($.extend(o, {
          url:      '../../controller.pl?action=Part/ajax_autocomplete',
          dataType: "json",
          data:     ajax_data(req.term),
          success:  function (data){
            rsp(data);
            clearTimeout( timer );
            timer = setTimeout( function(){   //calls click event after a certain time
                if( data.length == 0 ){
                    var buchungsgruppen_id;
                    $('<div></div>').appendTo('body').html(
                        '<table>' +
                          '<tr>' +
                            '<td> Artikel-Nr.:</td>' +
                            '<td><input type="text" id="txtArtAnlArtikelNr"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Beschreibung:</td>' +
                            '<td><input type="text" id="txtArtAnlBeschreibung" value="' + $('#add_item_parts_id_name').val() + '">' +
                              '<label for="instructionCheckbox">Instruction</label>' +
                              '<input type="checkbox" name="instructionCheckbox" id="instructionCheckbox">' +
                            '</td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Quantity:</td><td><input type="text" id="quantity" value="1"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Einheit:</td>' +
                            '<td>' +
                              '<select id="selectArtAnlUnits" name="units" type="text" class="ui-widget-content ui-corner-all" autocomplete="off" style="width: 100%">' +
                                '<option selected="selected"></option>' +
                              '</select>' +
                            '</td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Einkaufspreis:</td>' +
                            '<td><input type="text" id="txtArtAnlEinkaufspreis" value="0"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Verkaufspreis:</td>' +
                            '<td><input type="text" id="txtArtAnlPreis" value="0"></td>' +
                          '</tr>' +
                          '<tr>' +
                            '<td>Buchungsgruppe:</td>' +
                            '<td><select id="selectArtAnlBuchungsgruppen" name="buchungsgruppen" type="text" class="ui-widget-content ui-corner-all" autocomplete="off" style="width: 100%">' +
                                  '<option selected="selected"></option>' +
                                '</select>' +
                            '</td>' +
                          '</tr>' +
                        '</table>'
                        ).dialog({
                        modal: true,
                        title: 'Artikel anlegen',
                        zIndex: 10000,
                        autoOpen: true,
                        width: 'auto',
                        resizable: false,
                        create: function( event, ui ){
                            $( '#instructionCheckbox' ).checkboxradio();
                            if( $('#txtArtAnlBeschreibung').val().length >=18 ) $( '#instructionCheckbox' ).prop( "checked", true ).checkboxradio( 'refresh' );
                            //Sollte nur einmal beim laden der Seite erledigt werden! Ändert sich nicht mehr
                            $.ajax({
                                url: 'ajax/order.php?action=getBuchungsgruppen',
                                type: 'GET',
                                success: function( data ){
                                    $.each( data, function( index, item ){
                                        //console.log(item);
                                        $( '#selectArtAnlBuchungsgruppen' ).append( $( '<option id="' + item.id + '" value="' + item.description + '">' + item.description + '</option>' ) );
                                        if( item.id == 859 ){ //ToDo: 859????
                                            $( '#selectArtAnlBuchungsgruppen' ).children( '#'+item.id ).attr( 'selected', 'selected' );
                                            buchungsgruppen_id = item.id;
                                            //steuerSatz = item.description;
                                            //steuerSatz = steuerSatz.replace(/\D/g,'');
                                            //console.log(steuerSatz);
                                        }
                                    })

                                },
                                error:  function(){ alert( "Error: getBuchungsgruppen()!" ); }
                            }); //end ajax getBuchungsgruppen

                            $.ajax({
                                url: 'ajax/order.php?action=getUnits',
                                type: 'GET',
                                success: function( data ){
                                    $.each( data, function( index, item ){
                                        //console.log(item);
                                        $( '#selectArtAnlUnits' ).append( $( '<option id="unit__' + item.name + '" value="' + item.name + '">' + item.name + '</option>' ) );
                                        if( item.name == 'Std' ){ //ToDo: whats up when englisch is selected
                                            $( '#selectArtAnlUnits' ).children( '#unit__' + item.name ).attr( 'selected', 'selected' );
                                            unit = item.name;
                                            getArticleNumber();
                                        }
                                    })

                                },
                                error:  function(){ alert( 'Error: getUnits()' ); }
                            }); //end getUnits

                            //$('#add_item_parts_id_name').val('');

                            $('#selectArtAnlBuchungsgruppen').change(function () {
                                buchungsgruppen_id = $(this).children(':selected').attr('id');
                                //console.log(buchungsgruppen_id);
                                //steuerSatz = $(this).children(':selected').val();
                                //steuerSatz = steuerSatz.replace(/\D/g,'');
                                //console.log(steuerSatz);
                            })

                            $('#txtArtAnlEinkaufspreis').focus().on('keyup', function () {
                                //console.log($('#selectArtAnlBuchungsgruppen').find('option:selected').attr('id'));
                            });

                            $('#selectArtAnlUnits').change(function () {
                                unit = $(this).val();
                                //console.log(unit);
                                getArticleNumber();
                            });


                        },// end create function
                        buttons: [{
                            //first button
                            text: 'Artikel anlegen',
                            id: 'insertArtikel',
                            click: function (){
                                var artObject = {};
                                artObject['part'] = $('#txtArtAnlArtikelNr').val();
                                artObject['description'] = $('#txtArtAnlBeschreibung').val();
                                artObject['unit'] = $('#selectArtAnlUnits').val();
                                artObject['listprice'] = $('#txtArtAnlEinkaufspreis').val() == '' ? '0' : $('#txtArtAnlEinkaufspreis').val().replace(',', '.');
                                artObject['sellprice'] = $('#txtArtAnlPreis').val().replace(',', '.');
                                artObject['buchungsgruppen_id'] = buchungsgruppen_id;
                                artObject['quantity'] = $( "#quantity" ).val().replace(',', '.');
                                $.ajax({
                                    url: 'ajax/order.php',
                                    //data: { action: $( '#instructionCheckbox' ).is( ":checked" ) ? "newInstuction" : "newPart", data: artObject },
                                    data: { action: "newPart", data: artObject },
                                    type: "POST",
                                    success: function( data ){
                                        $( '.newOrderPos' ).children( '.itemNr').val( artObject['part'] );
                                        $( '#add_item_parts_id_name' ).val( artObject['description'] );
                                        $( '[id$=elem__4]:last' ).val( artObject['quantity'].replace( '.', ',' ) );
                                        $( '.orderPos' ).children( 'img' ).css({ 'visibility' : 'visible' }); //show del-image and move-image
                                        $( '.newOrderPos' ).children( '.unity' ).val( artObject['unit'] );
                                        $( '.newOrderPos' ).children( '.price' ).val( ( artObject['sellprice'] ).replace( '.', ',') );
                                        $( '.newOrderPos' ).children( '.discount' ).val( '0' );
                                        $( '.newOrderPos' ).children( '.partID' ).text( data );
                                        $( '.newOrderPos' ).children( '.total' ).val( ( artObject['quantity'] * artObject['sellprice'] ).toFixed( 2 ).replace( '.', ',' ) );
                                        $( '.newOrderPos' ).children().children( '.description' ).addClass( 'descrNewPos' );
                                        $( '.orderPos' ).removeClass( 'oP' );
                                        $( '.newOrderPos' ).clone().insertBefore( '.newOrderPos' ).removeClass( 'newOrderPos' ).addClass( 'orderPos oP' );
                                        $( '[id$=elem__4]:last' ).val( '' ); //cloned quantity
                                        $( '[id$=elem__7]:last' ).val( '0'  );
                                        $( '<input name="pos_description" type="text" class="ui-widget-content ui-corner-all description oPdescription elem">' ).insertAfter( $( '.oP' ).children( '.itemNr' ) );
                                        $( '<input name="pos_unit" type="text" class="ui-widget-content ui-corner-all unity oPunity elem" autocomplete="off">' ).insertAfter( $( '.oP' ).children( '.description' ) );
                                        //oPunity
                                        $( '.oP' ).children( '.oPdescription' ).val( artObject['description'] );
                                        $( '.oP' ).children( '.oPunity' ).val( artObject['unit'] );
                                        newPosition();
                                        saveLastArticleNumber();
                                        newOrderTotalPrice();
                                        //calculateRow();
                                        updateDatabase();

                                        //alert( 'Artikel erfolgreich angelegt' );
                                        $( '.orderPos' ).children( 'img' ).css({ 'visibility' : 'visible' });
                                    },
                                    error:  function(){
                                        alert( 'Error: newPart() !' );
                                    }
                                }); //ajax
                                $( this ).dialog( 'close' );
                            } //end insertArticle Click
                        }, // end Button 'Create Article'
                        {
                            text: 'Correction',
                            id: 'correctArticle',
                            click: function(){
                                //$( '#add_item_parts_id_name
                                $( this ).dialog( 'close' );
                            }
                        }],
                        close: function(){
                            $( this ).remove();
                        }

                    }) //end Dialog
                } //end If
             }, 1000 ); //end Timer
          }

        }));
      },
      select: function(event, ui) {
        set_item(ui.item);
      },
    });

/***************************************************/
    /*******************************************/
        /***********************************/
            /***************************/

    /***************************************************
    *get Article-Number
    ***************************************************/

    function getArticleNumber() {
        $.ajax({
            url: 'ajax/order.php?action=getArticleNumber&data=' + unit,
            type: 'GET',
            success: function( data ) {
                defaults_id = data[0].defaults_id;
                artNrZaehler = data[0].newnumber;
                service = data[0].service;
                //customer_hourly_rate = data[0].customer_hourly_rate;
                $( '#txtArtAnlArtikelNr' ).val( data[0].newnumber );
                if( $( '#selectArtAnlUnits').val() == 'Std' ) $( '#txtArtAnlPreis' ).val( data[0].customer_hourly_rate.replace( '.', ',' ) );
            },
            error:  function(){
                alert( "Error: getArticleNumber() !" );
            }
        })
    }

    /***************************************************
    *increase article-number in DB defaults
    ***************************************************/

    function saveLastArticleNumber(){
        $.ajax({
            url: 'ajax/order.php',
            data: { action: "saveLastArticleNumber", data: {'id': defaults_id, 'unit': unit, 'artNr': artNrZaehler, 'service': service } },
            type: "POST",
                success: function(){
                    //alert( 'saveLastArticleNumber()' );
                },
                error:  function(){
                    alert( 'Error: saveLastArticleNumber() !' );
                }
        });
    }

            /***************************/
        /***********************************/
    /*******************************************/
/***************************************************/

    /*  In case users are impatient and want to skip ahead:
     *  Capture <enter> key events and check if it's a unique hit.
     *  If it is, go ahead and assume it was selected. If it wasn't don't do
     *  anything so that autocompletion kicks in.  For <tab> don't prevent
     *  propagation. It would be nice to catch it, but javascript is too stupid
     *  to fire a tab event later on, so we'd have to reimplement the "find
     *  next active element in tabindex order and focus it".
     */
    /* note:
     *  event.which does not contain tab events in keypressed in firefox but will report 0
     *  chrome does not fire keypressed at all on tab or escape
     */
    $dummy.keydown(function(event){
      if (event.which == KEY.ENTER || event.which == KEY.TAB) {
        // if string is empty assume they want to delete
        if ($dummy.val() == '') {
          set_item({});
          return true;
        } else if (state == STATES.PICKED) {
          return true;
        }
        if (event.which == KEY.TAB) {
          event.preventDefault();
          handle_changed_text();
        }
        if (event.which == KEY.ENTER) {
          handle_changed_text({
            match_one:  function(){$('#update_button').click();},
            match_many: function(){open_dialog();}
          });
          return false;
        }
      } else {
        state = STATES.UNDEFINED;
      }
    });

    $dummy.on('paste', function(){
      setTimeout(function() {
        handle_changed_text();
      }, 1);
    });

    $dummy.blur(function(){
      window.clearTimeout(timer);
      timer = window.setTimeout(annotate_state, 100);
    });

    // now add a picker div after the original input
    var popup_button = $('<span>').addClass('ppp_popup_button');
    $dummy.after(popup_button);
    popup_button.click(open_dialog);

    var pp = {
      real:           function() { return $real },
      dummy:          function() { return $dummy },
      type:           function() { return $type },
      unit:           function() { return $unit },
      convertible_unit: function() { return $convertible_unit },
      update_results: update_results,
      result_timer:   result_timer,
      set_item:       set_item,
      reset:          make_defined_state,
      is_defined_state: function() { return state == STATES.PICKED },
      init_results:    function () {
        $('div.part_picker_part').each(function(){
          $(this).click(function(){
            set_item({
              id:   $(this).children('input.part_picker_id').val(),
              name: $(this).children('input.part_picker_description').val(),
              unit: $(this).children('input.part_picker_unit').val(),
              partnumber:  $(this).children('input.part_picker_partnumber').val(),
              description: $(this).children('input.part_picker_description').val(),
            });
            close_popup();
            $dummy.focus();
            return true;
          });
        });
        $('#part_selection').keydown(function(e){
           if (e.which == KEY.ESCAPE) {
             close_popup();
             $dummy.focus();
           }
        });
      }
    }
    $real.data('part_picker', pp);
    return pp;
  }
});

$(function(){
  $('input.part_autocomplete').each(function(i,real){
    kivi.PartPicker($(real));
  })
});
