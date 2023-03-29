
<script>
    const customerTable = '#tree'
    const customerPager = '#pager'
    const customerDialog = '#dialog'
    const customerForm = '#customerForm'
    
    let indexRow = 0
    let sortName = 'nama'
    let timeout = null
    let highlightSearch
    let noInvoice
    let currentSearch
    let postData
    let ordersPostData
    let triggerClick = true
    let activeGrid = '#tree'
    let socket
    let idCustomer
    
    let lastSelectedRow;

   
    
    $(document).ready(function() {
    
        // Define the jqGrid options
        $("#tree").jqGrid({
            url: "{{url('customers/mdgrid')}}", // The server-side URL to fetch data from
            datatype: "json",
            // data: row, // Menggunakan data dari variabel JavaScript
            colNames: ["id_customer", "No invoice", "Nama Pelanggan", "Tanggal pembelian", "Saldo", "Gender"], // Column names
            pager: "#pager", // The pagination element
            rowNum: 10, // Number of rows per page
            // loadonce: true,
            rowList: [10, 20, 30],
            rownumbers: true, // Dropdown for selecting rows per page
            viewrecords: true, // Show total number of records
            height: "auto", // Automatically adjust height
            sortname: sortName, // Default sorting column
            sortorder: "asc", // Default sorting order
            width: 1300,
            toolbar: [true, "top"], // display the toolbar at the top of the grid
            search: true,
            toolbarfilter: true,
            stringResult: true,
            searchOperators: true, // Aktifkan filtertoolbar
            searchOnEnter: true, // search when Enter is pressed
            searchOperators: true, // enable search operators
            searchFilterInputWidth: 150, // set the width of the search input box
            searchToolbarWidth: 400, // set the width of
            autoencode: true,
            scrollrows: true,
            sortable: true,
            caption: "Data Customers",
            autoencode: true,
            gridview: true,
            debug:true,
            closeAfterAdd:true,
            reloadAfterSubmit:true,
            closeAfterEdit:true,
            // Menambahkan tombol pada sel header kolom nomor urut
            colModel: [{
                    name: "id_customer",
                    label: "id",
                    sorttype: "int",
                    key: true,
                    hidden: true,
                    width: 50,
                    sortable: true,
    
                },
                {
                    name: "no_invoice",
                    label: "No Invoice",
                    width: 170,
                    index: "no_invoice",
                    editable: true,
                    sortable: true,
                    search: true,
                    uppertext: true // Pengaturan untuk mengonversi input ke huruf besar
                }, {
                    name: "nama",
                    label: "Nama",
                    width: 170,
                    index: "nama",
                    editable: true,
                    sortable: true,
                    uppertext: true // Pengaturan untuk mengonversi input ke huruf besar
                }, {
                    name: "tgl_pembelian",
                    label: "Tanggal Pembelian",
                    width: 170,
                    index: "tgl_pembelian",
                    sorttype: "date",
                    sortable: true,
                    edittype: "text",
                    formatter: "date",
                    formatoptions: {
                        srcformat: "Y-m-d",
                        newformat: "d-m-Y"
                    },
                    editoptions: {
                        dataInit: function(element) {
                            $(element).datepicker({
                                dateFormat: 'dd-mm-yy', // format the date as YYYY-MM-DD
                                changeYear: true, // allow the user to change the year
                                changeMonth: true, // allow the user to change the month
                                showButtonPanel: true // show a button panel beneath the calendar
                            });
                            $(element).inputmask('dd-mm-yyyy', {
                                'placeholder': 'DD-MM-YYYY'
                            });
                        },
    
    
                    },
                    align: "right",
                    editable: true,
                    editrules: {
                        date: true
                    },
                }, {
                    name: "saldo",
                    label: "Saldo",
                    width: 170,
                    index: "saldo",
                    formatter: "number",
                    sorttype: "number",
                    sortable: true,
                    formatoptions: {
                        thousandsSeparator: ".",
                        decimalSeparator: ",",
                        defaultValue: "0",
                        decimalPlaces: "0",
                    },
                    align: "right",
                    editable: true,
                    edittype: "text",
                    editoptions: {
                        dataInit: function(elem) {
                            var autoNumericOptions = {
                                digitGroupSeparator: ".",
                                decimalCharacter: ",",
                                currencySymbolPlacement: "p",
                                decimalPlaces: "0",
                                defaultValue: "0"
                            }
                            new AutoNumeric(elem, autoNumericOptions);
                            $(elem).css("text-align", "right"); // set rata kanan pada elemen input 
    
    
                        }
                    }
                }, {
                    name: 'gender',
                    label: 'Gender',
                    width: 200,
                    editable: true,
                    formatter: 'text',
                    sortable: true,
                    sorttype: 'select',
                    edittype: 'select',
                    formatoptions: {
                        value: {
                            "1": "LAKI-LAKI",
                            "2": "PEREMPUAN"
                        },
                    },
                    editoptions: {
                        dataInit: function(element) {
                            $(element).select2({
                                    placeholder: "PILIH GENDER",
                                    data: ["Laki-Laki", "Perempuan"]
                                }),
                                $(element).on('keyup', function() {
                                    $(element).val($(element).val().toUpperCase());
                                });
                        }
                    },
                    uppertext: true,
    
                }
            ],
            onSelectRow: function(id) {
                    indexRow = $(this).jqGrid('getCell', id, 'rn') - 1
                    page = $(this).jqGrid('getGridParam', 'page') - 1;
                    rows = $(this).jqGrid('getGridParam', 'postData').rows
                    if (indexRow >= rows) indexRow = (indexRow - rows * page)
                    
                    idCustomer = $(this)
                        .jqGrid('getRowData', id).id_customer
                        .replace(/(<([^>]+)>)/ig,"")

                        // console.log(idCustomer);

                    if (idCustomer) getDetail(idCustomer)
                },
                loadComplete: function() {
                   
                  
                    // convertHanzi()
                    $(document).unbind('keydown')
                    setCustomBindKeys($(this));
                    postData = $(this).jqGrid('getGridParam', 'postData')

                    setTimeout(function() {

                        $('#tree tbody tr td:not([aria-describedby=tree_rn])').highlight(highlightSearch)

                        if (indexRow > $('#tree').getDataIDs().length - 1) {
                            indexRow = $('#tree').getDataIDs().length - 1
                        }

                        if (triggerClick) {
                            $('#' + $('#tree').getDataIDs()[indexRow]).click()
                            triggerClick = false
                        } else {
                            $('#tree').setSelection($('#tree').getDataIDs()[indexRow])
                        }

                        $('#gsh_tree_rn').html(`
                        <button type="button" id="clearFilter" title="Clear Filter" style="width: 100%; height: 100%;"> X </button>	
                            `)

                        $('[id*=gs_]').on('input', function() {
                            highlightSearch = $(this).val()
                            clearTimeout(timeout)

                            timeout = setTimeout(function() {
                                $('#tree').trigger('reloadGrid')
                            }, 500);
                        })

                        $('#t_tree input').on('input', function() {
                            clearTimeout(timeout)

                            timeout = setTimeout(function() {
                                indexRow = 0
                                $(customerTable).jqGrid('setGridParam', {
                                    postData: {
                                        'global_search': highlightSearch
                                    }
                                }).trigger('reloadGrid')
                            }, 500);
                        })

                        $('input')
                            .css('text-transform', 'uppercase')
                            .attr('autocomplete', 'off')
                    }, 250)

                },
           
    
        });
        // console.log(idCustomer) 
        $("#tree-2").jqGrid({
            datatype: "json", // The format of data being fetched
            colNames: ["Item name", "Item price", "qty", "Total Price"], // Column names
            pager: "#pager2", // The pagination element
            rowNum: 10, // Number of rows per page
            rowList: [10, 20, 30],
            rownumbers: true, // Dropdown for selecting rows per page
            viewrecords: true, // Show total number of records
            height: "auto", // Automatically adjust height
            sortname: sortName, // Default sorting column
            sortorder: "asc", // Default sorting order
            width: 1300,
            scrollrows: true,
            sortable: true,
            caption: "Detail Orders",
            footerrow: true,
            userDataOnFooter: true,
            colModel: [
                {
                    name: "nama_brg",
                    label: "nama_brg",
                    width: 100,
                    index: "nama_brg",
                    editable: true,
                    sortable: true,
                    search: true,
                    uppertext: true // Pengaturan untuk mengonversi input ke huruf besar
                }, {
                    index: 'item_price',
                    name: 'item_price',
                    label: 'Item Price',
                    formatter: 'currency',
                    align: 'right',
                    formatoptions: {
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    },
                }, {
                    index: 'qty',
                    name: 'qty',
                    label: 'Qty',
                    align: 'right',
                },{
                    index: 'total_price',
                    name: 'total_price',
                    label: 'Total Price',
                    formatter: 'currency',
                    align: 'right',
                    formatoptions: {
                        decimalPlaces: 0,
                        decimalSeparator: ',',
                        thousandsSeparator: '.',
                    },
                    
                }
            ],
            loadComplete: function(data) {
                ordersPostData = $(this).jqGrid('getGridParam', 'postData')
                // console.log(data);
    
                sum = $('#tree-2').jqGrid("getCol", "total_price", true, "sum")
    
                $('#tree-2').jqGrid('footerData', 'set', {
                    qty: 'Total',
                    total_price: sum,
                }, true)
            },
            onSelectRow: function(id) {
                activeGrid = '#tree-2'
                idCustomer = $(this).jqGrid('getRowData', id).id_customer
                console.log(activeGrid);
                
            },
            keys: true,
        })
    
        .jqGrid('navGrid', '#pager2',
            {
                search: false,
                refresh: false,
                add: false,
                edit: false,
                del: false,
            }
        )
    
        function getDetail(idCustomer){
            $('#tree-2').jqGrid('setGridParam', {
                url: 'detail/' + idCustomer
            }).trigger('reloadGrid')
        }
    
    
        $('#t_tree').html(`
            <div id="global_search" style="float:left">
            <label> Global search </label>
            <input id="gs_global_search" class="ui-widget-content ui-corner-all" style="padding: 5px;" globalsearch="true" clearsearch="true" style="float:left">
            </div>
        `)
      
    
        // nable add
        $('#tree').jqGrid('navGrid', '#pager', {
           
            "edit": false,
            "add":false,
            "del": false,
            "search": false,
            "refresh": false,
            "view": false,
            "excel": false,
            "pdf": false,
            "csv": false,
            "columns": false,
            "reloadAfterSubmit": true,
            "closeAfterAdd": true,
    
        }, {
            "drag": true,
            "resize": true,
            "closeOnEscape": true,
            "dataheight": 150,
            "reloadAfterSubmit": true,
            "closeAfterSubmit": true,
            "beforeShowForm": sanitizeFormInputs,
            "afterSubmit": position,
            "closeAfterAdd": true,
        }, {
            "drag": true,
            "resize": true,
            "closeOnEscape": true,
            "dataheight": 150,
            "beforeShowForm": sanitizeFormInputs,
            "afterSubmit": position,
            "reloadAfterSubmit": true,
            "closeAfterSubmit": true,
            "closeAfterAdd": true,
        }, 
        );
        $(customerTable).navButtonAdd(customerPager, {
            caption: "Tambah",
             title: "Tambah Data",
             id: "addCustomer",
             buttonicon: "ui-icon-plus",
             onClickButton: function() {
                    // Membuat modal
                    $(customerDialog).load('form-add',function(){

                    }).dialog({
                        modal: true,
                        title: "Tambah Data",
                        width:900, // Mengatur lebar dialog menjadi 1000px
                        height: 500,
                        position: [0, 0],
                        closeText: "X",
                        buttons: {
                            'Save': {
                            text: 'Save',
                            class: 'ui-state-default',
                            click: function() {
                                    var form_data = $('#customerForm').serializeArray();
                                    form_data.push({name: 'oper', value: 'add'}); // tambahkan oper=add ke form data
                                   
                                    $.ajax({
                                        type: "POST",
                                        url:"mdgrid/store",
                                        data: form_data,
                                        dataType:"json",
                                        success: function (response, postdata, oper){
                                          alert("Data berhasil ditambahkan");
                                        //   position(response, postdata, oper)
                                            $(customerDialog).dialog('close')
                                            $('#tree').trigger('reloadGrid')
                                        },
                
                                        })
                                    
                                     },    
                            },
                            'Cancel': {
                            text: 'Cancel',
                            id: 'cancelAdd',
                            class: 'ui-state-default',
                            click: function() {
                                $(this).dialog('close');
                            }
                            }
                        }
                    });
                   
                },
            })

            
          $(customerTable).navButtonAdd(customerPager, {           
            caption: "Edit",
             title: "Edit",
             id: "editData",
             buttonicon: "ui-icon-pencil",
             onClickButton: function() {
                if ($(customerTable).jqGrid('getGridParam','selrow') !== null) {
				activeGrid = undefined
				editDialog()
                } else {
                    alert('Please, select row')
                }
                     
             }
            })

            $(customerTable).navButtonAdd(customerPager, {           
            caption: "Hapus",
             title: "Hapus Data",
             id: "hapustData",
             buttonicon: "ui-icon-trash",
             onClickButton: function() {
                if ($(customerTable).jqGrid('getGridParam','selrow') !== null) {
				activeGrid = undefined
				hapusDialog()
                } else {
                    alert('Please, select row')
                }
                     
             }
            })
            $(customerTable).navButtonAdd(customerPager, {           
            caption: "Report",
             title: "Report",
             id: "report",
             buttonicon: "ui-icon-print",
             onClickButton: function() {
                reportDialog();
                     
             }
            })
            $(customerTable).navButtonAdd(customerPager, {           
            caption: "Export",
             title: "Export",
             id: "export",
             position: "last",
             buttonicon: "ui-icon-document",
             onClickButton: function() {
                if ($(customerTable).jqGrid('getGridParam','selrow') !== null) {
				activeGrid = undefined
				    exportDialog()
                } else {
                    alert('Please, select row')
                }
                     
             }
            })
            
            function exportDialog(){
                $(customerDialog)
				.html(`
                    <input type="hidden" name="selected" value="${$('#tree').getInd($('#tree').getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>

					Yakin Ingin mengeksport data di baris ke ${$('#tree').getInd($('#tree').getGridParam('selrow'))} ? 
				`).dialog({
                modal: true,
                title: "Export Data",
                width:500, // Mengatur lebar dialog menjadi 1000px
                height: 200,
                position: [0, 0],
                closeText: "X",
                buttons: {
                    'Export': {
                    text: 'Export Data',
                    class: 'ui-state-default',
                    click: function() {
                        var selectedRow = $('#tree').jqGrid('getGridParam', 'selrow');
                        var rowData = $('#tree').jqGrid('getRowData', selectedRow);
                        var id_customer = rowData.id_customer; 
                        let selected = $(this).find('input[name=selected]').val()
                        let params
                        var filter = $('#tree').jqGrid('getGridParam', 'postData').filters;
                        var global_search = $('#tree').jqGrid('getGridParam', 'postData').global_search;

                        for (var key in postData) {
                            if (params != "") {
                                params += "&";
                            }
                            params += key + "=" + encodeURIComponent(postData[key]);
                        }

                        let url = `export?${params}&selected=${selected}&id_customer=${id_customer}`;
                            if (postData.filters) {
                               url += `&filters=${postData.filters}`;
                            }
                            if (postData.global_search) {
                                url += `&global_search=${postData.global_search}`;
                            }
                            window.open(url);
                       

                    },    
                    },
                    'Cancel': {
                    text: 'Cancel',
                    id: 'cancelDelete',
                    class: 'ui-state-default',
                    click: function() {
                        $(this).dialog('close');
                    }
                    }
                }
            });
            }
            
            

            function reportDialog(){
                var jumlahData = $('#tree').jqGrid('getGridParam', 'records');
                $(customerDialog)
				.html(`
					<div class="ui-state-default" style="padding: 5px;">
						<h5> Tentukan Baris </h5>
						
						<label> Dari: </label>
						<input type="text" name="start" value="${$('#tree').getInd($('#tree').getGridParam('selrow'))}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>

						<label> Sampai: </label>
						<input type="text" name="limit" value="${$('#tree').getGridParam('records')}" class="ui-widget-content ui-corner-all autonumeric" style="padding: 5px; text-transform: uppercase;" max="2" required>
					</div>
				`)
				.dialog({
					modal: true,
					title: "Customer Report",
					width:700, // Mengatur lebar dialog menjadi 1000px
                    height: 300,
					position: [0, 0],
					buttons: {
                        'Report':  {
                            text: 'Report',
                            class: 'ui-state-default',
                            click: function() {
                                let start = $(this).find('input[name=start]').val()
                                let limit = $(this).find('input[name=limit]').val()
                                let params
                                var filter = $('#tree').jqGrid('getGridParam', 'postData').filters;
                                var global_search = $('#tree').jqGrid('getGridParam', 'postData').global_search;

                                if (parseInt(start) > parseInt(limit)) {
                                    return alert('Sampai harus lebih besar')
                                }

                                for (var key in postData) {
                                if (params != "") {
                                    params += "&";
                                }
                                params += key + "=" + encodeURIComponent(postData[key]);
                                }

                                // window.open(`report/report.php?${params}&start=${start}&limit=${limit}&orders_sidx=${ordersPostData.sidx}&orders_sord=${ordersPostData.sord}&filters=${postData.filters}&global_search=${postData.global_search}`)
                                let url = `report?${params}&start=${start}&limit=${limit}`;
                                    if (postData.filters) {
                                   url += `&filters=${postData.filters}`;
                                    }
                                    if (postData.global_search) {
                                    url += `&global_search=${postData.global_search}`;
                                    }
                                    window.open(url);
                            }
						},
                        'Cancel': {
                            text: 'Cancel',
                            id: 'cancelAdd',
                            class: 'ui-state-default',
                            click: function() {
                                $(this).dialog('close');
                            }
                            }
                    }
                })
                
            }


            // activate the toolbar searching
            $('#tree').jqGrid('filterToolbar', {
                // JSON stringify all data from search, including search toolbar operators
                stringResult: true,
                searchOnEnter: false,
                autosearch: true,
                defaultSearch: "cn",
                aftersearch: function() {
                    indexrow: 0

                },
                gridComplete: function() {
                    $('#tree').setGridParam({
                        datatype: 'json'

                    })
                }
            });
        });


        $(document).on('click', '#clearFilter', function() {
            currentSearch = undefined
            $('[id*="gs_"]').val('')
            $(customerTable).jqGrid('setGridParam', {
                postData: null
            })
            $(customerTable)
                .jqGrid('setGridParam', {
                    postData: {
                        page: 1,
                        rows: 10,
                        sidx: 'name',
                        sord: 'asc',
                    },
                })
                .trigger('reloadGrid')
            highlightSearch = 'undefined'
        })


        function hapusDialog(){
            var selectedRow = $('#tree').jqGrid('getGridParam', 'selrow');
            var rowData = $('#tree').jqGrid('getRowData', selectedRow);
            var id_customer = rowData.id_customer; 
            $(customerDialog).load('form-delete/'+id_customer ,function(){
            }).dialog({
                modal: true,
                title: "Hapus Data",
                width:900, // Mengatur lebar dialog menjadi 1000px
                height: 500,
                position: [0, 0],
                closeText: "X",
                buttons: {
                    'Save': {
                    text: 'Delete Data',
                    class: 'ui-state-default',
                    click: function() {
                            $.ajax({
                            type: "POST",
                            url:"mdgrid/delete",
                            data: {
                              "oper" : "del",
                              "id_customer": id_customer
                            },
                            dataType:"json",
                            success: function (data){
                                alert("Data berhasil dihapus");
                                // position(response, postdata, oper);
                                $(customerDialog).dialog('close')
					            $('#tree').trigger('reloadGrid')

                            },
                
                            })   
                        
                    },    
                    },
                    'Cancel': {
                    text: 'Cancel',
                    id: 'cancelDelete',
                    class: 'ui-state-default',
                    click: function() {
                        $(this).dialog('close');
                    }
                    }
                }
            });
        }



        function editDialog(){
                    var selectedRow = $('#tree').jqGrid('getGridParam', 'selrow');
                    var rowData = $('#tree').jqGrid('getRowData', selectedRow);
                    var id= rowData.id_customer; 
                    $(customerDialog).load('form-update/'+id, function() {
                        }).
                        dialog({
                        modal: true,
                        title: "Edit Data",
                        width:900, // Mengatur lebar dialog menjadi 1000px
                        height: 500,
                        position: [0, 0],
                        closeText: "X",
                        buttons: {
                            'Save': {
                            text: 'Save',
                            class: 'ui-state-default',
                            click: function() {
                                var form_data = $('#customerForm').serializeArray();
                                    form_data.push({name: 'oper', value: 'edit'}); //
                                    $.ajax({
                                        type: "POST",
                                        url:"mdgrid/update",
                                        data: form_data,
                                        dataType:"json",
                                        success: function (response, postdata, oper){
                                          alert("Data berhasil diupdate");
                                        //   position(response, postdata, oper)
                                        $(customerDialog).dialog('close')
					                    $('#tree').trigger('reloadGrid')
                                        },
                
                                        })   
                                    
                                },    
                            },
                            'Cancel': {
                            text: 'Cancel',
                            id: 'cancelAdd',
                            class: 'ui-state-default',
                            click: function() {
                                $(this).dialog('close');
                            }
                            }
                        }
                    });
                }


        function setCustomBindKeys(grid) {
            $(document).on("keydown", function(e) {
                if (activeGrid) {
                    if (
                        e.keyCode == 33 ||
                        e.keyCode == 34 ||
                        e.keyCode == 35 ||
                        e.keyCode == 36 ||
                        e.keyCode == 38 ||
                        e.keyCode == 40 ||
                        e.keyCode == 13
                    ) {
                        e.preventDefault();
                        var gridIds = $(activeGrid).getDataIDs();
                        var selectedRow = $(activeGrid).getGridParam("selrow");
                        var currentPage = $(activeGrid).getGridParam("page");
                        var lastPage = $(activeGrid).getGridParam("lastpage");
                        var currentIndex = 0;
                        var row = $(activeGrid).jqGrid("getGridParam", "postData").rows;

                        for (var i = 0; i < gridIds.length; i++) {
                            if (gridIds[i] == selectedRow) currentIndex = i;
                        }

                        if (triggerClick == false) {
                            if (33 === e.keyCode) {
                                if (currentPage > 1) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) - 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (34 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: parseInt(currentPage) + 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (35 === e.keyCode) {
                                if (currentPage !== lastPage) {
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: lastPage,
                                        })
                                        .trigger("reloadGrid");
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":last")
                                            .attr("id")
                                        ) {
                                            $(activeGrid)
                                                .jqGrid(
                                                    "setSelection",
                                                    $(activeGrid)
                                                    .find(">tbody>tr.jqgrow")
                                                    .filter(":last")
                                                    .attr("id")
                                                )
                                                .trigger("reloadGrid");
                                        }
                                    }

                                    triggerClick = true;
                                }
                                if (e.ctrlKey) {
                                    if (
                                        $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                        $("#customer")
                                        .find(">tbody>tr.jqgrow")
                                        .filter(":last")
                                        .attr("id")
                                    ) {
                                        $(activeGrid)
                                            .jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":last")
                                                .attr("id")
                                            )
                                            .trigger("reloadGrid");
                                    }
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (36 === e.keyCode) {
                                if (currentPage > 1) {
                                    if (e.ctrlKey) {
                                        if (
                                            $(activeGrid).jqGrid("getGridParam", "selrow") !==
                                            $("#customer")
                                            .find(">tbody>tr.jqgrow")
                                            .filter(":first")
                                            .attr("id")
                                        ) {
                                            $(activeGrid).jqGrid(
                                                "setSelection",
                                                $(activeGrid)
                                                .find(">tbody>tr.jqgrow")
                                                .filter(":first")
                                                .attr("id")
                                            );
                                        }
                                    }
                                    $(activeGrid)
                                        .jqGrid("setGridParam", {
                                            page: 1,
                                        })
                                        .trigger("reloadGrid");

                                    triggerClick = true;
                                }
                                $(activeGrid).triggerHandler("jqGridKeyUp"), e.preventDefault();
                            }
                            if (38 === e.keyCode) {
                                if (currentIndex - 1 >= 0) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex - 1]);
                                }
                            }
                            if (40 === e.keyCode) {
                                if (currentIndex + 1 < gridIds.length) {
                                    $(activeGrid)
                                        .resetSelection()
                                        .setSelection(gridIds[currentIndex + 1]);
                                }
                            }
                            if (13 === e.keyCode) {
                                let rowId = $(activeGrid).getGridParam("selrow");
                                let ondblClickRowHandler = $(activeGrid).jqGrid(
                                    "getGridParam",
                                    "ondblClickRow"
                                );

                                if (ondblClickRowHandler) {
                                    ondblClickRowHandler.call($(activeGrid)[0], rowId);
                                }
                            }
                        }
                    }
                }
            });
        }

        function position(response, postdata, oper) {
           
            sidx = $('#tree').jqGrid('getGridParam', 'postData').sidx;
            sord = $('#tree').jqGrid('getGridParam', 'postData').sord;
            pagesize = $('#tree').jqGrid('getGridParam', 'postData').rows;
            var filterRules = $('#tree').jqGrid('getGridParam', 'postData').filters;
            var globalsearch = $('#tree').jqGrid('getGridParam', 'postData').global_search;
            var id_customer = JSON.parse(response);
            // console.log(id_customer);

            $.ajax({
                    url: "after_save.php",
                    dataType: "json",
                    data: {
                        'id_customer': id_customer,
                        'sidx': sidx,
                        'sord': sord,
                        'filters': filterRules,
                        'global_search': globalsearch
                    }
                })
                .done(function(data) {
                    // console.log(data);
                   
                    let posisi = data[0].position;
                    let pager = Math.ceil(posisi / pagesize);
                    let baris = posisi - ((pager - 1) * pagesize);

                    indexRow = baris - 1;
                    triggerClick = true

                    $('#tree').jqGrid('setSelection', $('#tree').jqGrid('getDataIDs')[indexRow], triggerClick);
                    $('#tree').jqGrid('setGridParam', {page: pager}).trigger('reloadGrid');
                    $("#cancelAdd").click();  
                    
                })
            }

            function  sanitizeFormInputs (form){
                // alert(1);
                var nilaiAsli = "";
                var no_invoice = document.getElementById("no_invoice"); // Ambil element input no_invoice
                var nama_pelanggan = document.getElementById("nama"); // Ambil element input nama_pelanggan
                var tgl_pembelian = document.getElementById("tgl_pembelian"); // Ambil element input tgl_pembelian
                var jenis_kelamin = document.getElementById("gender"); // Ambil element input jenis_kelamin
                var saldo = document.getElementById("saldo");// Ambil element input saldo

                nilaiAsli = no_invoice.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = no_invoice.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                no_invoice.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = tgl_pembelian.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = tgl_pembelian.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                tgl_pembelian.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = nama_pelanggan.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = nama_pelanggan.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                nama_pelanggan.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = jenis_kelamin.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = jenis_kelamin.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                jenis_kelamin.value = inputBaru; // Tampilkan input yang telah diubah

                nilaiAsli = saldo.value; // Simpan nilai asli dari input pada variabel nilaiAsli
                var inputBaru = saldo.value.replace(/<[^>]+>/g, ""); // Menghapus elemen tag HTML dari input menggunakan regex
                saldo.value = inputBaru; // Tampilkan input yang telah diubah
            }
          
    </script>
</body>

</html>