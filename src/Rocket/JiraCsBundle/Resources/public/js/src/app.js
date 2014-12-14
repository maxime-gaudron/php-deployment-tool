App.Router = Backbone.Router.extend({

    routes: {
        "": "index",
        ":author/:from/:to": 'showReport'
    },

    initialize: function(options) {
        this.currentView = new Backbone.View();
        this.issues = new App.Collection.WorkLogs(App.data.workLogs);
        this.filter = new App.View.Filter({
            collection: this.issues,
            el: $('#filters')
        });
    },

    render: function(selector, view) {
        this.currentView.remove();

        this.currentView = view;
        $(selector).html(this.currentView.render().el);
    },

    index: function() {
        this.filter.$el.submit();
    },

    showReport: function() {
        var view = new App.View.Issues({
            collection: this.issues
        });

        this.render('#work-logs', view);
    }
});

$(document).ready(function(){
    App.router = new App.Router();
    Backbone.history.start({pushState: true, root: Routing.generate('jira_reporting_worklog')});
});
