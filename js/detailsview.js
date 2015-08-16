/*
 * Copyright (c) 2015
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */

(function() {
	var TEMPLATE =
		'<div>' +
		'	<div class="detailFileInfoContainer">' +
		'	</div>' +
		'	<div>' +
		'		{{#if tabHeaders}}' +
		'		<ul class="tabHeaders">' +
		'		{{#each tabHeaders}}' +
		'		<li class="tabHeader" data-tabid="{{tabId}}" data-tabindex="{{tabIndex}}">' +
		'			<a href="#">{{label}}</a>' +
		'		</li>' +
		'		{{/each}}' +
		'		</ul>' +
		'		{{/if}}' +
		'		<div class="tabsContainer">' +
		'		</div>' +
		'	</div>' +
		'	<a class="close icon-close" href="#" alt="{{closeLabel}}"></a>' +
		'</div>';

	/**
	 * @class OCA.Files.DetailsView
	 * @classdesc
	 *
	 * The details view show details about a selected file.
	 *
	 */
	var DetailsView = OC.Backbone.View.extend({
		id: 'app-sidebar',
		tabName: 'div',
		className: 'detailsView',

		_template: null,

		/**
		 * List of detail tab views
		 *
		 * @type Array<OCA.Files.DetailTabView>
		 */
		_tabViews: [],

		/**
		 * List of detail file info views
		 *
		 * @type Array<OCA.Files.DetailFileInfoView>
		 */
		_detailFileInfoViews: [],

		/**
		 * Id of the currently selected tab
		 *
		 * @type string
		 */
		_currentTabId: null,

		/**
		 * Dirty flag, whether the view needs to be rerendered
		 */
		_dirty: false,

		events: {
			'click a.close': '_onClose',
			'click .tabHeaders .tabHeader': '_onClickTab'
		},

		/**
		 * Initialize the details view
		 */
		initialize: function() {
			this._tabViews = [];
			this._detailFileInfoViews = [];

			this._dirty = true;

			// uncomment to add some dummy tabs for testing
			//this._addTestTabs();
		},

		_onClose: function(event) {
			OC.Apps.hideAppSidebar();
			event.preventDefault();
		},

		_onClickTab: function(e) {
			var $target = $(e.target);
			e.preventDefault();
			if (!$target.hasClass('tabHeader')) {
				$target = $target.closest('.tabHeader');
			}
			var tabId = $target.attr('data-tabid');
			if (_.isUndefined(tabId)) {
				return;
			}

			this.selectTab(tabId);
		},

		_addTestTabs: function() {
			for (var j = 0; j < 2; j++) {
				var testView = new OCA.Files.DetailTabView({id: 'testtab' + j});
				testView.index = j;
				testView.getLabel = function() { return 'Test tab ' + this.index; };
				testView.render = function() {
					this.$el.empty();
					for (var i = 0; i < 100; i++) {
						this.$el.append('<div>Test tab ' + this.index + ' row ' + i + '</div>');
					}
				};
				this._tabViews.push(testView);
			}
		},

		template: function(vars) {
			if (!this._template) {
				this._template = Handlebars.compile(TEMPLATE);
			}
			return this._template(vars);
		},

		/**
		 * Renders this details view
		 */
		render: function() {
			var templateVars = {
				closeLabel: t('files', 'Close')
			};

			if (this._tabViews.length > 1) {
				// only render headers if there is more than one available
				templateVars.tabHeaders = _.map(this._tabViews, function(tabView, i) {
					return {
						tabId: tabView.id,
						tabIndex: i,
						label: tabView.getLabel()
					};
				});
			}

			this.$el.html(this.template(templateVars));

			var $detailsContainer = this.$el.find('.detailFileInfoContainer');

			// render details
			_.each(this._detailFileInfoViews, function(detailView) {
				$detailsContainer.append(detailView.get$());
			});

			if (!this._currentTabId && this._tabViews.length > 0) {
				this._currentTabId = this._tabViews[0].id;
			}

			this.selectTab(this._currentTabId);

			this._dirty = false;
		},

		/**
		 * Selects the given tab by id
		 *
		 * @param {string} tabId tab id
		 */
		selectTab: function(tabId) {
			if (!tabId) {
				return;
			}

			var tabView = _.find(this._tabViews, function(tab) {
				return tab.id === tabId;
			});

			if (!tabView) {
				console.warn('Details view tab with id "' + tabId + '" not found');
				return;
			}

			this._currentTabId = tabId;

			var $tabsContainer = this.$el.find('.tabsContainer');
			var $tabEl = $tabsContainer.find('#' + tabId);

			// hide other tabs
			$tabsContainer.find('.tab').addClass('hidden');

			// tab already rendered ?
			if (!$tabEl.length) {
				// render tab
				$tabsContainer.append(tabView.$el);
				$tabEl = tabView.$el;
			}

			// this should trigger tab rendering
			tabView.setFileInfo(this.model);

			$tabEl.removeClass('hidden');

			// update tab headers
			var $tabHeaders = this.$el.find('.tabHeaders li');
			$tabHeaders.removeClass('selected');
			$tabHeaders.filterAttr('data-tabid', tabView.id).addClass('selected');
		},

		/**
		 * Sets the file info to be displayed in the view
		 *
		 * @param {OCA.Files.FileInfoModel} fileInfo file info to set
		 */
		setFileInfo: function(fileInfo) {
			this.model = fileInfo;

			if (this._dirty) {
				this.render();
			}

			if (this._currentTabId) {
				// only update current tab, others will be updated on-demand
				var tabId = this._currentTabId;
				var tabView = _.find(this._tabViews, function(tab) {
					return tab.id === tabId;
				});
				tabView.setFileInfo(fileInfo);
			}

			_.each(this._detailFileInfoViews, function(detailView) {
				detailView.setFileInfo(fileInfo);
			});
		},

		/**
		 * Returns the file info.
		 *
		 * @return {OCA.Files.FileInfoModel} file info
		 */
		getFileInfo: function() {
			return this.model;
		},

		/**
		 * Adds a tab in the tab view
		 *
		 * @param {OCA.Files.DetailTabView} tab view
		 */
		addTabView: function(tabView) {
			this._tabViews.push(tabView);
			this._dirty = true;
		},

		/**
		 * Adds a detail view for file info.
		 *
		 * @param {OCA.Files.DetailFileInfoView} detail view
		 */
		addDetailView: function(detailView) {
			this._detailFileInfoViews.push(detailView);
			this._dirty = true;
		}
	});

	OCA.Files.DetailsView = DetailsView;
})();

