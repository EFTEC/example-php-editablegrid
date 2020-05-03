<!DOCTYPE html>
<html>
<head>
    <title>jQuery Grid Inline Editing</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container-fluid">
    <button id="btnAdd" class="gj-button-md">Add Row</button>
    <div class="row">
        <div class="col-xs-12">
            <table id="grid"></table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        let grid, countries;
        grid = $('#grid').grid({
            dataSource: 'ws/server.php?action=get',
            uiLibrary: 'bootstrap4',
            primaryKey: 'ID',
            inlineEditing: { mode: 'command' },
            columns: [
                { field: 'ID', width: 44 },
                { field: 'Name', editor: true },
                { field: 'CountryName', title: 'Nationality', type: 'dropdown', editField: 'IdCounty'
                    , editor: { dataSource: 'ws/server.php?action=countries', valueField: 'IdCounty', textField:'Name' } },
                { field: 'IsActive', title: 'Active?', type: 'checkbox', editor: true, width: 90, align: 'center' }
            ],
            pager: { limit: 5 }
        });
        grid.on('rowDataChanged', function (e, id, record) {
            // Clone the record in new object where you can format the data to format that is supported by the backend.
            const data = $.extend(true, {}, record);
            // Format the date to format that is supported by the backend.

            // Post the data to the server
            $.ajax({ url: 'ws/server.php?action=save', data: { record: data }, method: 'POST' })
                .done(function (e) {
                    if(e.result===false) {
                        alert(e.message);
                    }
                })
                .fail(function () {
                    alert('Failed to save.');
                });
        });
        grid.on('rowRemoving', function (e, $row, id, record) {
            if (confirm('Are you sure?')) {
                $.ajax({ url: 'ws/server.php?action=delete', data: { id: id }, method: 'POST' })
                    .done(function (e) {
                        if(e.result===false) {
                            alert(e.message);
                        }
                        grid.reload();
                    })
                    .fail(function () {
                        alert('Failed to delete.');
                    });
            }
        });
        $("#btnAdd").on("click",function() {
            $.ajax({url: 'ws/server.php?action=add', data: {}, method: 'POST'})
                .done(function (e) {
                    if(e.result===false) {
                        alert(e.message);
                    }
                    grid.reload();
                })                
                .fail(function () {
                    alert('Failed to insert.');
                })
        });
    });
</script>
</body>
</html>
