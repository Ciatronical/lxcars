namespace('kivi.Part', function(ns) {
  'use strict';

  var orderID;
  var ready=false;
  var timer;

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


      if (this.o.fat_set_item && item.id) {
        $.ajax({
          url: '../../controller.pl?action=Part/show.json',
          data: { 'part.id': item.id },
          success: function(rsp) {
            self.$real.trigger('set_item:PartPicker', rsp);

            //nach autocomplete erzeugt neue Position und füllt die aktuell fokussierte Position

            var newPosArray={};


            $(':focus').parents().eq(3).find('[name=partnumber]').text(rsp.partnumber);

            $(':focus').parents().eq(3).find('[name=partnumber]').attr('part_id',rsp.id);
            $(':focus').parents().eq(3).find('[name=sellprice_as_number]').val(parseFloat(rsp.sellprice).toFixed(2));
            var number=parseFloat($(':focus').parents().eq(2).find('[name=qty_as_number]').val());
            $(':focus').parents().eq(3).find('[name=partclassification]').text(rsp.part_type);
            $(':focus').parents().eq(3).find('[name=unit]').val(rsp.unit);
            $(':focus').parents().eq(3).find('[name=linetotal]').text(parseFloat(rsp.sellprice*number).toFixed(2));
            $(':focus').parents().eq(3).find('[name=item_partpicker_name]').val(rsp.description);
            $(':focus').parents().eq(3).find( '[class=x]' ).show();
            //$('.autocomplete').removeClass('part_autocomplete');

            //console.log($(':focus').parent());

            newPosArray['position'] = $(':focus').parents().eq(3).find('[name=position]').text();
            newPosArray['parts_id'] =  $(':focus').parents().eq(3).find('[name=partnumber]').attr('part_id');
            newPosArray['order_id'] = orderID;
            newPosArray['description'] = rsp.description;
            newPosArray['sellprice'] = rsp.sellprice;
            newPosArray['ordnumber'] = $('#ordernumber').text();
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

              $.ajax({
                 url: 'ajax/order.php?action=getPartJSON&data='+rsp.id,
                 type: 'GET',
                 async:false,

                 success: function (data) {
                    console.log(data[0].instruction);
                    newPosArray['instruction']=data[0].instruction;

                 },
                 error: function () {
                    alert( 'Error: new Pos not saved' )
                 }

            });

            $.ajax({
                 url: 'ajax/order.php',
                 type: 'POST',
                 async:false,
                 data: { action: "insertRow", data: newPosArray },
                 success: function (data) {
                    //console.log(data);
                    $(':focus').parents().eq(3).attr('id',data);

                 },
                 error: function () {
                    alert( 'Error: new Pos not saved' )
                 }

            });

            //erzeugt neue Position
            //console.log( $(':focus').parents().eq(3).is( :first)) );
            $(':focus').parents().eq(3).find('[name=position]').text();

            if($(':focus').parents().eq(3).is(':last-child'))
            $(':focus').parents().eq(3).clone().appendTo('#row_table_id');


            //console.log($('.listrow').filter(':last'));
            if( $('#row_table_id tr').length > 3 ) $('.dragdrop').show(); //dont show sortable < 3 rows
            ns.countPos();//nummeriert die positionen
            ns.init();
            ns.recalc();
            ns.updateOrder();



            ns.init();//Initialisiert alle partpicker für die autocomplete function nachdem eine neue Position hinzugefügt wurde
            $('.listrow').filter(':last').find('[name=item_partpicker_name]').focus();

            //sortable update
            $('.ui-sortable').sortable({items: '> tbody:not(.pin)'}); //letzte Position ist nicht Sortable

            //insertRow(rsp);//insert Position oder Instruction
            //alert( "Siehe da! Partnumber: " + rsp.partnumber + " Description: " + rsp.description );


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
      $.ajax({
        url: '../../controller.pl?action=Part/ajax_autocomplete',
        dataType: "json",
        data: $.extend( self.ajax_data(self.$dummy.val()), { prefer_exact: 1 } ),
        success: function (data) {

          if (data.length == 1) {
            self.set_item(data[0]);
            if (callbacks && callbacks.match_one) self.run_action(callbacks.match_one, [ data[0] ]);

          } else if (data.length > 1) {
            self.state = self.STATES.UNDEFINED;
            if (callbacks && callbacks.match_many) self.run_action(callbacks.match_many, [ data ]);
          } else {

              var description_name=$(":focus").parents().eq(3).find("[name=item_partpicker_name]").val();
              $('#newPart_dialog').dialog({
                    modal: true,
                    title: 'Artikel anlegen',
                    zIndex: 10000,
                    autoOpen: true,
                    width: 'auto',
                    resizable: false,
                    create: function( event, ui ){
                        $( '#instructionCheckbox' ).checkboxradio();
                        if( $('#dialogDescription').val().length >=18 ) $( '#instructionCheckbox' ).prop( "checked", true ).checkboxradio( 'refresh' );

                }

            });

            $('#dialogDescription').val(description_name);

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
            url:      '../../controller.pl?action=Part/ajax_autocomplete',
            dataType: "json",
            data:     self.ajax_data(req.term),
            success:  function (data){ rsp(data) }
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
      });
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

    $('.listrow').each(function () {
      posID++;
      $(this).find('[name=position]').text(posID);
      $(this).removeClass('pin');
    });

    var lastRow = $('.listrow').filter(':last');
    lastRow.find('[name=item_partpicker_name]').val('');
    lastRow.find('[name=partnumber]').text('');
    lastRow.find('[name=sellprice_as_number]').val('0.00');
    lastRow.find('[name=linetotal]').text('0.00');
    lastRow.find( '[name=partclassification]' ).text( '' );
    lastRow.find( '[name=qty_as_number]' ).val( '1' );
    lastRow.removeAttr('id');
    lastRow.find( 'img' ).hide();
    $('.row_entry').last().find('[class=x]').hide();
    lastRow.addClass('pin');

  });

  ns.insertRow=(function( pos ) {

    var posObject = {};
    var posArray = $( '.row_entry listrow listrow' ).children().children( 'td' );
    //console.log(posArray.length);
    $.each( posArray, function( index, item ){
        posObject[item.name] = item.value;
    });
    posObject['order_id'] = orderID;
    posObject['description'] = $( '.newOrderPos' ).children().children( '.elem' ).val();
    posObject['instruction'] = pos;
    // Insert
    $.ajax({
        url: 'ajax/order.php',
        data: { action: "insertRow", data: posObject },
        type: "POST",
        success: function( result ){
            last_pos_id = result;
            $('.newOrderPos').children('.posID').text( result );
            ns.updatePosition();
                //$('#pos__' + ( i +  1 ) + '__elem__9').text(result);
        },
        error:   function(){
            alert( 'Insert der Daten fehlgeschlagen!');
        }
    });
  });

  ns.delete_order_item_row = function(clicked) {
    var row = $(clicked).parents("tbody").first();
    var id =$(row).attr('id');
    //console.log(id);
    $.ajax({
      url: 'ajax/order.php?action=delPosition&data='+id,
      type: 'GET',
      success: function () {
        $(row).remove();
        ns.recalc();
        ns.updateOrder();

      },
      error: function () {
        alert('ERROR: Could not delete Position');
      }

    });

  };


  $( document ).ready( function(){

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

    var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
    var baseUrl = kivi_global.baseurl;
    $('[name=item_partpicker_name]').focus();

    //alert( kivi.t8( '_part' ) );


    $.ajax({
      url: 'ajax/order.php?action=getMechanics',
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){
        //console.log(item);
          $( '[name = mechanics]' ).append( $( '<option class="opt mech__' + item.name + '" value="'+item.name + '">' + item.name + '</option>' ) );
        })
      },
      error:  function(){
        alert( "Ajaxerror getMechnics()!");
      }
    })


    var accountingGroupsID;
    $.ajax({
      url: 'ajax/order.php?action=getAccountingGroups',
      type: 'GET',
      success: function( data ){
        $.each( data, function( index, item ){
          //console.log(item);accountingGroups
          $( '#accountingGroups' ).append( $( '<option id="' + item.id + '" value="' + item.description + '">' + item.description + '</option>' ) );
          accountingGroupsID=item.id;
          if( item.id == 859 ){ //ToDo: 859????
            $( '#accountingGroups' ).children( '#'+item.id ).attr( 'selected', 'selected' );

          }
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

          unitsType[item.name]=item.type;
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
      var btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button"> Wartet</button>');
      btn.appendTo( buttonPane );
      btn.bind( "click", function(){
          $( "#finish_time" ).val("Kunde wartet! SOFORT anfangen!");
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
    timeSuffix: ' Uhr',
    timeText: 'Zeit',
    hourText: 'Stunde',
    closeText: 'Fertig',
    currentText: 'Jetzt'
  });

  $( "#backToCRM" ).button({
    label: "CRM"
  }).css({
    'margin':'5px'
  }).click( function(){
    location.href = baseUrl + '/crm/firma1.php?Q=C&id=' + owner;
    return false;
  });

  $( "#backToOrderList" ).button({
    label: "Back to Orderlist"
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = baseUrl + '/crm/lxcars/' + previous + '?owner=' + owner + '&c_id=' + c_id;
    return false;
  });

  $( "#backToCar" ).button({
    label: "Back to Car"
  }).css({
    'margin':'5px'
  }).click( function(){
    window.location = baseUrl + '/crm/lxcars/lxcmain.php?owner=' + owner + '&c_id=' + c_id + '&task=3';
    return false;
  });
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
      if (data.car_status == null) {
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
      //ow = data.customer_id;
      orderID = data.oe_id;

      //Get Position
      //console.log(data.amount);
      if(data.amount!=null){//data.amount!=null Bei neuen Aufträgen werden die Positionen nicht abgefragt(Wenn Gesamtbetrag null)
        $.ajax({
          url: 'ajax/order.php?action=getPositions&data='+orderID,
          type: 'GET',
          success: function (data) {
            //console.log(data);

            $.each( data.reverse(), function( index, item ){

              $.ajax({
                 url: 'ajax/order.php?action=getPartJSON&data='+item.parts_id,
                 type: "GET",
                 async:false,
                 success: function(data){
                   //console.log(data[0]);
                    $('.row_entry').last().find('[name=partnumber]').text(data[0].partnumber);
                    $('.row_entry').last().find('[name=partclassification]').text(data[0].part_type);

                 },
                 error:  function(){
                    //alert( 'getParts fehlgeschlagen' );
                 }

              });

              $('.row_entry').last().attr('id', item.id);
              $('.row_entry').last().find('[name=partnumber]').attr('part_id', item.parts_id);
              $('.row_entry').last().find('[name=position]').text(item.position);
              $('.row_entry').last().find('[name=item_partpicker_name]').val(item.description);
              $('.row_entry').last().find('[name=mechanics]').val(item.u_id);
              $('.row_entry').last().find('[name=sellprice_as_number]').val(item.sellprice.toFixed(2));
              $('.row_entry').last().find('[name=unit]').val(item.unit).change();
              $('.row_entry').last().find('[name=pos_status]').val(item.status).change();
              $('.row_entry').last().find('[name=qty_as_number]').val(item.qty);
              $('.row_entry').last().find('[name=discount_as_percent]').val(item.discount);
              $('.row_entry').last().find('[name=linetotal]').text((item.qty*item.sellprice-item.qty*item.sellprice*item.discount/100).toFixed(2));
              $('.row_entry').last().find('[class=x]').show();
              $('.row_entry').last().clone().appendTo("#row_table_id");



            });
            if( $('#row_table_id tr').length > 3 ) $('.dragdrop').show();
            ns.countPos();
            ns.recalc();
            ns.init();
            ready=true;
            $('.listrow').filter(':last').find('[name=item_partpicker_name]').focus();

            $('.ui-sortable').sortable({items: '> tbody:not(.pin)'});


            //console.log(data);
          },
          error: function () {
              alert("error: getPositions fehlgeschlagen");
         }

        });
      }
    }
  });



  $('#btnSaveNewPart').click(function () {

    if ($( '#dialogNewArticleNumber' ).val()!="") {

      var dataArray = {};
      dataArray['partnumber'] = $('#dialogNewArticleNumber').val();
      dataArray['description'] = $('#dialogDescription').val();
      dataArray['unit'] = $('#dialogSelectUnits').val();
      dataArray['listprice'] = $('#dialogBuyPrice').val();
      dataArray['sellprice'] = $('#dialogSellPrice').val();
      dataArray['buchungsgruppen_id'] = accountingGroupsID;
      dataArray['quantity'] = $( "#quantity" ).val();
      dataArray['instruction'] = $( '#instructionCheckbox' ).is( ":checked" );

      dataArray['part_type'] = unitsType[$('#dialogSelectUnits').val()];
      if(dataArray['part_type'] == 'dimension')
      dataArray['part_type'] = 'part';

      console.log(dataArray);

      $.ajax({
         url: 'ajax/order.php',
         type: 'POST',
         data: { action: "newPart", data: dataArray },
         success: function () {

            alert('new Part saved')
         },
         error: function () {
            alert( 'Error: new Part not saved' )
         }

      });

    }

  })

  //updateOrder

  ns.updateOrder=function () {

      var updateDataJSON = new Array;
      updateDataJSON.push({
        //"Bezeichnung des Arrays": Inhalt der zu Speichern ist
        "id": orderID,
        "km_stnd": $('#milage').val(),
        "netamount": $('#orderTotalNetto').val(),
        "amount": $('#orderTotalBrutto').val(),
        "status": $('#orderstatus').val(),
        "finish_time": $('#finish_time').val(),
        "car_status": $('#car_status').val()
      });
      clearTimeout( timer );
      timer = setTimeout( function(){
        ns.updatePosition()
        //console.log('update Order');
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
      },800);

  }

  //Ändert die Artikelnummer bei Artikeltypauswahl
  $( '#dialogPart_typ' ).change( function(){
    var unit;
    var part_type = $( '#dialogPart_typ' ).val();
        if(part_type=='dimension')
            unit='Stck';
        else
            unit='Std'

    $.ajax({
        url: 'ajax/order.php?action=getArticleNumber&data=' + unit,
        type: 'GET',
        success: function (data) {

        var partnumber= $( '#dialogNewArticleNumber' ).val( data.newnumber );

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
        success: function (data) {
          $( '#dialogNewArticleNumber' ).val( data.newnumber );
          var partnumber= data.newnumber;

          if(partnumber<2000)
            $( '#dialogPart_typ' ).val('dimension').change();
          else if(partnumber>2000)
            $( '#dialogPart_typ' ).val('service').change();

        },
        error: function(){
           alert( 'Error: getArticleNumber' )
        }

    });
  });


  $( document ).on( 'change','.unitselect, .orderupdate, .add_item_input not:last', function(){
    if(ready){
    //console.log('change');
    ns.recalc();
    //ns.updatePosition();
    ns.updateOrder();
  }

  });


  $( document ).on( 'keyup','.recalc', function(){
    ns.recalc();
    //ns.updatePosition();
    ns.updateOrder();
  });



  ns.recalc=function() {

    var price = parseFloat( $( ':focus' ).parents().eq( 2 ).find( '[name=sellprice_as_number]' ).val() );
    var number = parseFloat( $( ':focus' ).parents().eq( 2 ).find( '[name=qty_as_number]' ).val() );
    var discount = parseFloat( $( ':focus' ).parents().eq( 2 ).find( '[name=discount_as_percent]' ).val() );
    discount = discount / 100;
    $( ':focus' ).parents().eq( 2 ).find( '[name=linetotal]' ).text( parseFloat( price * number -  price * number * discount ).toFixed( 2 ) );
    var totalprice = 0.00;
    var totalnetto = 0.00;
    $( '[name=linetotal]' ).each( function(){
      //console.log(parseFloat(this.textContent).toFixed(2));
      var linetotal = parseFloat( this.textContent );

      //console.log( linetotal );
      totalprice = totalprice + linetotal;
      var netto = linetotal - linetotal * 0.19;
      totalnetto = totalnetto + netto;

    });
    //console.log( totalprice );
    $( '#orderTotalBrutto' ).val( totalprice.toFixed( 2 ) );
    $( '#orderTotalNetto' ).val( totalnetto.toFixed( 2 ) );



  }

  $('#row_table_id').on('sortstop', function(event, ui) {
    //$('#row_table_id thead a img').remove();
    ns.countPos();
    ns.updatePosition();
  });

  ns.removeOrder=function () {

    $.ajax({
      url: 'ajax/order.php?action=removeOrder&data='+orderID,
      typ: 'GET',
      success: function () {},
      error: function () {alert('ERROR: removeOrder')}
    });

  }

  ns.updatePosition=function () {

     var updatePosData=new Array;

     $('.row_entry').each(function (index) {

       if($( this ).find( '[name=item_partpicker_name]' ).val()!=""){
          updatePosData.push({

            "order_nr": $( this ).find( '[name=position]' ).text(),
            "pos_description": $( this ).find( '[name=item_partpicker_name]' ).val(),
            "pos_unit": $( this ).find( '[name=unit]' ).val(),
            "pos_qty": $( this ).find( '[name=qty_as_number]' ).val(),
            "pos_price": $( this ).find( '[name=sellprice_as_number]' ).val(),
            "pos_discount": $( this ).find( '[name=discount_as_percent]' ).val(),
            "pos_total": $( this ).find( '[name=linetotal]' ).text(),
            "pos_emp": $( this ).find( '[name=mechanics]' ).val(),
            "pos_status": $( this ).find( '[name=pos_status]' ).val(),
            "pos_id": $( this ).attr( 'id' ),
            "parts_id": $( this ).find( '[name=partnumber]' ).attr('part_id'),
            "pos_instruction": $( this ).hasClass( 'instruction' )
          });
       }

     });
     //console.log(updatePosData);
     //clearTimeout( timer );
     //timer = setTimeout( function(){
       //console.log('update Pos')
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

});
