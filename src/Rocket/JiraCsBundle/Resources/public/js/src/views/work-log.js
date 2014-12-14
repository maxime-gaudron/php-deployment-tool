App.View.Filter = Backbone.View.extend({
    events: {
        "submit": "onSubmit",
        "changeDate .from": "onFromDateChange"
    },
    initialize: function() {
        this.$('.input-group.date').datepicker({
            format: "dd/mm/yyyy",
            weekStart: 1,
            todayBtn: false,
            calendarWeeks: true,
            autoclose: true
        });
        var filter = JSON.parse(window.localStorage.getItem('filter'));

        this.$('.from').datepicker('update', moment().weekday(1).startOf('week').subtract(7, 'days').toDate());
        this.$('.to').datepicker('update', moment().weekday(1).endOf('week').toDate());
        this.$('.to').datepicker('setStartDate', this.$('.from').datepicker('getDate'));

        if (_.isObject(filter)) {
            this.$('select[name="author"]').val(filter.author);
            this.$('select[name="author"]').trigger("chosen:updated");
        }
    },
    onSubmit: function(event) {
        event.preventDefault();
        var filter = {
            author: this.$(':selected').text(),
            from: moment(this.$('.from').datepicker('getDate')).format('DD-MM-YYYY'),
            to: moment(this.$('.to').datepicker('getDate')).format('DD-MM-YYYY')
        };
        window.localStorage.setItem('filter', JSON.stringify(filter));

        this.collection.remove(this.collection.models);

        if (filter.author.length > 0) {
            this.collection.fetchByFilter(filter);
            App.router.navigate(filter.author + '/' + filter.from + '/' + filter.to, {trigger: true});
        }
    },
    onFromDateChange: function(selectedDate) {
        var endOfTheWeek = moment(selectedDate.date).weekday(1).endOf('week');
        this.$('.to').datepicker('setStartDate', selectedDate.date);
        this.$('.to').datepicker('update', endOfTheWeek.toDate());
    }
});

App.View.Issues = Backbone.View.extend({
    initialize: function (options) {
        this.listenTo(this.collection, "reset", this.render);
        this.listenTo(this.collection, "add", this.renderItem);
    },
    render: function() {
        this.collection.each(this.renderItem, this);

        return this;
    },
    renderItem: function(worklog) {
        var view = new App.View.Issue({
            model: worklog
        });
        this.$el.append(view.render().el);
    }
});

App.View.Issue = Backbone.View.extend({
    template: _.template($('#template-work-log').html()),
    initialize: function (options) {
        _.bindAll(this, 'setupZeroClipboard', 'copyToClipboard');

        this.listenTo(this.model, "remove", this.removeView);
    },
    render: function() {
        var hours = moment.duration(this.model.get('sum') * 1000).hours();
        var minutes = moment.duration(this.model.get('sum') * 1000).minutes();

        var attributes = this.model.toJSON();
        attributes.id = moment(this.model.get('id'), 'DD/MM/YYYY').format('DD MMMM YYYY');
        this.$el.html(this.template(attributes));

        this.$('.total-time-hours').html(hours);
        this.$('.total-time-minutes').html(minutes);

        _.each(this.model.get('workLogs'), this.renderDetail, this);

        this.client = new ZeroClipboard(this.$('.copy-to-clipboard'));
        this.client.on('ready', this.setupZeroClipboard);

        return this;
    },
    renderDetail: function(workLog) {
        var view = new App.View.WorkLogDetail({
            workLog: workLog
        });
        this.$('.worklogs').append(view.render().el);
    },
    setupZeroClipboard: function( readyEvent ) {
        readyEvent.client.on("copy" , this.copyToClipboard);
    },
    copyToClipboard: function(event) {
        var data = _.reduce(this.model.get('workLogs'), this.createSummaryText, '', this);
        event.clipboardData.setData('text/plain', data);
    },
    createSummaryText: function(data, workLog) {
        return data + '[' + workLog.key + ']' + '\n' + workLog.comment + '\n\n';
    },
    removeView: function() {
        this.client.destroy();
        this.remove();
    }
});

App.View.WorkLogDetail = Backbone.View.extend({
    template: _.template($('#template-work-log-detail').html()),
    initialize: function(options) {
        this.workLog = options.workLog;
    },
    render: function() {
        this.$el.html(this.template(this.workLog));

        return this;
    }
});
