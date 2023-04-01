
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <title>Tes</title>
</head>
<style type="text/css">
	input, label, select, textarea {
		text-transform: uppercase;
		padding: 5px;
        font-size: 11px;
	}
</style>
<body>
    <form id="customerForm">
        <table width="50%" cellspacing="0" id="customerData">
            <tr>
                <td>
                    <label>No Invoice</label>
                </td>
                <td>
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="text" id="no_invoice" name="no_invoice" class="FormElement ui-widget-content ui-corner-all" autocomplete="off" onkeyup="this.value = this.value.toUpperCase();">


                </td>
            </tr>
            <tr>
                <td>
                    <label>Tanggal Pembelian</label>
                </td>
                <td>
                    <input type="text" id="tgl_pembelian" name="tgl_pembelian" class="FormElement ui-widget-content ui-corner-all hasDatePicker" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off" maxlength="10">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Nama Customer</label>
                </td>
                <td>
                    <input type="text" id="nama_customer" name="customer_name" class="FormElement ui-widget-content ui-corner-all" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Saldo</label>
                </td>
                <td>
                    <input type="text" id="saldo" name="saldo" class="FormElement ui-widget-content ui-corner-all im-currency" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off">
                </td>
            </tr>
            <tr>
                <td>
                    <label>Gender</label>
                </td>
                <td>
                    <select id="gender" class="FormElement ui-widget-content ui-corner-all" name="gender_id" required>
                        <option value="LAKI-LAKI">LAKI-LAKI</option>
                        <option value="PEREMPUAN">PEREMPUAN</option>
                    </select>
                </td>
            </tr>
        </table>
    
        <br>
    
        <table width="100%" class="table ui-state-default" cellpadding="5" cellspacing="0" id="detailData">
            <thead>
                <tr>
                    <th class="ui-th-div">Item Name</th>
                    <th class="ui-th-div">Item Price</th>
                    <th class="ui-th-div">Qty</th>
                    <th class="ui-th-div">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" name="item_name[]" class="FormElement ui-widget-content ui-corner-all" required onkeyup="this.value = this.value.toUpperCase();" autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="item_price[]" class="FormElement ui-widget-content ui-corner-all im-currency" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="qty[]" class="FormElement ui-widget-content ui-corner-all im-numeric" onkeyup="this.value = this.value.toUpperCase();" style="text-align: right" required autocomplete="off">
                    </td>
                    <td>
                        <a href="javascript:">
                            <span class="ui-icon ui-icon-trash" onclick="$(this).parent().parent().parent().remove()"></span>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>
                        <a href="javascript:" onkeyup="this.value = this.value.toUpperCase();" onclick="addRow(); setNumericFormat(); formBindKeys();">
                            <span class="ui-icon ui-icon-plus"></span>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <script type="text/javascript">
        $(document).ready(function() {
            let index = 0
            
            $('#gender').select2({
                dropdownParent: $('#dialog')
            });
            setDateFormat()
            setNumericFormat()
            formBindKeys()
        })
    
        function addRow() {
            $('#detailData tbody tr').last().before(`
                <tr>
                    <td>
                        <input type="text" name="item_name[]" class="FormElement ui-widget-content ui-corner-all" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="item_price[]" class="FormElement ui-widget-content ui-corner-all im-currency" onkeyup="this.value = this.value.toUpperCase();" required autocomplete="off">
                    </td>
                    <td>
                        <input type="text" name="qty[]" class="FormElement ui-widget-content ui-corner-all im-numeric" onkeyup="this.value = this.value.toUpperCase();" style="text-align: right" required autocomplete="off">
                    </td>
                    <td>
                        <a href="javascript:">
                            <span class="ui-icon ui-icon-trash" onkeyup="this.value = this.value.toUpperCase();" onclick="$(this).parent().parent().parent().remove()"></span>
                        </a>
                    </td>
                </tr>
            `)
        }
        
        function setDateFormat() {
            $('.hasDatePicker').datepicker({
                dateFormat: 'dd-mm-yy', // format the date as YYYY-MM-DD
                changeYear: true, // allow the user to change the year
                changeMonth: true, // allow the user to change the month
                showButtonPanel: true, // show a button panel beneath the calendar
            }).inputmask('dd-mm-yyyy', {
                'placeholder': 'DD-MM-YYYY'
            })
            .focusout(function(e) {
                let val = $(this).val()
                if (val.match('[a-zA-Z]') == null) {
                    if (val.length == 8) {
                        $(this).inputmask({
                            inputFormat: "dd-mm-yyyy",
                        }).val([val.slice(0, 6), '20', val.slice(6)].join(''))
                    }
                } else {
                    $(this).focus()
                }
            })
            .focus(function() {
                let val = $(this).val()
                if (val.length == 10) {
                    $(this).inputmask({
                        inputFormat: 'dd-mm-yy',
                    }).val([val.slice(0, 6), '', val.slice(8)].join(''))
                }
            })
        }
    
        function setNumericFormat() {
            $('.im-numeric').keypress(function(e){
                if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                  return false;
                }
            })
    
            $('.im-currency').inputmask('integer', {
                alias: 'numeric',
                groupSeparator: '.',
                autoGroup: true,
                digitsOptional: false,
                allowMinus: false,
                placeholder: '',
            })
        }

       
    
        function formBindKeys() {
            let inputs = $('#customerForm [name]:not(:hidden)')
            let element
            let position
    
            inputs.each(function(i, el) {
                $(el).attr('data-input-index', i)
            })
    
            $(inputs[0]).focus()
    
            inputs.focus(function() {
                $(this).data('input-index')
            })
    
            inputs.keydown(function(e) {
                let operator
                switch(e.keyCode) {
                    case 38:
                        element = $(inputs[$(this).data('input-index') - 1])
                        if (element.is(':not(select)') && element.attr('type') !== 'email') {
                            position = element.val().length
                            element[0].setSelectionRange(position, position)
                        }
                        element.hasClass('hasDatePicker')
                            ? $('.ui-datepicker').show()
                            : $('.ui-datepicker').hide()
                        element.focus()
                        break
                    case 40:
                        element = $(inputs[$(this).data('input-index') + 1])
                        if (element.is(':not(select)') && element.attr('type') !== 'email') {
                            position = element.val().length
                            element[0].setSelectionRange(position, position)
                        }
                        element.hasClass('hasDatePicker')
                            ? $('.ui-datepicker').show()
                            : $('.ui-datepicker').hide()
                        element.focus()
                        break
                }
            })
        }
    </script>
</body>
</html>