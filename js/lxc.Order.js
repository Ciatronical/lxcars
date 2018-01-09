 namespace('kivi.Part', function(ns) {
  'use strict';

   $.urlParam = function( name ){
      var results = new RegExp( '[\?&]' + name + '=([^&#]*)' ).exec( window.location.href );
      if( results == null );// alert( 'Parameter: "' + name + '" does not exist in "' + window.location.href + '"!' );
      else return decodeURIComponent( results[1] || 0 );
    }

  var id = $.urlParam( 'id' );
  var owner = $.urlParam( 'owner' );
  var c_id = $.urlParam( 'c_id' );
  var previous = $.urlParam( 'previous' );
  var newOrder = $.urlParam( 'newOrder' );
  var c_hsn = $.urlParam( 'c_hsn' );
  var c_tsn = $.urlParam( 'c_tsn' );


  var orderID;
  var ready = false;
  var timer;
  var updateTime = 1500;

  var customer_hourly_rate;

  var KEY = {
    TAB:       9,
    ENTER:     13,
    SHIFT:     16,
    CTRL:      17,
    ALT:       18,
    ESCAPE:    27,
    PAGE_UP:   33,
    PAGE_DOWN: 34,
    LEFT:      37,
    UP:        38,
    RIGHT:     39,
    DOWN:      40,
  };

  ns.Picker = function($real, options) {
    //console.log("picker");
    var self = this;
    this.o = $.extend(true, {
      limit: 20,
      delay: 50,
      action: {
        commit_none: function(){ },
        commit_one:  function(){ $('#update_button').click(); },
        commit_many: function(){ self.open_dialog(); }
      }
    }, $real.data('part-picker-data'), options);
    this.$real              = $real;
    this.real_id            = $real.attr('id');
    this.last_real          = $real.val();
    this.$dummy             = $($real.siblings()[0]);
    this.autocomplete_open  = false;
    this.state              = this.STATES.PICKED;
    this.last_dummy         = this.$dummy.val();
    this.timer              = undefined;
    this.dialog             = undefined;

    this.init();
  };

  ns.Picker.prototype = {

    CLASSES: {
      PICKED:       'partpicker-picked',
      UNDEFINED:    'partpicker-undefined',
    },
    ajax_data: function(term) {
      var data = {
        'filter.all:substr:multi::ilike': term,
        'filter.obsolete': 0,
        current:  this.$real.val(),
      };

      if (this.o.part_type)
        data['filter.part_type'] = this.o.part_type.split(',');

      if (this.o.classification_id)
        data['filter.classification_id'] = this.o.classification_id.split(',');

      if (this.o.unit)
        data['filter.unit'] = this.o.unit.split(',');

      if (this.o.convertible_unit)
        data['filter.unit_obj.convertible_to'] = this.o.convertible_unit;

      return data;
    },
    set_item: function(item) {
      var self = this;
      if (item.id) {
        this.$real.val(item.id);
        // autocomplete ui has name, use the value for ajax items, which contains displayable_name
        this.$dummy.val(item.name ? item.name : item.value);
      } else {
        this.$real.val('');
        this.$dummy.val('');
      }
      this.state      = this.STATES.PICKED;
      this.last_real  = this.$real.val();
      this.last_dummy = this.$dummy.val();
      this.$real.trigger('change');

      console.log(item.id);
      if (this.o.fat_set_item && item.id) {
        $.ajax({
          url: 'ajax/order.php?action=getPartJSON',
          data: { 'data': item.id },
          success: function( rsp ){
            self.$real.trigger( 'set_item:PartPicker', rsp);

            //nach autocomplete erzeugt neue Position und füllt die aktuell fokussierte Position
            rsp=rsp[0];
            var newPosArray={};

             if( $( '#ordernumber' ).text() == '0000' ) {

              $( '#employee' ).text( kivi.myconfig.name );
              $( '#milage' ).val( '0' );
              console.log(c_hsn);
              $.ajax({
                  url: 'ajax/order.php?action=newOrder',
                  data: { action: 'newOrder', data: { owner_id: owner, car_id: c_id} },
                  type: 'POST',
                  async: false,
                  success: function ( newOrderID ){

                  $.ajax({
                    url: 'ajax/order.php?action=getOrder&data=' + newOrderID,
                    type: 'GET',
                    async: false,
                    success: function( data ){

                        var car = data.c_id;
                        if( data.km_stnd == null ){
                          data.km_stnd = '0';
                        }
                        if ( data.car_status == null ) {
                          data.car_status = 'Auto hier';
                        }
                        $( '#orderTotalNetto' ).val(data.netamount);
                        $( '#orderTotalBrutto' ).val(data.amount);
                        $( '#ordernumber' ).text( data.ordnumber );
                        $( '#name' ).text( data.customer_name );
                        $( '#employee' ).text( kivi.myconfig.name );
                        $( '#date' ).text( data.transdate );
                        $( '#finish_time' ).val( data.finish_time );
                        $( '#milage' ).val( data.km_stnd );
                        $( '#licenseplate' ).val( data.c_ln );
                        $( '#orderstatus' ).val( data.order_status ).change();
                        $( '#car_status' ).val( data.car_status ).change();
                        $( '#mtime' ).text(data.mtime);
                        $( '#headline' ).html( '<b>Auftrag ' + data[1] + ' ' + data[2] + ' ' + data[3] + ' von ' + data.customer_name + '</b>' );

                    }
                  })


                     orderID=newOrderID;
                  },
                  error:  function(){ alert( "Holen der Auftrags-Nr fehlgeschlagen!" ); }
              })

            }

            $( ':focus' ).parents().eq(3).find( '[name=partnumber]' ).text( rsp.partnumber );

            $( ':focus' ).parents().eq(3).find( '[name=partnumber]' ).attr( 'part_id',rsp.id );

            if(rsp.unit == 'Std')
              $( ':focus' ).parents().eq(3).find( '[name=sellprice_as_number]' ).val(ns.formatNumber( parseFloat( customer_hourly_rate ).toFixed( 2 ) ) );
            else
              $( ':focus' ).parents().eq(3).find( '[name=sellprice_as_number]' ).val(ns.formatNumber( parseFloat( rsp.sellprice ).toFixed( 2 ) ) );


            var number=parseFloat($(':focus').parents().eq( 2 ).find( '[name=qty_as_number]') .val() );
            $( ':focus' ).parents().eq(3).find( '[name=partclassification]' ).text(kivi.t8(rsp.part_type));

            if( rsp.instruction ){
              $( '.row_entry:last [name = partclassification]' ).text( kivi.t8("I") );
            }

            $( ':focus' ).parents().eq(3).find( '[name=unit]').val( rsp.unit );
            $( ':focus' ).parents().eq(3).find( '[name=linetotal]').text(ns.formatNumber( parseFloat( rsp.sellprice*number ).toFixed( 2 )) );
            $( ':focus' ).parents().eq(3).find( '[name=item_partpicker_name]' ).val( rsp.description );

            //console.log($( ':focus' ).parents().eq(3));

            $( ':focus' ).parents().eq(3).find( '[class=x]' ).show();
            //$('.autocomplete').removeClass('part_autocomplete');

            //console.log($(':focus').parent());
            ns.getQtybyDescription(item.description);
            newPosArray['position'] = $( ':focus' ).parents().eq(3).find( '[name=position]' ).text();
            newPosArray['parts_id'] =  $( ':focus' ).parents().eq(3).find( '[name=partnumber]' ).attr( 'part_id' );
            newPosArray['order_id'] = orderID;
            newPosArray['description'] = rsp.description;
            newPosArray['sellprice'] = rsp.sellprice;
            newPosArray['ordnumber'] = $( '#ordernumber' ).text();
            newPosArray['qty'] = number;
            newPosArray['unit'] = $(':focus').parents().eq(3).find('[name=unit]').val();
            newPosArray['status'] = $(':focus').parents().eq(3).find('[name=pos_status]').val();

            var discount;
            if($(':focus').parents().eq(3).find('[name=discount_as_percent]').text()=="")
            discount='0';
            else
            discount=$(':focus').parents().eq(3).find('[name=discount_as_percent]').text();

            newPosArray['discount'] = discount;
            newPosArray['linetotal'] = rsp.sellprice*number;

            //console.log(newPosArray);

            console.log(rsp.id);

            newPosArray['instruction']=rsp.instruction;
            if (rsp.instruction) {
              $( ':focus' ).parents().eq(3).addClass( 'instruction' );
            }

            $.ajax({
                 url: 'ajax/order.php',
                 type: 'POST',
                 async:false,
                 data: { action: "insertRow", data: newPosArray },
                 success: function ( data ) {
                    console.log( data );
                    $( ':focus' ).parents().eq(3).attr( 'id',data );

                 },
                 error: function () {
                    alert( 'Error: new Pos not saved' )
                 }

            });


            if( rsp.description.includes( 'Hauptuntersuchung' ) || rsp.description.includes( 'HU/AU' ) ){
              $.ajax({
                url: 'ajax/order.php?action=setHuAuDate&data=' + c_id,
                type: 'GET'

              });

            }

            //erzeugt neue Position
            //console.log( $(':focus').parents().eq(3).is( :first)) );
            $( ':focus' ).parents().eq(3).find( '[name=position]' ).text();

            if($( ':focus' ).parents().eq(3).is( ':last-child' ) )
            $( ':focus' ).parents().eq(3).clone().appendTo( '#row_table_id' );


            //console.log($('.listrow').filter(':last'));
            if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show(); //dont show sortable < 3 rows
            ns.countPos();//nummeriert die positionen
            ns.init();
            ns.recalc();



            ns.updateOrder();

            ns.init();//Initialisiert alle partpicker für die autocomplete function nachdem eine neue Position hinzugefügt wurde
            $('.listrow').filter(':last').find('[name=item_partpicker_name]').focus();
            $('.listrow').filter(':last').removeClass('instruction');
            //sortable update
            $('.ui-sortable').sortable({items: '> tbody:not(.pin)'}); //letzte Position ist nicht Sortable

            //insertRow(rsp);//insert Position oder Instruction
            //alert( "Siehe da! Partnumber: " + rsp.partnumber + " Description: " + rsp.description );
            $( '.instruction , .instruction div , .instruction :input ' ).css({
              'color' : 'red',
              'background-color' : 'lightblue',

            });

          },
        });
      } else {
        this.$real.trigger('set_item:PartPicker', item);

      }
      this.annotate_state();
    },
    set_multi_items: function(data) {
      this.run_action(this.o.action.set_multi_items, [ data ]);
    },
    make_defined_state: function() {
      if (this.state == this.STATES.PICKED) {
        this.annotate_state();
        return true
      } else if (this.state == this.STATES.UNDEFINED && this.$dummy.val() === '')
        this.set_item({})
      else {
        this.set_item({ id: this.last_real, name: this.last_dummy })
      }
      this.annotate_state();
    },
    annotate_state: function() {
      if (this.state == this.STATES.PICKED)
        this.$dummy.removeClass(this.STATES.UNDEFINED).addClass(this.STATES.PICKED);
      else if (this.state == this.STATES.UNDEFINED && this.$dummy.val() === '')
        this.$dummy.removeClass(this.STATES.UNDEFINED).addClass(this.STATES.PICKED);
      else {
        this.$dummy.addClass(this.STATES.UNDEFINED).removeClass(this.STATES.PICKED);
      }
    },
    handle_changed_text: function(callbacks) {

      var self = this;
      console.log( self.ajax_data(self.$dummy.val()));
      $.ajax({
        url: '../../controller.pl?action=Part/ajax_autocomplete',
        dataType: "json",
        data: $.extend( self.ajax_data(self.$dummy.val()), { prefer_exact: 1 } ),
        success: function (data) {

          if (data.length == 1) {
            self.set_item(data[0]);
            if ( callbacks && callbacks.match_one) self.run_action(callbacks.match_one, [ data[0] ] );

          } else if (data.length > 1) {
            self.state = self.STATES.UNDEFINED;
            if ( callbacks && callbacks.match_many ) self.run_action( callbacks.match_many, [ data ] );
          } else {

              var description_name = $( ":focus" ).parents().eq(3).find( "[name=item_partpicker_name]" ).val();

              var descriptionArray=description_name.split(" ");

              console.log(descriptionArray);
              $.each(descriptionArray, function(index) {
                //console.log(descriptionArray[index]);
                $.ajax({
                  url: 'ajax/order.php?action=getQtyNewPart&data='+descriptionArray[index],
                  type: 'GET',
                  success: function( data ) {
                    //console.log(data);
                     $( '#dialogDescription' ).val( description_name );
                      $( '#dialogPart_typ' ).change();

                        if( $('#dialogDescription').val().length >17 ) {

                          $( '#dialogPart_typ' ).val( 'instruction' ).change();
                          $( "dialogSelectUnits" ).val( 'Std' ).change();
                        }else{
                          $( '#dialogPart_typ' ).val( 'dimension' ).change();
                          $( "dialogSelectUnits" ).val( 'Stck' ).change();
                        }


                    if ( data>1 ) {
                    $( '#quantity' ).val( data );
                    return false;
                    }
                  },
                  error: function() {
                    alert( 'error: getQtyNewPart' );
                  }
                });
              });
              $( '#quantity' ).val();
              $( '#newPart_dialog' ).dialog({
                    modal: true,
                    title: 'Artikel anlegen',
                    zIndex: 10000,
                    autoOpen: true,
                    width: 'auto',
                    resizable: false,
                    create: function( event, ui ){
                      console.log("test");
                        $( '#dialogDescription' ).val( description_name );


                }

            });



            self.state = self.STATES.UNDEFINED;
            if (callbacks && callbacks.match_none) self.run_action(callbacks.match_none, [ self, self.$dummy.val() ]);
          }
          self.annotate_state();
        }
      });
    },
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
    handle_keydown: function(event) {

      var self = this;
      if (event.which == KEY.ENTER || event.which == KEY.TAB) {

        // if string is empty assume they want to delete
        if (self.$dummy.val() === '') {
          self.set_item({});
          return true;
        } else if (self.state == self.STATES.PICKED) {
          if (self.o.action.commit_one) {
            self.run_action(self.o.action.commit_one);
          }
          return true;
        }
        if (event.which == KEY.TAB) {
          event.preventDefault();
          self.handle_changed_text();
        }
        if (event.which == KEY.ENTER) {

          self.handle_changed_text({
            match_none: self.o.action.commit_none,
            match_one:  self.o.action.commit_one,
            match_many: self.o.action.commit_many
          });

          return false;
        }
      } else if (event.which == KEY.DOWN && !self.autocomplete_open) {
        var old_options = self.$dummy.autocomplete('option');
        self.$dummy.autocomplete('option', 'minLength', 0);
        self.$dummy.autocomplete('search', self.$dummy.val());
        self.$dummy.autocomplete('option', 'minLength', old_options.minLength);
      } else if ((event.which != KEY.SHIFT) && (event.which != KEY.CTRL) && (event.which != KEY.ALT)) {
        self.state = self.STATES.UNDEFINED;
      }
    },
    open_dialog: function() {

      if (this.o.multiple) {
        this.dialog = new ns.PickerMultiPopup(this);
      } else {
        this.dialog = new ns.PickerPopup(this);
      }
    },
    close_dialog: function() {
      this.dialog.close_dialog();
      this.dialog = undefined;
    },
    init: function() {
      //console.log("init");
      var self = this;
      this.$dummy.autocomplete({
        source: function(req, rsp) {
          $.ajax($.extend(self.o, {
            url:      'ajax/order.php?action=autocompletePart',
            dataType: "json",
            data:     { data: req.term },
            success:  function ( data ){rsp( data );
              console.log(data);
            }
          }));
        },
        select: function(event, ui) {
          self.set_item(ui.item);
        },
        search: function(event, ui) {
          //console.log("search");
          if ((event.which == KEY.SHIFT) || (event.which == KEY.CTRL) || (event.which == KEY.ALT))
            event.preventDefault();
        },
        open: function() {
          //console.log("open");
          self.autocomplete_open = true;
        },
        close: function() {
          self.autocomplete_open = false;
        }


      }) .data( "ui-autocomplete" )._renderItem = function( ul, item ) {

        if( item.instruction ){
          return $( "<li>" )
              .data( "data-value", item )
              .append( "<a style='color : blue'; >" + item.label + "</a>" )
              .appendTo( ul );
        }

        return $( "<li>" )
            .data( "data-value", item )
            .append( "<a>" + item.label + "</a>" )
            .appendTo( ul );

      };

      this.$dummy.keydown(function(event){ self.handle_keydown(event) });
      this.$dummy.on('paste', function(){
        setTimeout(function() {
          self.handle_changed_text();
        }, 1);
      });
      this.$dummy.blur(function(){
        window.clearTimeout(self.timer);
        self.timer = window.setTimeout(function() { self.annotate_state() }, 100);
      });

      var popup_button = $('<span>').addClass('ppp_popup_button');
      this.$dummy.after(popup_button);
      popup_button.click(function() { self.open_dialog() });
    },
    run_action: function(code, args) {
      if (typeof code === 'function')
        code.apply(this, args)
      else
        kivi.run(code, args);
    },
    clear: function() {
      this.set_item({});
    }
  };
  ns.Picker.prototype.STATES = {
    PICKED:    ns.Picker.prototype.CLASSES.PICKED,
    UNDEFINED: ns.Picker.prototype.CLASSES.UNDEFINED
  };

  ns.PickerPopup = function(pp) {
    this.timer = undefined;
    this.pp    = pp;
    this.open_dialog();
  };

  ns.PickerPopup.prototype = {

    open_dialog: function() {

      var self = this;
      kivi.popup_dialog({
        url: '../../controller.pl?action=Part/part_picker_search',
        data: self.pp.ajax_data(this.pp.$dummy.val()),
        id: 'part_selection',
        dialog: {
          title: kivi.t8('Part picker'),
          width: 800,
          height: 800,
        },
        load: function() { self.init_search(); }
      });
      window.clearTimeout(this.timer);
      return true;
    },
    init_search: function() {

      var self = this;
      $('#part_picker_filter').keypress(function(e) { self.result_timer(e) }).focus();
      $('#no_paginate').change(function() { self.update_results() });
      this.update_results();
    },
    update_results: function() {
      var self = this;
      $.ajax({
        url: '../../controller.pl?action=Part/part_picker_result',
        data: $.extend({
          no_paginate: $('#no_paginate').prop('checked') ? 1 : 0,
        }, self.pp.ajax_data(function(){
          var val = $('#part_picker_filter').val();
          return val === undefined ? '' : val
        })),
        success: function(data){
          $('#part_picker_result').html(data);
          self.init_results();
        }
      });
    },
    init_results: function() {
      var self = this;
      $('div.part_picker_part').each(function(){
        $(this).click(function(){
          self.pp.set_item({
            id:   $(this).children('input.part_picker_id').val(),
            name: $(this).children('input.part_picker_description').val(),
            classification_id: $(this).children('input.part_picker_classification_id').val(),
            unit: $(this).children('input.part_picker_unit').val(),
            partnumber:  $(this).children('input.part_picker_partnumber').val(),
            description: $(this).children('input.part_picker_description').val(),
          });
          self.close_dialog();
          self.pp.$dummy.focus();
          return true;
        });
      });
      $('#part_selection').keydown(function(e){
         if (e.which == KEY.ESCAPE) {
           self.close_dialog();
           self.pp.$dummy.focus();
         }
      });
    },
    result_timer: function(event) {
      var self = this;
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
      window.clearTimeout(this.timer);
      if (event.which == KEY.ENTER) {
        self.update_results();
      } else {
        this.timer = window.setTimeout(function() { self.update_results() }, 100);
      }
    },
    close_dialog: function() {
      $('#part_selection').dialog('close');
    }
  };

  ns.reinit_widgets = function() {
    kivi.run_once_for('input.part_autocomplete', 'part_picker', function(elt) {
      if (!$(elt).data('part_picker'))
        $(elt).data('part_picker', new kivi.Part.Picker($(elt)));
    });
  }

  ns.init = function() {
    ns.reinit_widgets();
  }

  //zählt und beschriftet die Positionsnummer und löscht den Inhalt der letzten Position
  ns.countPos=(function () {

    var posID=0;

    $( '.listrow' ).each(function () {
      posID++;
      $(this).find( '[name=position]' ).text(posID);
      $(this).removeClass('pin');
    });

    var lastRow = $( '.listrow' ).filter( ':last ' );
    lastRow.find( '[name=item_partpicker_name]' ).val( '' );
    lastRow.find( '[name=partnumber]' ).text( '' );
    lastRow.find( '[name=sellprice_as_number]' ).val( 0 );
    lastRow.find( '[name=linetotal]' ).text( 0 );
    lastRow.find( '[name=partclassification]' ).text( '' );
    lastRow.find( '[name=longdescription]' ).val( '' );
    lastRow.find( '[name=qty_as_number]' ).val( '1' );
    lastRow.removeAttr( 'id' );
    lastRow.find( 'img' ).hide();
    $( '.row_entry' ).last().find( '[class=x]' ).hide();
    lastRow.addClass( 'pin' );

  });



  ns.delete_order_item_row = function(clicked) {
    var row = $( clicked ).parents( "tbody" ).first();
    //console.log( row );
    var id =$( row ).attr( 'id' );
    var dataArray={};
    dataArray['id']=id;
    dataArray['instruction']=$(row).hasClass( 'instruction' );
    //console.log(id);
    $.ajax({
      url: 'ajax/order.php',
      type: 'POST',
      data: { action: "delPosition", data: dataArray },
      success: function () {
        $(row).remove();
        ns.countPos();
        ns.recalc();
        ns.updateOrder();

      },
      error: function () {
        alert( 'ERROR: Could not delete Position' );
      }

    });

  };


  $( document ).ready( function(){



    var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
    var baseUrl = kivi_global.baseurl;
    $('[name=item_partpicker_name]').focus();


    $.ajax({
      url: 'ajax/order.php?action=getUsersFromGroup&data='+"Werkstatt",
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){
        //console.log(item);
          $( '[name = mechanics], [name = allMechanics]' ).append( $( '<option class="opt mech__' + item.name + '" value="'+item.name + '">' + item.name + '</option>' ) );
        })
      },
      error:  function(){
        alert( "Ajaxerror getMechnics()!" );
      }
    })


    $.ajax({
      url: 'ajax/order.php?action=getCustomer_hourly_rate',
      type: 'GET',
      success: function (data) {
              //console.log(data);
        customer_hourly_rate = data['customer_hourly_rate'];

      },
      error: function() {
        alert('Error: getCustomer_hourly_rate');
      }

    })

    $.ajax({
      url: 'ajax/order.php?action=getAccountingGroups',
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){
          $( '#accountingGroups' ).append( $( '<option id="' + item.id + '" value="' + item.description + '">' + item.description + '</option>' ) );
        })
      },
      error: function(){
        alert( "Error: getAccountingGroups()!" );
      }
    }); //end ajax accountingGroups

    var unitsType=new Array;

    $.ajax({
      url: 'ajax/order.php?action=getUnits',
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){

          unitsType[item.name] = item.type;
          $( '[name = unit]' ).append($( '<option class="opt unit__' + item.name + '" value="' + item.name + '">' + item.name + '</option>' ) );
        })
      },
      error:  function(){
        alert( "Error getUnits()!" );
      }
    })


  //DateTimePicker
  function AddButton( input ){
    setTimeout( function(){
      var buttonPane = $( input ).datepicker( "widget" ).find( ".ui-datepicker-buttonpane" );
      var btn = $( '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button"> Wartet</button>' );
      btn.appendTo( buttonPane );
      btn.bind( "click", function(){
          $( "#finish_time" ).val("Kunde wartet! SOFORT anfangen!").change();
      });
    }, 1 );
  }

  $( "#finish_time" ).datetimepicker({
    beforeShow: function( input ){
      AddButton( input );
    },
    onChangeMonthYear: function( year, month, inst ){
      AddButton( inst.input );
    },
    stepMinute: 5,
    hour: 1,
    hourMin: 6,
    hourMax: 19,
    timeSuffix: kivi.t8( ' clock' ),
    timeText: kivi.t8(' Time'),
    hourText: 'Stunde',
    closeText: 'Fertig',
    currentText: 'Jetzt'
  });

  $( "#crm" ).button({
    label: "CRM"
  }).css({
    'margin':'5px'
  }).click( function(){
    location.href = baseUrl + '/crm/firma1.php?Q=C&id=' + owner;
    return false;
  });

  $( "#orderList" ).button({
    label: kivi.t8("Orderlist")
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = baseUrl + '/crm/lxcars/orderlist.phtml?owner=' + owner + '&c_id=' + c_id + '&c_hsn=' + c_hsn + '&c_tsn=' + c_tsn;
    return false;
  });

  $( "#car" ).button({
    label: kivi.t8("Car")
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = baseUrl + '/crm/lxcars/lxcmain.php?owner=' + owner + '&c_id=' + c_id + '&task=3';
    return false;
  });

  $( "#back" ).button({
    label: kivi.t8("back")
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = baseUrl + '/crm/lxcars/' + previous + '?owner=' + owner + '&c_id=' + c_id + '&task=3' + '&c_hsn=' + c_hsn + '&c_tsn=' + c_tsn;
    return false;
  });



  $( "#printOrder, #pdfOrder" ).button({
    label:  kivi.t8('Print')
  }).css({
    'margin':'5px'
  }).click( function(){
    var id = $(this)[0].id;
    $.ajax({
      url: 'ajax/order.php?action=printOrder&data=',
      type: 'GET',
      async: false,
      data: { data: { 'orderId': orderID, 'print': id == 'printOrder' ? 1 : 0 } },
      success: function ( data ) {
        if( id == 'pdfOrder' ) window.open( 'out.pdf' );
      },
      error: function () {
        alert( 'Error printOrder()!' )
      }
    });
    return false;
  });

  $( "#pdfOrder" ).button({
    label: 'Pdf'
  })

  $( '#invoice' ).button({
    label: kivi.t8( 'invoice' )
  }).css({
    'margin':'5px'
  }).click( function(){
    alert( 'currently not implemented' );
    //window.location = baseUrl + '/crm/lxcars/' + previous + '?c_id=' + c_id + '&task=3';
    return false;
  });

  ns.recalc = function(){
    var totalprice = 0;
    var totalnetto = 0;
    $( '.listrow' ).each( function(){
      if( $( this ).hasClass( 'instruction' ) ){
        var linetotal = 0;
      }
      else{
        var number = parseFloat( $( this ).find( '[name = qty_as_number]' ).val() );
        var price = parseFloat( $( this ).find( '[name = sellprice_as_number]' ).val() );
        var discount = parseFloat( $( this ).find( '[name = discount_as_percent]' ).val() );
        discount = discount / 100;
        $( this ).find( '[name = linetotal]' ).text( ns.formatNumber( parseFloat( price * number -  price * number * discount ).toFixed( 2 ) ) );
        var linetotal = parseFloat($( this ).find( '[name = linetotal]' ).text() );
      }

      //console.log( linetotal );
      totalprice = totalprice + linetotal;
      var netto = linetotal - linetotal * 0.19;//ToDo Was passiert wenn der Staat den Steuersatz auf 22 Prozent anhebt??
      totalnetto = totalnetto + netto;
      //console.log(totalprice);
      $( '#orderTotalBrutto' ).val( ns.formatNumber( totalprice.toFixed( 2 ) ) );
      $( '#orderTotalNetto' ).val( ns.formatNumber( totalnetto.toFixed( 2 ) ) );
    });
  }

  $( "label[for = 'instructionCheckbox']" ).text( kivi.t8( 'Instruction' ) );
  $( "btnNewOrder" ).val( kivi.t8( 'new Order' ) );

  $( '#row_table_id' ).on( 'sortstop', function( event, ui ){
    //$('#row_table_id thead a img').remove();
    ns.countPos();
    ns.updatePosition();
  });

  ns.formatNumber = function ( number ){
    var format = kivi.myconfig.numberformat;
    var fractionalPart = format.split( ',' );

    if( fractionalPart[1].length == 2 ){
      number = number.replace( '.',',' );
    }
    else{
      fractionalPart = format.split( '.' );
      if( fractionalPart[1].length == 2 )
        number = number.replace( ',','.' );
    }
    if( number.length > 6 ){ //ToDo: Was macht dieser Code??
    //var str='.';
    //number = [number.slice(0, number.length-6), str , number.slice(number.length-6)].join('');
    //console.log(number);
    }
    return number;
  }


  //Get Order
  $.ajax({
    url: 'ajax/order.php?action=getOrder&data=' + id,
    type: 'GET',
    async: false,
    success: function( data ){
      console.log( data );
      var car = data.c_id;
      if( data.km_stnd == null ){
        data.km_stnd = '0';
      }
      if ( data.car_status == null ) {
        data.car_status = 'Auto hier';
      }
      $( '#orderTotalNetto' ).val(data.netamount);
      $( '#orderTotalBrutto' ).val(data.amount);
      $( '#ordernumber' ).text( data.ordnumber );
      $( '#name' ).text( data.customer_name );
      $( '#employee' ).text( kivi.myconfig.name );
      $( '#date' ).text( data.transdate );
      $( '#finish_time' ).val( data.finish_time );
      $( '#milage' ).val( data.km_stnd );
      $( '#licenseplate' ).val( data.c_ln );
      $( '#orderstatus' ).val( data.order_status ).change();
      $( '#car_status' ).val( data.car_status ).change();
      $( '#mtime' ).text(data.mtime);
      //if (data[1]=="undefined")
      //$( '#headline' ).html( '<b>Auftrag ' + data[0]['hersteller'] + ' ' + data[0]['typ'] + ' ' + data[0]['bezeichung'] + ' von ' + data.customer_name + '</b>' );
      //else
      $( '#headline' ).html( '<b>Auftrag ' + data[1] + ' ' + data[2] + ' ' + data[3] + ' von ' + data.customer_name + '</b>' );
      orderID = data.oe_id;

      //Get Position
      //console.log(data.amount);
      if( data.amount!=null ){//data.amount!=null Bei neuen Aufträgen werden die Positionen nicht abgefragt(Wenn Gesamtbetrag null)
        $.ajax({
          url: 'ajax/order.php?action=getPositions&data=' + orderID,
          type: 'GET',
          success: function ( data ) {
            //console.log(data);

            $.each( data.reverse(), function( index, item ){

              $( '.row_entry [name=partnumber]' ).last().text( item.partnumber );

              $( '.row_entry [name=partclassification]' ).last().text( kivi.t8( item.part_type ) );
              if (item.instruction)
                $( '.row_entry [name=partclassification]' ).last().text( kivi.t8( 'I') );

              $( '.row_entry').last().attr( 'id', item.id );
              $( '.row_entry [name=partnumber]').last().attr( 'part_id', item.parts_id );
              $( '.row_entry [name=position]').last().text( item.position );
              $( '.row_entry [name=item_partpicker_name]').last().val( item.description );
              $( '.row_entry [name=mechanics]').last().val( item.u_id );
              if(item.unit == 'Std')
              $( '.row_entry [name=sellprice_as_number]').last().val(customer_hourly_rate.toFixed(2));
              else
              $( '.row_entry [name=sellprice_as_number]').last().val( ns.formatNumber(item.sellprice.toFixed(2)) );

              $( '.row_entry [name=unit]' ).last().val( item.unit ).change();
              $( '.row_entry [name=pos_status]' ).last().val( item.status ).change();
              $( '.row_entry [name=qty_as_number]' ).last().val( ns.formatNumber(item.qty.toFixed(2)) );
              $( '.row_entry [name=discount_as_percent]' ).last().val( ns.formatNumber((item.discount * 100).toFixed(2) ) );

              $( '.row_entry [name=linetotal]' ).last().text( ns.formatNumber((item.qty*item.sellprice-item.qty*item.sellprice*item.discount/100).toFixed( 2 )) );
              $( '.row_entry [name=longdescription]' ).last().val( item.longdescription ).change();
              $( '.row_entry [class=x]' ).last().show();

              if ( item.instruction )
              $( '.row_entry' ).last().addClass( 'instruction' );

              $( '.row_entry' ).last().clone().appendTo( "#row_table_id" );
              $( '.row_entry' ).last().removeClass( 'instruction' );


            });
            if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show();
            ns.countPos();
            ns.recalc();
            ns.init();
            ready = true;
            $( '.listrow' ).filter( ':last' ).find( '[name=item_partpicker_name]' ).focus();

            $( '.ui-sortable' ).sortable( {items: '> tbody:not(.pin)'} );



            $( 'instrution, .instruction div , .instruction :input ' ).css({
              'color' : 'red',
              'background-color' : 'lightblue'

            });

            //console.log(data);
          },
          error: function () {
              alert( "error: getPositions fehlgeschlagen" );
         }

        });
      }
    }
  });





  $( '#btnSaveNewPart' ).click(function () {

    if ( $( '#dialogNewArticleNumber' ).val()!="" ) {

      var dataArray = {};
      dataArray['partnumber'] = $( '#dialogNewArticleNumber' ).val();
      dataArray['description'] = $( '#dialogDescription' ).val();
      dataArray['unit'] = $( '#dialogSelectUnits' ).val();
      dataArray['listprice'] = $( '#dialogBuyPrice' ).val();
      dataArray['sellprice'] = $( '#dialogSellPrice' ).val().replace(",",".");
      dataArray['buchungsgruppen_id'] = $( '#accountingGroups option:selected' ).attr( 'id' );
      //alert(  );
      dataArray['quantity'] = $( "#quantity" ).val();

      var part_type = $( '#dialogPart_typ' ).val();
      console.log(part_type);
      if(part_type == "instruction"){
      console.log("instruction")


     dataArray['instruction'] = true;
     }else
     dataArray['instruction'] = false;

      dataArray['order_id'] = orderID;
      dataArray['part_type'] = unitsType[$( '#dialogSelectUnits' ).val()];
      if( dataArray['part_type'] == 'dimension' )
        dataArray['part_type'] = 'part';
      if (dataArray['part_type'] == 'instruction')
        dataArray['part_type'] = 'service';

      dataArray['position'] =  $( '.row_entry' ).last().find( '[name=position]' ).text();
      //console.log( dataArray );

      $.ajax({
         url: 'ajax/order.php',
         type: 'POST',
         data: { action: "newPart", data: dataArray },
         success: function( data ){
            $( '.row_entry:last [name=partnumber]' ).text( dataArray.partnumber );
            $( '.row_entry:last [name=partclassification]' ).text( kivi.t8(dataArray.part_type) );
            if( dataArray.instruction ) $( '.row_entry:last[name = partclassification]' ).text( kivi.t8( "I" ) );
            $( '.row_entry:last').attr('id', data);
            $( '.row_entry:last [name=partnumber]' ).attr( 'part_id', data );
            $( '.row_entry:last [name=position]').text( dataArray.position );
            $( '.row_entry:last [name=item_partpicker_name]' ).val( dataArray.description );
            $( '.row_entry:last [name=sellprice_as_number]' ).val( ns.formatNumber( dataArray.sellprice ) );
            $( '.row_entry:last [name=unit]').val( dataArray.unit ).change();



            $( '.row_entry:last [name=qty_as_number]' ).val( dataArray.quantity );
            $( '.row_entry:last [name=linetotal]' ).text( ns.formatNumber( ( dataArray.qty*dataArray.sellprice ).toFixed(2) ) );
            $( '.row_entry:last [class=x]' ).show();

            if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show();

            $( '.row_entry [name=item_partpicker_name]' ).last().focus();

            $.ajax({
              url: 'ajax/order.php',
              type: 'POST',
              async:false,
              data: { action: "insertRow", data: dataArray },
              success: function (data) {
                //console.log(data);
                $('.row_entry').last().attr( 'id',data );

             },
             error: function(){
                alert( 'Error: new Pos not saved' )
             }

            });

            if (dataArray.instruction)
              $( '.row_entry' ).last().addClass( 'instruction' );

            $( '.row_entry' ).last().clone().appendTo( "#row_table_id" );
            $( '.row_entry' ).last().removeClass( 'instruction' );
            ns.countPos();
            ns.recalc();
            ns.init();

            $( '#newPart_dialog' ).dialog( 'close' );


            $( '.instruction , .instruction div , .instruction :input ' ).css({
              'color' : 'red',
              'background-color' : 'lightblue',

            });
            ns.updateOrder();

         },
         error: function () {
            alert( 'Error: new Part not saved' )
         }

      });

    }

  })

  ns.getQtybyDescription = function ( description ) {

   $.ajax({
       url: 'ajax/order.php?action=getQty&data=' + description,
       type: 'GET',
       async: false,
       success: function ( data ) {
          //console.log(data);
          $( ':focus' ).parents().eq( 3 ).find( '[name=qty_as_number]' ).val( data );
       },
       error: function () {
          alert( 'Error: getQty' )
       }

   });


  }
  //ToDo FORMATIEREN!!!!!
  ns.updateOrder = function(){
    var updateDataJSON = new Array;
    updateDataJSON.push({
        "id": orderID,
        "km_stnd": $( '#milage' ).val() == '' ? 0 : $( '#milage' ).val().replace(/\D/g,''),
        "netamount": $( '#orderTotalNetto' ).val().replace( ',','.' ),
        "amount": $( '#orderTotalBrutto' ).val().replace( ',','.' ),
        "status": $( '#orderstatus' ).val(),
        "finish_time": $( '#finish_time' ).val(),
        "car_status": $( '#car_status' ).val()
      });
      clearTimeout( timer );

      timer = setTimeout( function(){
        ns.updatePosition()
       console.log( 'update Order' );
       $.ajax({
        url: 'ajax/order.php',
        async: false,
        data: { action: "updateOrder", data: updateDataJSON },
        type: "POST",
        success: function(){

        },
        error:  function(){
          alert( 'Update des Auftrages fehlgeschlagen' );
        }

       });
      }, updateTime );

  }




  //Ändert die Artikelnummer bei Artikeltypauswahl
  $( '#dialogPart_typ' ).change( function(){
    var unit;
    var part_type = $( '#dialogPart_typ' ).val();
        if( part_type == 'dimension' ){

            unit='Stck';
        }
        else{
            unit='Std';
            $('#dialogSellPrice').val(ns.formatNumber(parseFloat(customer_hourly_rate).toFixed(2)));
        }




    $( '#dialogSelectUnits' ).val( unit ).change();
    $.ajax({
        url: 'ajax/order.php?action=getArticleNumber&data=' + unit,
        type: 'GET',
        success: function ( data ) {

        var partnumber = $( '#dialogNewArticleNumber' ).val( data.newnumber );

        },
        error: function(){
           alert( 'Error: getArticleNumber' )
        }

     });
  });

  //Ändert den Artikeltyp bei Einheitenauswahl
  $( '#dialogSelectUnits' ).change( function(){
      var unit = $( '#dialogSelectUnits' ).val();

      $.ajax({
        url: 'ajax/order.php?action=getArticleNumber&data=' + unit,
        type: 'GET',
        success: function ( data ) {
          $( '#dialogNewArticleNumber' ).val( data.newnumber );
          var partnumber= data.newnumber;



          /*
          if(partnumber<2000)
            $( '#dialogPart_typ' ).val('dimension').change();
          else if(partnumber>2000)
            $( '#dialogPart_typ' ).val('service').change();
          */
        },
        error: function(){
           alert( 'Error: getArticleNumber' )
        }

    });
  });

  $( '#instructionCheckbox' ).change( function() {
    if($( '#instructionCheckbox' ).val())
        $( '#dialogPart_typ' ).val( 'service' ).change();

  });


  $( document ).on( 'change','.unitselect, .orderupdate', function(){
    if(ready){
    //console.log('change');
    ns.recalc();
    //ns.updatePosition();
    ns.updateOrder();
  }

  });


  $( document ).on( 'keyup','.recalc, .orderupdate, .add_item_input:not(:last)' , function(){
    ns.recalc();
    //ns.updatePosition();
    ns.updateOrder();
  });

  ns.removeOrder = function() {

    $.ajax({
      url: 'ajax/order.php?action=removeOrder&data='+orderID,
      typ: 'GET',
      success: function () {},
      error: function () {alert('ERROR: removeOrder')}
    });

  }

  $("#allMechanicsID").change(function () {

    $("[name=mechanics]").each(function ( index ) {

      $(this).val($("#allMechanicsID").val()).change();


    });

  });


  $("#allStatusID").change(function () {

    $("[name=pos_status]").each(function ( index ) {

      $(this).val($("#allStatusID").val()).change();


    });

  });

  //ToDo: FORMATIEREN!!!!
  ns.updatePosition = function() {

     var updatePosData = new Array;

     $( '.row_entry' ).each(function( index ) {

       var discount = $( this ).find( '[name=discount_as_percent]' ).val().replace( ',', '.' ).replace( /[^\d.-]/g, '' ) / 100;


       if($( this ).find( '[name=partnumber]' ).text()!=""){



          updatePosData.push({

            "order_nr": $( this ).find( '[name=position]' ).text(),
            "pos_description": $( this ).find( '[name=item_partpicker_name]' ).val(),
            "pos_unit": $( this ).find( '[name=unit]' ).val(),
            "pos_qty": $( this ).find( '[name=qty_as_number]' ).val().replace( ',','.' ).replace( /[^\d.-]/g, '' ),
            "pos_price": $( this ).find( '[name=sellprice_as_number]' ).val().replace( ',','.' ).replace( /[^\d.-]/g, '' ),
            "pos_discount": discount,
            "pos_total": $( this ).find( '[name=linetotal]' ).text().replace( ',','.' ),
            "pos_emp": $( this ).find( '[name=mechanics]' ).val(),
            "pos_status": $( this ).find( '[name=pos_status]' ).val(),
            "pos_id": $( this ).attr( 'id' ),
            "parts_id": $( this ).find( '[name=partnumber]' ).attr('part_id'),
            "pos_instruction": $( this ).hasClass( 'instruction' ),
            "longdescription": $( this ).find( '[name=longdescription]' ).val()

          });
       }



     });
     console.log(updatePosData);
     //clearTimeout( timer );
     //timer = setTimeout( function(){
       console.log( 'update Pos' )
       $.ajax({
         url: 'ajax/order.php',
         data: { action: "updatePositions", data: updatePosData },
         type: "POST",
         success: function(){

         },
         error:  function(){
            alert( 'Update der Positionen fehlgeschlagen' );
         }

       });
    //},800);
   }


  });

  //Print Order

});
