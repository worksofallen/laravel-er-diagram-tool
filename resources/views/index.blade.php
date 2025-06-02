<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover"/>
    <meta name="description" content="Interactive entity-relationship diagram or data model diagram implemented by GoJS in JavaScript for HTML."/>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/gojs@2.1.47/extensions/ZoomSlider.css"/>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gojs/2.1.46/go.js"></script>
    <script src="https://unpkg.com/gojs@2.1.47/extensions/Figures.js"></script>
    <script src="https://unpkg.com/gojs@2.1.47/extensions/ZoomSlider.js"></script>

    <title>Interactive Entity Relationship Diagram | {{ $appName }}</title>
</head>
<body class="bg-gray-100 tracking-wide bg-gray-200">
<div id="app" v-cloak class="w-full flex">
    <aside class="text-xl text-grey-darkest break-all bg-gray-200 pl-2 pr-2 h-screen sticky top-1 overflow-auto">
        <div class="pt-2 pb-4">
            <h3 class="text-base font-bold leading-6 text-gray-900">Search &amp; Filter by Models</h3>
            <div class="flex items-center pt-2">
                <label for="search" class="sr-only">Model Search</label>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" id="search"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                           placeholder="Model name..." onkeypress="if (event.keyCode === 13) searchDiagram()">
                </div>
                <button onclick="searchDiagram()"
                        class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </div>
        </div>
        <div class="relative flex items-start pb-3 border-b-[1px] border-gray-300">
            <div class="flex h-6 items-center">
                <input id="input-table-names-checkbox-check-all" aria-describedby="checkbox-check-all"
                       name="input-table-names-checkbox-check-all" type="checkbox"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
            </div>
            <div class="ml-3 text-sm leading-6">
                <label for="input-table-names-checkbox-check-all" class="font-semibold text-gray-900">Check All
                    Models</label>
            </div>
        </div>
        <div id="filter-by-table-name"></div>

        <div class="mt-4 pb-3 pt-3 text-sm border-t-[1px] border-gray-300">
            <h3 class="text-base font-bold leading-6">Filter by Relation Type</h3>
            <input type="checkbox" class="text-sm mb-2" id="input-relation-type-checkbox-check-all"> <label
                for="input-relation-type-checkbox-check-all">Check All</label>
            <div id="filter-by-relation-type" class="space-y-3"></div>
        </div>

    </aside>
    <div class="w-10/12 bg-gray-300 relative">
        <div id="myDiagramDiv" style="background-color: white; width: 100%; height: 100vh"></div>
    </div>
</div>
<script>

    var nodeDataArray = [];
    var linkDataArray = [];
    var nodeDataArray = [];
    var linkDataArray = [];
    function init() {
        var $ = go.GraphObject.make; // for conciseness in defining templates

        myDiagram =
            $(go.Diagram, "myDiagramDiv", // must name or refer to the DIV HTML element
                {
                    allowDelete: false,
                    allowCopy: false,
                    layout: $(go.LayeredDigraphLayout),
                    "undoManager.isEnabled": true,
                    initialScale: 0.5,
                });

        var itemTempl =
            $(go.Panel, "Horizontal", // this Panel is a row in the containing Table
                new go.Binding("portId", "name"), // this Panel is a "port"
                {
                    background: "transparent", // so this port's background can be picked by the mouse
                    fromSpot: go.Spot.Right, // links only go from the right side to the left side
                    toSpot: go.Spot.Left,
                    // allow drawing links from or to this port:
                    fromLinkable: false,
                    toLinkable: false
                },
                $(go.Shape, {
                        desiredSize: new go.Size(15, 15),
                        strokeJoin: "round",
                        strokeWidth: 3,
                        stroke: null,
                        margin: 2,
                        // but disallow drawing links from or to this shape:
                        fromLinkable: false,
                        toLinkable: false
                    },
                    new go.Binding("figure", "figure"),
                    new go.Binding("stroke", "color"),
                    new go.Binding("fill", "color"),
                ),
                $(go.TextBlock, {
                        margin: new go.Margin(0, 5),
                        column: 1,
                        font: "13px sans-serif",
                        alignment: go.Spot.Left,
                        // and disallow drawing links from or to this text:
                        fromLinkable: false,
                        toLinkable: false
                    },
                    new go.Binding("text", "name")),
                $(go.TextBlock, {
                        margin: new go.Margin(0, 5),
                        column: 2,
                        font: "11px courier",
                        alignment: go.Spot.Left
                    },
                    new go.Binding("text", "info"))
            );

        // define the Node template, representing an entity
        myDiagram.nodeTemplate =
            $(go.Node, "Auto", // the whole node panel
                {
                    selectionAdorned: true,
                    resizable: true,
                    layoutConditions: go.Part.LayoutStandard & ~go.Part.LayoutNodeSized,
                    fromSpot: go.Spot.AllSides,
                    toSpot: go.Spot.AllSides,
                    isShadowed: true,
                    shadowOffset: new go.Point(3, 3),
                    shadowColor: "#C5C1AA"
                },
                new go.Binding("location", "location").makeTwoWay(),
                // whenever the PanelExpanderButton changes the visible property of the "LIST" panel,
                // clear out any desiredSize set by the ResizingTool.
                new go.Binding("desiredSize", "visible", function(v) {
                    return new go.Size(NaN, NaN);
                }).ofObject("LIST"),
                // define the node's outer shape, which will surround the Table
                $(go.Shape, "RoundedRectangle", {
                    fill: 'white',
                    stroke: "#eeeeee",
                    strokeWidth: 6
                },  new go.Binding("fill", "isHighlighted", function(h) { return h ? "gold" : "#ffffff"; }).ofObject()),
                $(go.Panel, "Table", {
                        margin: 8,
                        stretch: go.GraphObject.Fill
                    },
                    $(go.RowColumnDefinition, {
                        row: 0,
                        sizing: go.RowColumnDefinition.None
                    }),

                    // the table header
                    $(go.TextBlock, {
                            row: 0,
                            alignment: go.Spot.Left,
                            margin: new go.Margin(0, 24, 0, 2), // leave room for Button
                            font: "bold 16px sans-serif"
                        },
                        new go.Binding("text", "key")),
                    // the collapse/expand button
                    $("PanelExpanderButton", "LIST", // the name of the element whose visibility this button toggles
                        {
                            row: 0,
                            alignment: go.Spot.TopRight
                        }),
                    // the list of Panels, each showing an attribute
                    $(go.Panel, "Vertical", {
                            name: "LIST",
                            row: 1,
                            padding: 3,
                            alignment: go.Spot.TopLeft,
                            defaultAlignment: go.Spot.Left,
                            stretch: go.GraphObject.Horizontal,
                            itemTemplate: itemTempl,
                        },
                        new go.Binding("itemArray", "schema"))
                ), // end Table Panel

                $(go.Panel, "Spot",
                    new go.Binding("opacity", "ribbonText", t => t ? 1 : 0),
                    // note that the opacity defaults to zero (not visible),
                    // in case there is no "ribbon" property
                    { opacity: 0,
                        alignment: new go.Spot(1, 0, 5, -5),
                        alignmentFocus: go.Spot.TopRight },

                    // the ribbon itself
                    $(go.Shape, {
                        geometryString: "F1 M0 0 L30 0 70 40 70 70z",
                        stroke: null,
                        strokeWidth: 0,
                    }, new go.Binding('fill', "ribbonColour")),
                    $(go.TextBlock,
                        new go.Binding("text", "ribbonText"),
                        {
                            alignment: new go.Spot(1, 0, -29, 29),
                            angle: 45,
                            maxSize: new go.Size(100, NaN),
                            font: "bold 13px sans-serif",
                            textAlign: "center",
                        },
                        new go.Binding('stroke', "ribbonTextColour")
                    )
                )
            ); // end Node

        // define the Link template, representing a relationship
        myDiagram.linkTemplate =
            $(go.Link, // the whole link panel
                {
                    selectionAdorned: true,
                    layerName: "Foreground",
                    reshapable: true,
                    routing: go.Link.{{ $routingType }},
                    corner: 5,
                    curve: go.Link.Orthogonal,
                    curviness: 0,
                },
                $(go.Shape, // the link shape
                    {
                        stroke: "#303B45",
                        strokeWidth: 1.5,
                    }),
                $(go.Shape, // the arrowhead
                    {
                        toArrow: "Triangle",
                        fill: "#1967B3"
                    }),
                $(go.TextBlock, // the "from" label
                    {
                        textAlign: "center",
                        font: "bold 12px sans-serif",
                        stroke: "#1967B3",
                        segmentIndex: 1.5,
                        segmentOffset: new go.Point(NaN, NaN),
                        segmentOrientation: go.Link.Horizontal,
                        fromLinkable: true,
                        toLinkable: true
                    },
                    new go.Binding("text", "fromText")),

                $(go.TextBlock, // the "to" label
                    {
                        textAlign: "center",
                        font: "bold 12px sans-serif",
                        stroke: "#1967B3",
                        segmentIndex: -10,
                        segmentOffset: new go.Point(NaN, NaN),
                        segmentOrientation: go.Link.OrientUpright,
                        fromLinkable: true,
                        toLinkable: true
                    },
                    new go.Binding("text", "toText"))
            );

        myDiagram.model = $(go.GraphLinksModel, {
            copiesArrays: true,
            copiesArrayObjects: true,
            linkFromPortIdProperty: "fromPort",
            linkToPortIdProperty: "toPort",
            nodeDataArray: nodeDataArray,
            linkDataArray: linkDataArray,
        });

        zoomSlider = new ZoomSlider(myDiagram, {
            alignment: go.Spot.TopLeft,
            alignmentFocus: go.Spot.TopLeft,
        });

        loadFilterByTableNames()
        loadFilterByRelationType()
        setCheckboxesForTableNames();
        setCheckboxesForRelationTypes();
    };

    // the Search functionality highlights all of the nodes that have at least one data property match a RegExp
    function searchDiagram() {  // called by button
        var input = document.getElementById("search");
        if (!input) return;
        myDiagram.focus();

        myDiagram.startTransaction("highlight search");

        if (input.value) {
            // search four different data properties for the string, any of which may match for success
            // create a case insensitive RegExp from what the user typed
            var safe = input.value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex = new RegExp(safe, "i");
            var results = myDiagram.findNodesByExample(
                { key: regex }, // this is the `key` field from the node data, you can add more of these args if you want to search more fields
            );
            myDiagram.highlightCollection(results);
            // try to center the diagram at the first node that was found
            if (results.count > 0) myDiagram.centerRect(results.first().actualBounds);
        } else {  // empty string only clears highlighteds collection
            myDiagram.clearHighlighteds();
        }

        myDiagram.commitTransaction("highlight search");
    }

    function loadFilterByRelationType() {
        var json = linkDataArray;
        var appended = [];
        $.each(json, function(i, v) {
            // check if doesn't exist in the array
            if ($.inArray(this.type, appended) == -1) {
                // append
                appended.push(this.type)
                $("#filter-by-relation-type").append($("<div class='text-sm'>")
                    .append(` <label for="relationship-${this.type}" class="px-2 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500"><code>${this.type}</code></label>`)
                    .prepend($("<input>").attr({
                        'type': 'checkbox',
                        'checked': true,
                        'class': 'input-relation-type-checkbox',
                        'name': 'subscribe-relation-type',
                        "data-feed": this.type,
                        'id': `relationship-${this.type}`,
                    })
                    .val(this.type)
                    .prop('checked', this.type === 'BelongsTo') // Super noisy if you default them all on
                ));
            }
        });
        $(".input-relation-type-checkbox").on('change', function() {
            setCheckboxesForTableNames()
            setCheckboxesForRelationTypes()
        });
    }

    function setCheckboxesForRelationTypes() {
        newLinkDataArray = []

        $(".input-relation-type-checkbox").each(function() {
            if ($(this).prop('checked')) {
                for (let i = 0; i < linkDataArray.length; i++) {
                    const element = linkDataArray[i];
                    if (element.type == $(this).val()) {
                        newLinkDataArray.push(element)
                    }
                }
            }
        });
        myDiagram.model.linkDataArray = newLinkDataArray
    }

    function loadFilterByTableNames() {
        var appended = [];
        var unbucketedNodes = nodeDataArray;

        var nodesByDomain = unbucketedNodes.reduce((group, model) => {
           const { domain } = model;

            group[domain] = group[domain] ?? [];
            group[domain].push(model);

            return group;
        }, {});

        for (const domain in nodesByDomain) {
            $("#filter-by-table-name").append(
                $("<h2 class='mt-4 mb-1 p-2 text-lg font-semibold bg-gray-300 rounded-lg'>").append(
                    $("<div class='flex items-center'>").append(
                        $("<input>").attr({
                            'type': 'checkbox',
                            'checked': true,
                            'class': 'input-domain-checkbox',
                            'name': `subscribe-domain-${domain}`,
                            'data-domain': domain,
                            'id': `domain-${domain}`,
                        }),
                        $("<label class='leading-6 ml-2'>").attr('for', `domain-${domain}`).text(domain)))
            );

            $.each(nodesByDomain[domain], function (i, v) {
                // check if doesn't exist in the array
                if ($.inArray(this.key, appended) == -1) {
                    // append
                    appended.push(this.key);

                    $("#filter-by-table-name").append($("<div class='ml-4 mb-0.5 text-sm'>")
                        .append(` <label for="table-${this.key}">${this.key}</label>`)
                        .prepend($("<input>").attr({
                            'type': 'checkbox',
                            'checked': true,
                            'class': 'input-table-name-checkbox',
                            'name': 'subscribe-table-name',
                            'data-feed': this.key,
                            'data-domain': this.domain,
                            'id': 'table-' + this.key,
                        }).val(this.key)
                            .prop('checked', this.checked)
                    ));
                }
            });
        };

        $(".input-table-name-checkbox").on('change', function() {
            setCheckboxesForTableNames();
            setCheckboxesForRelationTypes();
        });

        $('.input-domain-checkbox').on('change', function() {
            const domain = $(this).data('domain');
            const checked = $(this).prop('checked');

            $(`.input-table-name-checkbox[data-domain=${domain}]`).each(function () {

                $(this).prop('checked', checked);
            });

            setCheckboxesForTableNames();
            setCheckboxesForRelationTypes();
        });
    }

    $("#input-relation-type-checkbox-check-all").on('change', function() {
        $(".input-relation-type-checkbox").prop('checked', this.checked);
        setCheckboxesForTableNames();
        setCheckboxesForRelationTypes();
    });

    $("#input-table-names-checkbox-check-all").on('change', function() {
        $(".input-table-name-checkbox, .input-domain-checkbox").prop('checked', this.checked);
        setCheckboxesForTableNames();
        setCheckboxesForRelationTypes();
    });

    function setCheckboxesForTableNames() {
        newNodeDataArray = []
        newLinkDataArray = []

        $(".input-table-name-checkbox").each(function() {
            if ($(this).prop('checked')) {

                for (let i = 0; i < nodeDataArray.length; i++) {
                    const element = nodeDataArray[i];
                    if (element.key == $(this).val()) {
                        newNodeDataArray.push(element)
                    }
                }
                for (let i = 0; i < linkDataArray.length; i++) {
                    const element = linkDataArray[i];
                    if (element.from == $(this).val() || element.to == $(this).val()) {
                        newLinkDataArray.push(element)
                    }
                }
            }
        });
        myDiagram.model.nodeDataArray = newNodeDataArray
        myDiagram.model.linkDataArray = newLinkDataArray
    }

    nodeDataArray = @json($node_data);
    linkDataArray = @json($link_data);

    window.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>
