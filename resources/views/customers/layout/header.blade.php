<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/theme/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/trirand/ui.jqgrid.css" />
    <script src="/js/jquery.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="/js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="/js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.6.0/autoNumeric.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="/js/highlight.js"></script>
   
    <title>Data Customer</title>
</head>
<style>
    * {
        text-transform: uppercase;
    }

    .highlight {
        background-color: yellow;
    }
    .ui-dialog-buttonset button {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .ui-dialog-buttonset button:hover {
        background-color: #0062cc;
    }

</style>