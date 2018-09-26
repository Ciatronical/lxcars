namespace( 'kivi.Part', function( ns ){
  'use strict';

  ns.urlParam = function( name ){
    var results = new RegExp( '[\?&]' + name + '=([^&#]*)' ).exec( window.location.href );
    if( results == null );// alert( 'Parameter: "' + name + '" does not exist in "' + window.location.href + '"!' );
    else return decodeURIComponent( results[1] || 0 );
  }

  var id = ns.urlParam( 'id' );
  var owner = ns.urlParam( 'owner' );
  var c_id = ns.urlParam( 'c_id' );
  var previous = ns.urlParam( 'previous' );
  var newOrder = ns.urlParam( 'newOrder' );
  var c_hsn;
  var c_tsn;
  var c_ln;
  var customer_name;
  var rowsToUpdate = [];
  var isNewRow = true;

  var orderID;
  var partID = 0;
  var ready = false;
  var timer;
  var updateTime = 1500;
  //var tax = 0;

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

      if( this.o.fat_set_item && item.id){
        $.ajax({
          url: 'ajax/order.php?action=getPartJSON',
          data: { 'data': item.id },
          success: function( rsp ){
            self.$real.trigger( 'set_item:PartPicker', rsp );
            //nach autocomplete erzeugt neue Position und füllt die aktuell fokussierte Position
            rsp = rsp[0];
            var newPosArray = {};
            if( $( '#ordernumber' ).text() == '0000' ) ns.newOrder();

            $( ':focus' ).parents().eq(3).find( '[name=partnumber]' ).text( rsp.partnumber );
            $( ':focus' ).parents().eq(3).find( '[name=partnumber]' ).attr( 'part_id',rsp.id );

            if( rsp.unit == 'Std' ) //ToDo
              $( ':focus' ).parents().eq(3).find( '[name=sellprice_as_number]' ).val(ns.formatNumber( parseFloat( customer_hourly_rate ).toFixed( 2 ) ) );
            else
              $( ':focus' ).parents().eq(3).find( '[name=sellprice_as_number]' ).val(ns.formatNumber( parseFloat( rsp.sellprice ).toFixed( 2 ) ) );

            var number = parseFloat($( ':focus' ).parents().eq( 2 ).find( '[name=qty_as_number]' ) .val() );
            $( ':focus' ).parents().eq(3).find( '[name=partclassification]' ).text( kivi.t8( rsp.part_type ) );

            if( rsp.instruction ){
              $( '.row_entry:last [name = partclassification]' ).text( kivi.t8("I") );
            }

            $( ':focus' ).parents().eq( 3 ).find( '[name=unit]').val( rsp.unit );
            $( ':focus' ).parents().eq( 3 ).find( '[name=linetotal]').text(ns.formatNumber( parseFloat( rsp.sellprice*number ).toFixed( 2 )) );
            $( ':focus' ).parents().eq( 3 ).find( '[name=item_partpicker_name]' ).val( rsp.description );
            $( ':focus' ).parents().eq( 3 ).find( '[class=x]' ).show();
            $( ':focus' ).parents().eq( 3 ).find( '[class=edit]' ).show();
            $( ':focus ').parents().eq( 3 ).find( '[class=discount100]' ).show();
            ns.getQtybyDescription( item.description );
            newPosArray['position'] = $( ':focus' ).parents().eq( 3 ).find( '[name=position]' ).text();
            newPosArray['parts_id'] =  $( ':focus' ).parents().eq( 3 ).find( '[name=partnumber]' ).attr( 'part_id' );
            newPosArray['order_id'] = orderID;
            newPosArray['description'] = rsp.description;
            newPosArray['sellprice'] = rsp.sellprice;
            newPosArray['ordnumber'] = $( '#ordernumber' ).text();
            newPosArray['qty'] = number;
            newPosArray['unit'] = $( ':focus' ).parents().eq( 3 ).find( '[name=unit]' ).val();
            newPosArray['status'] = $( ':focus' ).parents().eq( 3 ).find( '[name=pos_status]' ).val();
            //var discount = $( ':focus' ).parents().eq( 3 ).find( '[name=discount_as_percent]' ).text(); //Wenn eine neue Posotion eingefügt ist diese
            //newPosArray['discount'] = discount == "" ? 0 : discount;                                    //niemals rabatiert. Oder??
            newPosArray['discount'] = 0;
            newPosArray['linetotal'] = rsp.sellprice * number;
            newPosArray['instruction'] = rsp.instruction;
            if( rsp.instruction )
              $( ':focus' ).parents().eq( 3 ).addClass( 'instruction' );
            
            //save as new position if flag set
            if ( isNewRow ){
              $.ajax({ //new position in table orderitems
                url: 'ajax/order.php',
                type: 'POST',
                async: false,
                data: { action: "insertRow", data: newPosArray },
                success: function( data ){
                  //console.log( data );
                  $( ':focus' ).parents().eq( 3 ).attr( 'id', data );
                },
                error: function(){
                  alert( 'Error: new posion not saved' )
                }
              }); // end ajax
            }

            // is 'Hauptuntersuchung in order position NOT in intructions change HU date in car
            if( ( rsp.description.includes( 'Hauptuntersuchung' ) || rsp.description.includes( 'HU/AU' ) ) && !rsp.instruction ){
              $.ajax({
                url: 'ajax/order.php?action=setHuAuDate&data=' + c_id,
                type: 'GET'
              });
            }
            //console.log( $(':focus').parents().eq(3).is( :first)) );
            $( ':focus' ).parents().eq( 3 ).find( '[name=position]' ).text(); //Macht was??

            $( ':focus' ).parents().eq( 3 ).find( '[name=unit]' ).trigger("change");
            // new row in table
            if( $( ':focus' ).parents().eq( 3 ).is( ':last-child' ) )
              $( ':focus' ).parents().eq( 3 ).clone().appendTo( '#row_table_id' );

            //console.log($('.listrow').filter(':last'));
            if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show(); //dont show sortable < 3 rows
            ns.countPos();// Numbers the positions
            //ns.init();
            ns.recalc();
            ns.init(); // Initializes all partpicker for the autocomplete function after adding a new position
            $( '.listrow' ).filter( ':last' ).find( '[name=item_partpicker_name]' ).focus();
            $( '.listrow' ).filter( ':last' ).removeClass('instruction');
            //sortable update
            $('.ui-sortable').sortable({items: '> tbody:not(.pin)'}); // last position is not sortable
            ns.updateOrder();

            //set flag new row for next call
            isNewRow = true;
          }, // function success
        });
      } //if
      else {
        this.$real.trigger( 'set_item:PartPicker', item );
      }
      this.annotate_state();
    },
    set_multi_items: function(data) {
      this.run_action( this.o.action.set_multi_items, [ data ] );
    }, // my Brake
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
      //console.log( self.ajax_data(self.$dummy.val()));
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

              //console.log(descriptionArray);
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

                    if ( data > 1 ){
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
                modal: true, //i
                title: 'Artikel anlegen',
                zIndex: 10000, //i
                autoOpen: true,
                width: 'auto', //i
                resizable: false, //i
                open: function( event, ui ){
                  $( '.editable' ).prop( 'disabled', false );
                  $( '#dialogDescription' ).val( description_name );
                }
              });

            partID = 0;

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
              //console.log(data);
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
    $( '.row_entry' ).last().find( '[class=edit]' ).hide();
    $( '.row_entry' ).last().find( '[class=discount100]' ).hide();
    lastRow.addClass( 'pin' );

  });


  ns.editPart = function ( clicked ) {
    partID = $( clicked ).parents( "tbody" ).first().find( '[name=partnumber]' ).attr( 'part_id' );

    $( '#newPart_dialog' ).dialog({
      modal: true,
      title: 'Artikel bearbeiten',
      width: 'auto',
      resizable: false,
      open: function( event, ui ){

        //ToDo: ajax Prüfen ob Artikel schon in anderen Aufträgen oder Re existiert
        //wenn ja dann:
        $.ajax({
          url: 'ajax/order.php?action=getPartCount&data=' + partID,
          type: 'GET',
          success: function ( data ){
            //console.log( data.count );
            if( data.count > 1 )
              $( '.editable' ).prop( 'disabled', true );
            else
              $( '.editable' ).prop( 'disabled', false );
            //disable: Typ des Artikels ändern, wenn ANWEISUNG || Änderung zu Anweisung nicht zulassen
            //NOT on new part
            if (data.count > 0){
              if ($( '#dialogPart_typ option:selected' ).text() == "Anweisung") {
                $( '#dialogPart_typ' ).prop( 'disabled', true );
              } else {
                $( '#dialogPart_typ option[value=instruction]' ).prop('disabled', true);
              }
            }
          }
        })
      }
    });

    $('#newPart_dialog').on('keypress', function(e){ //warum mehrfach selectiert?? Besser Punkt-Operator verwenden. ;)
      if (e.which == 13) {
        $('#btnSaveNewPart').trigger("click");
      }
    });

    $( '#dialogDescription' ).val($( clicked ).parents( "tbody" ).first().find( '[name=item_partpicker_name]' ).val());
    $( '#dialogNewArticleNumber' ).val($( clicked ).parents( "tbody" ).first().find( '[name=partnumber]' ).text());
    $( '#dialogSellPrice' ).val($( clicked ).parents( "tbody" ).first().find( '[name=sellprice_as_number]' ).val());
    $( '#dialogPart_typ' ).val($( clicked ).parents( "tbody" ).first().find( '[name=partclassification]' ).text() == "A" ? "instruction" : ( $( clicked ).parents( "tbody" ).first().find( '[name=partclassification]' ).text() == "D" ? "service" : "dimension" ) );
    $( '#dialogSelectUnits' ).val($( clicked ).parents( "tbody" ).first().find( '[name=unit]' ).val());
  }


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
  //New Order
  ns.newOrder = function () {
    $( '#employee' ).text( kivi.myconfig.name );
              $( '#milage' ).val( '0' );
              //console.log(c_hsn);
              $.ajax({
                  url: 'ajax/order.php',
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
                        $( '#orderTotalNetto' ).text( data.netamount );
                        $( '#orderTotalBrutto' ).text( data.amount );
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
                        c_ln = data.c_ln;
                        customer_name = data.customer_name;
                        c_hsn = data.c_2;
                        c_tsn = data.c_3;
                        //$( '#headline' ).html( '<b>Auftrag ' + data[1] + ' ' + data[2] + ' ' + data[3] + ' von ' + data.customer_name + '</b>' );

                        if( data[1] == undefined )
                          $( '#headline' ).html( '<b>Auftrag ' + data.c_ln + ' von ' + data.customer_name + '</b>' );
                        else
                          $( '#headline' ).html( '<b>Auftrag ' + data[1] + ' ' + data[2] + ' ' + data[3] + ' von ' + data.customer_name + '</b>' );

                    }
                  })


                     orderID = newOrderID;
                  },
                  error:  function(){ alert( "Holen der Auftrags-Nr fehlgeschlagen!" ); }
              })

}

  $( document ).ready( function(){

    var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
    var baseUrl = kivi_global.baseurl;
    $('[name=item_partpicker_name]').focus();

    $.ajax({ //no DB query in order.php
      url: 'ajax/order.php?action=getUsersFromGroup&data='+"Werkstatt",
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){
          $( '[name = mechanics], [name = allMechanics]' ).append( $( '<option class="opt mech__' + item.name + '" value="'+item.name + '">' + item.name + '</option>' ) );
        })
      },
      error:  function(){
        alert( "Ajaxerror getMechnics()!" );
      }
    })

    var unitsType = new Array;
    //get metadata (= the same for ALL orders) -> return is 3dim-Array of objects in JSON
    $.ajax({
      url: 'ajax/order.php?action=getMetadata',
      type: 'GET',
      success: function( data ){
        //split data into 4 arrays -> iteration without splitting would be possible but has no performance gains and is less maintanable and readable
        var units = data[0]['units'];
        var taxzones = data[1]['taxzones'];
        var accountinggroups = data[2]['accountinggroups'];
        var customerhourlyrate = data[3]['customerhourlyrate'];

        //processing of getUnits
        $.each( units, function( index,item ){
          unitsType[item.name] = item.type;
          $( '[name=unit]' ).append( '<option class="opt unit__' + item.name + '" value="' + item.name + '">' + item.name + '</option>' );
        });

        //processing of getTaxzones
        $.each( taxzones, function( index, item ){
          //console.log(item);
          if (index == 0)
            $( '[name = taxzones]' ).append( $( '<option value = "' + item.id + '" selected = "true">' + item.description + '</option>' ) );
          else
            $( '[name = taxzones]' ).append( $( '<option value = "' + item.id + '" >' + item.description + '</option>' ) );
          });

        //processing of getAccountingGroups
        $.each( accountinggroups, function( index, item ){
          $( '#accountingGroups' ).append( $( '<option id="' + item.id + '" value="' + item.description + '">' + item.description + '</option>' ) );
        });

        //processing of customer_hourly_rate
        customer_hourly_rate = customerhourlyrate[0]['customer_hourly_rate'];
      },
      error: function (data){
        console.log( "Error getMetadata: "+data );
      }
    });

  //DateTimePicker
  function AddButton( input ){
    setTimeout( function(){  //Timeout to force this handler to load after pageLoad for shorter initial loading time
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

  $( "#kbaToCoparts" ).button({
    label: 'Coparts'
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = 'lxcars://kba' + c_hsn + c_tsn + '___' + c_ln + '___' + customer_name;
      customer_name = data.customer_name;;
    return false;
  });


  $( '#invoice' ).button({
    label: kivi.t8( 'invoice' )
  }).css({
    'margin':'5px'
  }).click( function(){
    alert( 'currently not implemented' );
    //window.location = baseUrl + '/crm/lxcars/' + previous + '?c_id=' + c_id + '&task=3';
    return false;
  });

  $( '#deleteOrder' ).button({
    label: kivi.t8( 'delete Order' )
  }).click( function(){
    $( "#confirmDialog" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      title: 'Delete Order!',
      open: function() {
        $( this ).html( 'Do you want to delete this order irretrievably?' );
      },
      buttons: [{
        text: "Delete Order",
        click: function(){
          $.ajax({
            url: 'ajax/order.php',
            data: { action: 'removeOrder', data: { orderID: id } },
            type: 'POST',
            success: function(){
              window.location = baseUrl + '/crm/lxcars/orderlist.phtml?owner=' + owner + '&c_id=' + c_id + '&c_hsn=' + c_hsn + '&c_tsn=' + c_tsn;
            },
            error: function(){
              alert( 'Error in function removeOrder()!' );
            }
          }) //ajax
          $( this ).dialog( "close" );
        } //click
      }, //button
      {
        text: "Cancel",
        click: function(){
          $( this ).dialog( "close" );
        }
      }]
    }); //dialog
    return false;
  });

  ns.recalc = function(){
    //cache rowsToUpdate for increased responsiveness
    var cachedRowsToUpdate = rowsToUpdate;
    rowsToUpdate = [];
    $( 'tbody .listrow' ).each( function(item){
      //find row number
      var rowNumber = parseInt($(this).find("[name=position]:first").html());
      //calcPrice only if row number in rowsToUpdate[] or if array's empty
      if( cachedRowsToUpdate.indexOf( rowNumber ) != -1 || cachedRowsToUpdate.length < 1 ){
        var  calculation = $( this ).find( "[name=sellprice_as_number]:first" ).val().toString().replace( /,/g, '.' ); // "/,/g" == replaceAll()
        if ( calculation.includes( '+' ) || calculation.includes( '-' ) || calculation.includes( '*' ) || calculation.includes( '/' ) ) {
          var result = eval( calculation );
          $( this ).find( "[name=sellprice_as_number]:first" ).val( result.toFixed( 2 ).toString().replace( '.',',' ) );
        }
        var number = parseFloat( $( this ).find( '[name = qty_as_number]' ).val().replace( ',' , '.' ).replace( '' , 0 ) );
        var price = parseFloat( $( this ).find( '[name = sellprice_as_number]' ).val().replace( ',' , '.' ).replace( '', 0) );
        var discount = parseFloat( $( this ).find( '[name = discount_as_percent]' ).val().replace( '' , 0 ) ) / 100;
        $( this ).find( '[name = linetotal]' ).text( ns.formatNumber( parseFloat( price * number -  price * number * discount ).toFixed( 2 ) ) );
      }
    });
    // linetotals, taxes must be calculated in each line!!!
    var linetotal = 0;
    var linetotal_tax = 0; //with tax
    var linetotal_sum = 0;
    var linetotal_tax_sum = 0;
    $( '[name=linetotal]:not( .linetotal_instruction )' ).each( function( item ){   //ToDo: Hier wird leider die letzte Zeile mit selektiert
      linetotal = parseFloat( $( this ).text().replace( ',' , '.' ) );              //Wenn die Eltern nicht sortable sind oder mit last
      linetotal_tax = linetotal * ( 1 + parseFloat( $( this ).attr( 'data-tax' ) ) )//Performance-Einfluss extrem gering!
      linetotal_sum += linetotal;
      linetotal_tax_sum += linetotal_tax;
    });
    $( '#orderTotalNetto' ).text( ns.formatNumber( linetotal_sum.toFixed( 2 ) ) );
    $( '#orderTotalBrutto' ).text( ns.formatNumber( linetotal_tax_sum.toFixed( 2 ) ) );
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

      var car = data.c_id;
      if( data.km_stnd == null ){
        data.km_stnd = '0';
      }
      if ( data.car_status == null ) {
        data.car_status = 'Auto hier';
      }
      $( '#orderTotalNetto' ).text(parseFloat(data.netamount).toFixed(2));
      $( '#orderTotalBrutto' ).text(parseFloat(data.amount).toFixed(2));
      $( '#ordernumber' ).text( data.ordnumber );
      $( '#name' ).text( data.customer_name );
      $( '#employee' ).text( kivi.myconfig.name );
      $( '#date' ).text( data.transdate );
      $( '#finish_time' ).val( data.finish_time );
      $( '#milage' ).val( data.km_stnd );
      $( '#licenseplate' ).val( data.c_ln );
      $( '#orderstatus' ).val( data.order_status ).change();
      $( '#car_status' ).val( data.car_status ).change();
      $( '.isInternalOrder').prop('checked', data.internalorder);
      $( '#mtime' ).text(data.mtime);
      c_ln = data.c_ln;
      customer_name = data.customer_name;
      c_hsn = data.c_2;
      c_tsn = data.c_3;

      if( data[1] == undefined )
        $( '#headline' ).html( '<b>Auftrag ' + data.c_ln + ' von ' + data.customer_name + '</b>' );
      else
        $( '#headline' ).html( '<b>Auftrag ' + data[1] + ' ' + data[2] + ' ' + data[3] + ' von ' + data.customer_name + '</b>' );
      orderID = data.oe_id;

      //Get Positions
      if( newOrder != 1 ){//= data.amount!=null -> positions are not get on new orders (total = 0)
        $.ajax({
          url: 'ajax/order.php?action=getPositions&data=' + orderID,
          type: 'GET',
          success: function ( data ){
            $.each( data, function( index, item ){
              $( '.row_entry [name=partnumber]' ).last().text( item.partnumber );
              $( '.row_entry [name=partclassification]' ).last().text( kivi.t8( item.part_type ) );
              if (item.instruction) {
                $( '.row_entry [name=partclassification]' ).last().text( kivi.t8( 'I') );
                $( '.row_entry [name=linetotal]' ).last().addClass('linetotal_instruction');
              }
              $( '.row_entry').last().attr( 'id', item.id );
              $( '.row_entry [name=partnumber]').last().attr( 'part_id', item.parts_id );
              $( '.row_entry [name=position]').last().text( item.position );
              $( '.row_entry [name=item_partpicker_name]').last().val( item.description );
              $( '.row_entry [name=mechanics]').last().val( item.u_id );
              $( '.row_entry [name=sellprice_as_number]').last().val( ns.formatNumber( item.sellprice.toFixed( 2 ) ) );
              $( '.row_entry [name=unit]' ).last().val( item.unit ).change();
              $( '.row_entry [name=pos_status]' ).last().val( item.status ).change();
              $( '.row_entry [name=qty_as_number]' ).last().val( ns.formatNumber( item.qty.toFixed( 2 ) ) );
              //change qty_as_number to number field if unit is pieces
              if ($( '.row_entry [name=unit]' ).last().find( 'option:selected' ).first().text() == "Stck"){
                var qty_field = $( '.row_entry [name=qty_as_number]' ).last(); //store field for increased performance: not selecting everytime
                var width = qty_field.width(); //store width because type=number fields are bigger by default
                var value = qty_field.val(); //value gets deleted on field change
                qty_field.attr( 'type',  'number');
                qty_field.width( width ); //restore width
                if ( value.includes(',') ) //cut decimals ONLY if value contains ',', otherwise whole string is nulled
                  value = value.substring(0, value.indexOf(','));
                qty_field.val( value ); //restore value
                if ( qty_field.val() == 0) //mark in red if value=0 (case: 0,5 hours are changed to pieces: 0,5->0)
                  qty_field.css( 'color', 'red' );
                else
                  qty_field.css( 'color', 'black' ); //undo possible red marking in previous row
              } else $( '.row_entry [name=qty_as_number]' ).last().css( 'color', 'black' ); //undo possible red marking in previous row
              $( '.row_entry [name=discount_as_percent]' ).last().val( ns.formatNumber( ( item.discount * 100 ).toFixed( 0 ) ) );
              $( '.row_entry [name=linetotal]' ).last().attr( 'data-tax', item.rate );
              $( '.row_entry [name=linetotal]' ).last().text( ns.formatNumber( (item.qty * item.sellprice - item.qty * item.sellprice * item.discount / 100 ).toFixed( 2 ) ) );
              $( '.row_entry [name=longdescription]' ).last().val( item.longdescription ).change();
              $( '.row_entry [class=x]' ).last().show();
              $( '.row_entry [class=edit]' ).last().show();
              $( '.row_entry [class=discount100]' ).last().show();
              //change 100%Discount button value if 100% already set
              if( $( '.row_entry [name=discount_as_percent]' ).last().val() == "100,00" )
                $( '.row_entry [name=discount100button]' ).last().val( "0%" );
              else
                $( '.row_entry [name=discount100button]' ).last().val( "100%" );

              if( item.instruction )
                $( '.row_entry' ).last().addClass( 'instruction' );

              $( '.row_entry' ).last().clone().appendTo( "#row_table_id" );
              $( '.row_entry' ).last().removeClass( 'instruction' );
              $( '.row_entry [name=linetotal]' ).last().removeClass('linetotal_instruction');
            }); //each data
            if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show();
            ns.countPos();
            ns.recalc();

            ns.init();
            ready = true;
            $( '.listrow' ).filter( ':last' ).find( '[name=item_partpicker_name]' ).focus();
            $( '.ui-sortable' ).sortable( {items: '> tbody:not(.pin)'} );
          },
          error: function () {
              alert( "error: getPositions fehlgeschlagen" );
          }
        });
      }
    }
  });

  $( '#btnSaveNewPart' ).click( function(){
    if( $( '#ordernumber' ).text() == '0000' )
      ns.newOrder();

    if ( $( '#dialogNewArticleNumber' ).val() != '' ){
     var dataArray = {};
     dataArray['partnumber'] = $( '#dialogNewArticleNumber' ).val();
     dataArray['description'] = $( '#dialogDescription' ).val();
     dataArray['unit'] = $( '#dialogSelectUnits' ).val();
     dataArray['listprice'] = $( '#dialogBuyPrice' ).val();
     dataArray['sellprice'] = $( '#dialogSellPrice' ).val().replace("," , ".");
     dataArray['buchungsgruppen_id'] = $( '#accountingGroups option:selected' ).attr( 'id' );
     dataArray['quantity'] = $( "#quantity" ).val();

     var part_type = $( '#dialogPart_typ' ).val();

     if(part_type == "instruction"){
       dataArray['instruction'] = true;
     }else{
       dataArray['instruction'] = false;
     }

     dataArray['order_id'] = orderID;
     dataArray['part_type'] = unitsType[$( '#dialogSelectUnits' ).val()];
     if( dataArray['part_type'] == 'dimension' )
       dataArray['part_type'] = 'part';
     if( dataArray['part_type'] == 'instruction' )
       dataArray['part_type'] = 'service';

     dataArray['position'] =  $( '.row_entry' ).last().find( '[name=position]' ).text();
     dataArray['partID'] = partID;

     $.ajax({
         url: 'ajax/order.php',
         type: 'POST',
         data: { action: partID == 0 ? "newPart" : "updatePart", data: dataArray },
         success: function( data ){
            if( partID == 0 ){ //partID 0 means new part
              $( '.row_entry:last [name=partnumber]' ).text( dataArray.partnumber );
              $( '.row_entry:last [name=partclassification]' ).text( kivi.t8( dataArray.part_type ) );
              if( dataArray['instruction'] ) {
                $( '.row_entry:last [name=linetotal]' ).addClass( 'linetotal_instruction' );
                $( '.row_entry:last [name=partclassification]' ).text( kivi.t8( "I" ) );
              }

              $( '.row_entry:last').attr( 'id', data );
              $( '.row_entry:last [name=partnumber]' ).attr( 'part_id', data );
              $( '.row_entry:last [name=position]').text( dataArray.position );
              $( '.row_entry:last [name=item_partpicker_name]' ).val( dataArray.description );
              $( '.row_entry:last [name=sellprice_as_number]' ).val( ns.formatNumber( dataArray.sellprice ) );
              $( '.row_entry:last [name=unit]').val( dataArray.unit ).change();
              $( '.row_entry:last [name=qty_as_number]' ).val( dataArray.quantity );
              $( '.row_entry:last [name=linetotal]' ).text( ns.formatNumber( ( dataArray.qty*dataArray.sellprice ).toFixed( 2 ) ) );
              $( '.row_entry:last [class=x]' ).show();
              $( '.row_entry:last [class=edit]' ).show();
              $( '.row_entry:last [class=discount100]' ).show();
              //change 100%Discount button value if 100% already set
              if ($( '.row_entry:last [class=discaspercent]' ).val() == "100,00") {
                $( '.row_entry:last [class=discount100]' ).val("0%");
              }

              if( $( '#row_table_id tr' ).length > 3 ) $( '.dragdrop' ).show();

              $( '.row_entry [name=item_partpicker_name]' ).last().focus();

              ns.newLine( dataArray );
            }
            ns.countPos();
            ns.recalc();

            ns.init();

            $( '#newPart_dialog' ).dialog( 'close' );

            $( document.activeElement ).parents( "tbody" ).first().find( "[name = sellprice_as_number]" ).val( dataArray.sellprice );
            $( document.activeElement ).parents( "tbody" ).first().find( "[name = item_partpicker_name]" ).val( dataArray.description );
            $( document.activeElement ).parents( "tbody" ).first().find( "[name = qty_as_number]" ).val( dataArray.quantity );
            $( document.activeElement ).parents( "tbody" ).first().find( "[name = unit]" ).val( dataArray.unit );
            $( document.activeElement ).parents( "tbody" ).first().find( "[name = partnumber]" ).text( dataArray.partnumber );
            $( document.activeElement ).parents( "tbody" ).first().find( "[name = partclassification]" ).text( kivi.t8( dataArray.part_type ) );
            if( dataArray['instruction'] ){
             $( document.activeElement ).parents( "tbody" ).first().find( "[name = partclassification]" ).text( kivi.t8( "I" ) );
             $( document.activeElement ).parents( "tbody" ).first().addClass( 'instruction' );

            }else {
              $( document.activeElement ).parents( "tbody" ).first().removeClass( 'instruction' );
            }

            ns.updateOrder();
            $( '#newPart_dialog' ).dialog( 'close' );
         },
         error: function () {
            alert( 'Error: new Part not saved' )
         }
      });
    }
  }) //#btnSaveNewPart

  ns.newLine = function( dataArray ){
    //console.log("NewLine");
    if( partID == 0 ){
      $.ajax({
        url: 'ajax/order.php',
        type: 'POST',
        async:false,
        data: { action: "insertRow", data: dataArray },
        success: function (data) {
          //console.log(data);
          $( '.row_entry' ).last().attr( 'id',data );
        },
        error: function(){
          alert( 'Error: new Pos not saved' )
        }
      });

      if( dataArray.instruction )
        $( '.row_entry' ).last().addClass( 'instruction' );
      $( '.row_entry' ).last().clone().appendTo( "#row_table_id" );
      $( '.row_entry' ).last().removeClass( 'instruction' );
      $( '.row_entry' ).last().find('[name=item_partpicker_name]').focus();

      clearTimeout( timer );
      timer = setTimeout( function(){
        ns.updateOrder();
      }, updateTime );
    }
  }

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
    ns.updatePosition();
    var updateDataJSON = new Array;
    updateDataJSON.push({
        "id": orderID,
        "km_stnd": $( '#milage' ).val() == '' ? 0 : $( '#milage' ).val().replace(/\D/g,''),
        "netamount": $( '#orderTotalNetto' ).text().replace( ',' , '.' ).replace( '', '0' ),
        "amount": $( '#orderTotalBrutto' ).text().replace( ',' , '.' ).replace( '', '0' ),
        "status": $( '#orderstatus' ).val(),
        "finish_time": $( '#finish_time' ).val(),
        "car_status": $( '#car_status' ).val(),
        "internalorder": ($('.isInternalOrder').is(':checked'))
      });

      $.ajax({
        url: 'ajax/order.php',
        async: true,
        data: { action: "updateOrder", data: updateDataJSON },
        type: "POST",
        success: function(){
        },
        error:  function(){
          alert( 'Update des Auftrages fehlgeschlagen' );
        }
      });
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
            $( '#dialogSellPrice' ).val(ns.formatNumber( parseFloat( customer_hourly_rate ).toFixed( 2 )) );
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


  $( document ).on( 'change ','select, .hasDatepicker', function(){
    if( ready ){
      ns.updateOrder();
    }
  });

  $( document ).on ( 'focus ','.recalc', function(){
    $( this ).trigger("keyup");
  })

  $( document ).on( 'keyup ','.recalc', function(){
    //ARRAY für rows in need of recalculation
    //if array's empty, recalculate all
    //get row number, add to array if not yet in it
    var rowNumber = parseInt($(this).closest("tr").find("[name=position]:first").html());
    if (rowsToUpdate.indexOf(rowNumber) == -1) {
      rowsToUpdate.push(rowNumber);
    }

    clearTimeout( timer );
     timer = setTimeout( function(){
        ns.recalc();
        ns.updateOrder();
    },   updateTime );
  });

  $(document).on('click', 'input, textarea', function(e){
    if ($(this).hasClass("isInternalOrder")) {
      ns.updateOrder();
      //use trigger of orderupdate to use timer
      //$('.orderupdate').trigger('keyup');
    } /*else {
      $(this)
          .on('mouseup', function(){
              $(this).select();
              return false;
          })
          .select();
        }*/
  });

  $(document).on('click', '.discount100', function(e){
    var percentfield = $(this).closest('tr').find('.discaspercent');
    if (percentfield.val() == "100" || percentfield.val() == "100,00")
    {
      percentfield.val("0,00");
      $(this).val("100%");
    }
    else{
      percentfield.val("100");
      $(this).val("0%");
    }
    percentfield.trigger("keyup");
  });

  //if unit is selected as 'pieces', only allow whole numbers in quantity
  $(document).on('change', '.unitselect', function(e){
    var qty_field = $( this ).closest( 'tr' ).find( '[name=qty_as_number]' ).first(); //store field for increased performance: not selecting everytime
    var value = qty_field.val(); //value gets deleted on field change
    var width = qty_field.width(); //store width because type=number fields are bigger by default
    if ($( this ).find( 'option:selected' ).first().text() == "Stck"){
      qty_field.attr( 'type',  'number');
      qty_field.width( width );
      if ( value.includes(',') ) //cut decimals ONLY if value contains ',', otherwise whole string is nulled
        value = value.substring(0, value.indexOf(','));
      qty_field.val( value ); //restore value
      if ( qty_field.val() == 0)
        qty_field.css( 'color', 'red' ); //mark in red if value=0 (case: 0,5 hours are changed to pieces: 0,5->0)
      else
        qty_field.css( 'color', 'black' ); //undo possible red marking if value no longer 0
    }
    else{
      qty_field.attr( 'type',  'text');
      qty_field.width( width );
      qty_field.val( value );
      qty_field.css( 'color', 'black' ); //undo possible red marking if type no longer pieces
    }
  });

  //if quantity is set to 0, mark red; otherwise undo possible red marking
  $(document).on('change', '[name=qty_as_number]', function(e){
    if ($( this ).val() == 0)
      $( this ).css( 'color', 'red' );
    else
      $( this ).css( 'color', 'black' );
  });

  $(document).on('keyup', '.orderupdate, .add_item_input:not(:last)' , function(e){
    //set flag: is not new row, because is not the last ("new position") row
    //used by: part picker function
    isNewRow = false;
    //DON'T update order, if editing of part is still in progress
    //determined by: input field has focus OR enter is not pressed
    if ($( this ).isFocused = false){
      clearTimeout( timer );
      timer = setTimeout( function(){
        ns.updateOrder();
      }, updateTime );
    }
    //if enter is pressed: don't wait, update immediately
    //if selected field is longdescription, prevent new line if SHIFT is not pressed (prevent by removing new line chars)
    if (e.which == KEY.ENTER) {
      if ($( this ).attr( "name" ) == "longdescription" &&
          !e.shiftKey) {
            $( this ).val( $(this).val().replace(/\r?\n|\r/g, "") );
          }
      ns.updateOrder();
    }
  });

  ns.removeOrder = function() {
    $.ajax({
      url: 'ajax/order.php?action=removeOrder&data=' + orderID,
      typ: 'GET',
      success: function () {},
      error: function () {alert('ERROR: removeOrder')}
    });
  }

  $( "#allMechanicsID" ).change(function () {
    $( "[name=mechanics]" ).val( $("#allMechanicsID").val() ).change();
  });

  $( "#allStatusID" ).change(function () {
    $( "[name=pos_status]" ).val( $( "#allStatusID" ).val() ).change();
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
            "pos_qty": $( this ).find( '[name=qty_as_number]' ).val().replace( ',' , '.' ).replace( /[^\d.-]/g, '' ).replace( '' , 0 ),
            "pos_price": $( this ).find( '[name=sellprice_as_number]' ).val().replace( ',','.' ).replace( /[^\d.-]/g, '' ).replace( '' , 0 ),
            "pos_discount": discount,
            "pos_total": $( this ).find( '[name=linetotal]' ).text().replace( ',' , '.' ),
            "pos_emp": $( this ).find( '[name=mechanics]' ).val(),
            "pos_status": $( this ).find( '[name=pos_status]' ).val(),
            "pos_id": $( this ).attr( 'id' ),
            "parts_id": $( this ).find( '[name=partnumber]' ).attr('part_id' ),
            "pos_instruction": $( this ).hasClass( 'instruction' ),
            "longdescription": $( this ).find( '[name=longdescription]' ).val()

          });
       }
     });
      $.ajax({
        url: 'ajax/order.php',
        data: { action: "updatePositions", data: updatePosData },
        async: true,
        type: "POST",
        success: function(){
        },
        error:  function(){
           alert( 'Update der Positionen fehlgeschlagen' );
        }
      });
   }
 });
});
