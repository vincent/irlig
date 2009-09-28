IRL_IG = {
	classes: {},

	debug: function() {
		console.log(arguments[0],arguments[1],arguments[2]);
	},

	init: function() {
		console.log('Game loaded');
	}
};

document.observe('load', IRL_IG.init)