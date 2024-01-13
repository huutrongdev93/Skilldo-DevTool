class DevToolSidebar {
	constructor() {

		this.sidebar = $('.devTools-sidebar');

		this.theme = localStorage.getItem('devTool-theme')

		console.log(this.theme)

		if(this.theme) {
			console.log(this.theme)
			this.sidebar.addClass(this.theme)
		}

		this.tabElActive = localStorage.getItem('devTool-tab-active')

		if(this.tabElActive) {
			$(this.tabElActive).trigger('click');
		}

		this.event()
	}
	toggle() {
		this.sidebar.toggleClass('open')
		return false
	}
	clickTab(element) {
		localStorage.setItem('devTool-tab-active', '#' + element.attr('id'))
	}
	cacheClear(element) {

		let button = SkilldoHelper.buttonLoading(element);

		button.loading()

		let data =  {
			action: 'DevToolAjax::cacheClear',
			type: element.data('type')
		}

		request.post(ajax, data).then(function(response) {
			button.success()
			SkilldoHelper.message.response(response);
		})
			.catch(function(error) {
				button.success()
			})

		return false
	}
	saveLayout(element) {

		let button = SkilldoHelper.buttonLoading('button[form="devTools-form-layout"]');

		button.loading()

		let data = element.serializeJSON();

		data.action = 'DevToolAjax::saveLayout'

		request.post(ajax, data).then(function (response) {
			button.success();
			SkilldoHelper.message.response(response);
		})
			.catch(function (error) {
				button.success();
			});

		return false;
	}
	debugBarAjax(element) {

		const self = this;

		let button = SkilldoHelper.buttonLoading(element);

		let data = element.serializeJSON();

		data.action = 'DevToolAjax::debugBarAjax';

		button.loading()

		request.post(ajax, data).then(function (response) {

			button.success();

			SkilldoHelper.message.response(response);

			response.data.html = decodeURIComponent(atob(response.data.html).split('').map(function (c) {
				return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
			}).join(''));

			self.sidebar.find('#ci_profiler_ajax .ci_profiler_ajax_query').html(response.data.html);
		})
		.catch(function (error) {
			button.success();
		});

		return false;
	}
	themeDark(element) {

		let button = SkilldoHelper.buttonLoading(element);

		button.loading()

		this.sidebar.addClass('dark')

		localStorage.setItem('devTool-theme', 'dark');

		button.success();

		return false;
	}
	themeLight(element) {

		let button = SkilldoHelper.buttonLoading(element);

		button.loading()

		this.sidebar.addClass('light')

		this.sidebar.removeClass('dark')

		localStorage.setItem('devTool-theme', 'light');

		button.success();

		return false;
	}
	event() {

		const self = this;

		$(document)
			.on('click', '.devTools-btn-toggle', function () {
				return self.toggle();
			})
			.on('click', '.devTools-btn-close', function () {
				return self.toggle();
			})
			.on('click', '#devTools-tabs li button', function () {
				return self.clickTab($(this))
			})
			.on('click', '.devTools-btn-cache', function () {
				return self.cacheClear($(this));
			})
			.on('click', '.devTools-theme-dark', function () {
				return self.themeDark($(this));
			})
			.on('click', '.devTools-theme-light', function () {
				return self.themeLight($(this));
			})
			.on('click', '#devTools-debug-ajax', function () {
				return self.debugBarAjax($(this));
			})
			.on('submit', '#devTools-form-layout', function () {
				return self.saveLayout($(this));
			})
	}
}

$(function(){
	new DevToolSidebar();
});