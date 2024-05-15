var chart_config = {
    chart: {
        container: "#network-tree",
        connectors: {
            type: "step",
        },
        animateOnInit: false,
        node: {
            collapsable: false
        },
        animation: {
            nodeAnimation: "easeOutBounce",
            nodeSpeed: 700,
            connectorsAnimation: "bounce",
            connectorsSpeed: 700
        }
    },
    nodeStructure: treeData
};

tree = new Treant(chart_config);
