App.Model.WorkLog = Backbone.Model.extend({

});

App.Collection.WorkLogs = Backbone.Collection.extend({
    model: App.Model.WorkLog,
    url: function() {
        return Routing.generate('jira_reporting_worklog_json', {
            author: this.filter.author,
            from: this.filter.from,
            to: this.filter.to
        });
    },
    filter: {
        author: '',
        from: moment().format('DD-MM-YYYY'),
        to: moment().format('DD-MM-YYYY')
    },
    fetchByFilter: function(filter) {
        this.filter = filter;
        this.fetch({reset: true});
    }
});
