let empty = {
	options: [],
	args: []
};

function TerminalDevTool(){}

TerminalDevTool.commands = {
	'cms:version': {...empty},
	'cms:lang:build': {...empty},
	'cache:clear': {...empty},
	'theme:db:run': {...empty},
	'theme:db:create': {...empty},
	'theme:child:copy': {...empty},
	'pl': {...empty},
	'plugin': {...empty},
	'plugin:db:run': {...empty},
	'plugin:db:create': {...empty},
	'db:show': {...empty},
	'db:table': {...empty}
}

TerminalDevTool.echo = function(response, term) {
	if(response.data?.type) {
		if(response.data.type === 'plugins') {
			TerminalDevTool.plugins(response.data.plugins, term)
		}
		if(response.data.type === 'plugin-info') {
			TerminalDevTool.pluginInfo(response.data.plugin, term)
		}
	}
	else {
		term.echo(response.message, {newline: true});
		response.data.map(function(message, index) {
			term.echo(message, {newline: true});
		})
		term.resume();
	}
};

TerminalDevTool.plugins = function(plugins, term) {

	let data = [['Name', 'Active']];

	Object.keys(plugins).map(function(value) {
		data.push([value, plugins[value]]);
	})

	term.echo(ascii_table(data, true));

	term.resume();
};

TerminalDevTool.pluginInfo = function(plugin, term) {

	let data = [['Name', 'Value']];

	Object.keys(plugin).map(function(value) {
		data.push([value, plugin[value]]);
	})

	term.echo(ascii_table(data, true));

	term.resume();
};

TerminalDevTool.errors = function(response, term) {
	term.error(response.message, {newline: true});
	response.data.map(function(message, index) {
		term.error(message, {newline: true});
	})
	term.resume();
};

TerminalDevTool.color = function (name, string, bold = 'b') {
	let colors = {
		blue:   '#55f',
		green:  '#59ebed',
		grey:   '#999',
		red:    '#A00',
		yellow: '#FF5',
		violet: '#6871ff',
		white:  '#fff'
	}
	if (colors[name]) {
		return '[['+bold+';' + colors[name] + ';]' + string + ']';
	} else {
		return string;
	}
}

TerminalDevTool.autoCompletePath = function(command, callback) {
	let input = command.split(" ").pop(); // Lấy phần cuối cùng của lệnh (tức là phần mà người dùng đang gõ)
	let matches = [];

	// Lặp qua các đường dẫn và tìm các đường dẫn khớp
	for (var i = 0; i < TerminalDevTool.paths.length; i++) {
		if (TerminalDevTool.paths[i].startsWith(input)) {
			matches.push(TerminalDevTool.paths[i]);
		}
	}

	// Gọi callback với danh sách các đường dẫn khớp
	callback(matches);
}

TerminalDevTool.run = function (element) {

	let terminalDataElement = $('#terminal-data');

	TerminalDevTool.commands['theme:child:copy'].args = terminalDataElement.data('path-theme');

	let plugins = terminalDataElement.data('plugins');

	TerminalDevTool.commands['plugin'].args = plugins;
	TerminalDevTool.commands['plugin:db:run'].args = plugins;
	TerminalDevTool.commands['plugin:db:create'].args = plugins;

	$(element).terminal(function(command, term) {

		term.pause();

		let data = {
			action: 'DevToolAjax::terminal',
			command: command
		}

		request.post(ajax, data).then(function(response) {

			if(response.status === 'error') {
				TerminalDevTool.errors(response, term)
				return false;
			}

			TerminalDevTool.echo(response, term)
		})

	}, {
		autocompleteMenu: true,
		greetings: function () {
			return [
				TerminalDevTool.color('green', 'Cms SkillDo'),
				TerminalDevTool.color('white', ' version '),
				TerminalDevTool.color('yellow', '{{Cms::version()}}'),
				'\n',
				'Type command or access the link documentation for list commands'
			].join('');
		},
		prompt: function(command, term) {
			let url = domain.replace(/(^\w+:|^)\/\//, '');

			url = url.replace(/\//g, '');

			return [
				TerminalDevTool.color('green', 'root&#64;'),
				TerminalDevTool.color('violet', url),
				TerminalDevTool.color('white', ' via '),
				TerminalDevTool.color('violet', 'php-' + term.attr('data-php') + ':'),
				TerminalDevTool.color('blue', '~/'),
				'$ '
			].join('');
		},
		completion: function() {
			let term = this;
			// return promise if completion need to be async
			// in this example it's not needed, you can just retrun an array
			return new Promise(function(resolve) {
				let command = term.get_command();
				let name = command.match(/^([^\s]*)/)[0];
				let list = [];
				if (name) {
					let word = term.before_cursor(true);
					if (name === word) {
						list = Object.keys(TerminalDevTool.commands);
					} else if (command.match(/\s/)) {
						if (TerminalDevTool.commands[name]) {
							list = TerminalDevTool.commands[name].args;
						}
					}
				}
				resolve(list);
			});
		}
	});
}

class DevToolSidebar {
	constructor() {

		this.sidebar = $('.devTools-sidebar');

		this.sidebar.show()

		this.open = localStorage.getItem('devTool-open')

		if(this.open === 'true') {
			this.sidebar.addClass('open')
		}

		this.tabElActive = localStorage.getItem('devTool-tab-active')

		if(this.tabElActive) {
			$(this.tabElActive).trigger('click');
		}

		this.event()
	}
	toggle() {
		localStorage.setItem('devTool-open', !this.sidebar.hasClass('open'))
		this.sidebar.toggleClass('open')
		return false
	}
	clickTab(element) {
		localStorage.setItem('devTool-tab-active', '#' + element.attr('id'))
	}
	cacheClear(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.loading()

		let data =  {
			action: 'DevToolAjax::cacheClear',
			type: element.data('type')
		}

		request.post(ajax, data).then(function(response) {
			button.success()
			SkilldoMessage.response(response);
		})
		.catch(function(error) {
			button.success()
		})

		return false
	}
	saveLayout(element) {

		let button = SkilldoUtil.buttonLoading('button[form="devTools-form-layout"]');

		button.loading()

		let data = element.serializeJSON();

		data.action = 'DevToolAjax::saveLayout'

		request.post(ajax, data).then(function (response) {
			button.success();
			SkilldoMessage.response(response);
		})
			.catch(function (error) {
				button.success();
			});

		return false;
	}
	debugBarAjax(element) {

		const self = this;

		let button = SkilldoUtil.buttonLoading(element);

		let data = element.serializeJSON();

		data.action = 'DevToolAjax::debugBarAjax';

		button.loading()

		request.post(ajax, data).then(function (response) {

			button.success();

			SkilldoMessage.response(response);

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
	setTheme(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.loading()

		this.theme = element.attr('data-theme')

		this.sidebar.removeClass('light')

		this.sidebar.removeClass('dark')

		this.sidebar.addClass(this.theme)

		localStorage.setItem('devTool-theme', this.theme);

		let data =  {
			action: 'DevToolAjax::setting',
			name: 'theme',
			value: this.theme
		}

		request.post(ajax, data).then(function(response) {})

		button.success();

		return false;
	}
	setLayout(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.loading()

		this.layout = element.attr('data-layout')

		this.sidebar.removeClass('vertical-right')

		this.sidebar.removeClass('vertical-left')

		this.sidebar.removeClass('horizontal')

		this.sidebar.addClass(this.layout)

		localStorage.setItem('devTool-layout', this.layout);

		let data =  {
			action: 'DevToolAjax::setting',
			name: 'layout',
			value: this.layout
		}

		request.post(ajax, data).then(function(response) {})

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
			.on('click', '.devTools-btn-theme', function () {
				return self.setTheme($(this));
			})
			.on('click', '.devTools-btn-layout', function () {
				return self.setLayout($(this));
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
	TerminalDevTool.run('.terminal')

	new DevToolSidebar();
});