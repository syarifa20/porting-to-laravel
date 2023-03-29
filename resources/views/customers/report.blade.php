<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/libraries/stimulsoft-report/2021.3.6/css/stimulsoft.viewer.office2013.whiteblue.css">
    <link rel="stylesheet" href="/libraries/stimulsoft-report/2021.3.6/css/stimulsoft.designer.office2013.whiteblue.css">
    <script src="/libraries/stimulsoft-report/2021.3.6/scripts/stimulsoft.reports.js
    "></script>
    <script src="/libraries/stimulsoft-report/2021.3.6/scripts/stimulsoft.dashboards.js
    "></script>
    <script src="/libraries/stimulsoft-report/2021.3.6/scripts/stimulsoft.viewer.js
    "></script>
    <script src="/libraries/stimulsoft-report/2021.3.6/scripts/stimulsoft.designer.js
    "></script>
    <script type="text/javascript">
        function onLoad(){
           
            Stimulsoft.Base.StiLicense.loadFromFile("/libraries/stimulsoft-report/2021.3.6/stimulsoft/license.php");
            var viewerOptions = new Stimulsoft.Viewer.StiViewerOptions()

            var viewer = new Stimulsoft.Viewer.StiViewer(viewerOptions, "StiViewer", false)
            var report = new Stimulsoft.Report.StiReport()

            var options = new Stimulsoft.Designer.StiDesignerOptions()
            options.appearance.fullScreenMode = true

            // var designer = new Stimulsoft.Designer.StiDesigner(options, "Designer", false)

            var dataSet = new Stimulsoft.System.Data.DataSet("Data")

                viewer.renderHtml('content')
                report.loadFile('/reports/CustomerOrder.mrt')

                report.dictionary.dataSources.clear()

                dataSet.readJson(<?= json_encode($data) ?>)

                report.regData(dataSet.dataSetName, '', dataSet)
                report.dictionary.synchronize()
                report.pages.getByIndex(0).margins = new Stimulsoft.Report.Components.StiMargins(0, 0, 0, 0) /*(last printed)*/

                // report.pages.getByIndex(0).margins = new Stimulsoft.Report.Components.StiMargins(1.5, .5, 2, 1.5)


                /*
                var dataRelation = new Stimulsoft.Report.Dictionary.StiDataRelation("relation", "relation", "relation", report.dictionary.dataSources.getByName("customers"), report.dictionary.dataSources.getByName("orders"), ["no_invoice"],  ["no_invoice"]);

                report.dictionary.relations.add(dataRelation)
                */
                
                // report.dictionary.synchronize()

                viewer.report = report

                /*$(document).keydown(function(e) {
                    switch(e.keyCode) {
                    case 33:
                        e.preventDefault()
                        $("img[src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAAFpJREFUeNpi/P//PwMlgJFSAxhABhCDfRq3/8cmzkSMJb5NO3A6k4kSzQQNIKQZrwHEaMZpALGacRqwuc6DkSIDSDEEbyASYwjBaCRoCKUpkXHAMxPFBgAEGACURoA7FfW1FgAAAABJRU5ErkJggg==']").click()
                        console.log('page up')
                        break
                    case 34:
                        e.preventDefault()
                        $("img[src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAAGFJREFUeNpi/P//PwMlgJFSAxhABqBjn8bt/7GJY8NMuAz2bdpBlNOY8EkSYwgTIQWEDGEixpn4DGEiNrBxGUK0AZvrPBjJNgCXZqIMwKeZoAGENFMlJTIOeGai2ACAAAMAH36AO+rHr5IAAAAASUVORK5CYII=']").click()
                        break
                    }
                })*/
                designer.renderHtml("content")
                designer.report = report
                }

                function afterPrint() {
                if (confirm('Tutup halaman?')) {
                    window.close()
                }
                }
  </script>
  <!-- <style type="text/css">
    .stiJsViewerPage {
      word-break:  break-all !important;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
      }
    }
  </style> -->
  <title>Customer Order</title>
</head>
<body onLoad="onLoad()" onafterprint="afterPrint()">

  <div id="content"></div>

</body>
</html>


