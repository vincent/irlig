IRL_IG = {
	classes: {},

	ground: null,
	player: null,

	debug: function() {
		console.log(arguments[0],arguments[1],arguments[2],arguments[3]);
	},

	init: function(name, grid, mode) {

		/* No Firebug ? */
		if (typeof(console) == 'undefined')
			console = { log:function(){ new Error(arguments) } };

		window.IRL_IG = this;
		IRL_IG.debug('Game loaded %o', window.IRL_IG);

		this.ground = new IRL_IG.classes.ground('map', name, grid, mode);

		if (mode == 'edit') {
			this.hud = new IRL_IG.classes.edit_hud('edit_hud');

		}
		else {

			this.hud = new IRL_IG.classes.hud('hud');
			this.player = new IRL_IG.classes.player('player_buddy');

			window.setTimeout(function(){
				this.ground.registerMainPlayer(this.player);
			}.bind(this), 2000);
		}
	}

};

document.observe('load', IRL_IG.init)
