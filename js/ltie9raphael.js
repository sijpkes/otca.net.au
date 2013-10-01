$(document).ready(function(){
    var paper = Raphael("canvas", 450, 380);
    var map = paper.set();

    // load svgz map
    $.ajax({
        type: "GET",
        url: "../svg/wheel.xml",
        dataType: "xml",
        success: parseXml,
		error: function (e) { alert(e); }
    });

    // ... removed a few other variables

    function parseXml(xml) {
        var count = 0;
        $(xml).find("g").children("path").each(function()
        {
            var deptNr = depts[count];
            var path = $(this).attr("d");
            var c = paper.path(path);
            c.attr(attr).attr("title",deptNr);
            map.push(c);
            count++;
        });
        //startMap();
    }
});
