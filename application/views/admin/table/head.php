<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">
#playlist {border: 1px solid #666666;}
#playlist tbody tr td {font-family: "lucida grande", verdana, sans-serif;font-size: 8pt;padding: 3px 8px;border-left: 1px solid #D9D9D9;}
#playlist tbody tr.selected td {background-color: #3d80df;color: #ffffff;font-weight: bold;border-left: 1px solid #346DBE;border-bottom: 1px solid #7DAAEA;}
select {background-color: #EDF3FE;}
</style>
<script type="text/javascript">
    <!--    
    function hasClass(obj) {
        var result = false;
        if (obj.getAttributeNode("class") != null) {
            result = obj.getAttributeNode("class").value;
        }
        return result;
    }
    function stripe(id) {
        var even = false;
        var evenColor = arguments[1] ? arguments[1] : "#fff";
        var oddColor = arguments[2] ? arguments[2] : "#eee";var table = document.getElementById(id);
        if (! table) { return; }
        var tbodies = table.getElementsByTagName("tbody");
        for (var h = 0; h < tbodies.length; h++) {
            var trs = tbodies[h].getElementsByTagName("tr");
            for (var i = 0; i < trs.length; i++) {
                if (!hasClass(trs[i]) && ! trs[i].style.backgroundColor) {
                    var tds = trs[i].getElementsByTagName("td");
                    for (var j = 0; j < tds.length; j++) {
                        var mytd = tds[j];
                        if (! hasClass(mytd) && ! mytd.style.backgroundColor) {
                            mytd.style.backgroundColor = even ? evenColor : oddColor;
                        }
                    }
                }
                even =  ! even;
            }
        }
    }    
   -->
</script>
