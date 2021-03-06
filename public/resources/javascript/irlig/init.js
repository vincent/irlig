var grid_assoc;
IRL_IG = {
	classes: {},

	ground: null,
	player: null,

	debug: function() {
		console.log(arguments[0],arguments[1],arguments[2],arguments[3]);
	},

	init: function(name, grid, mode) {

		grid_assoc = grid;
		grid = $A(grid);
		
		/* No Firebug ? */
		if (typeof(console) == 'undefined')
			console = { log:function(){ new Error(arguments) } };

		window.IRL_IG = this;
		IRL_IG.debug('Game loaded %o', window.IRL_IG);

		this.ground = new IRL_IG.classes.ground('map', name, grid, mode);

		this.keys = new IRL_IG.classes.keys();

		if (mode == 'edit') {
			this.edit_hud = new IRL_IG.classes.edit_hud('edit_hud');

		}
		else {
			this.hud = new IRL_IG.classes.hud('hud');
			//this.player = new IRL_IG.classes.player('player_buddy');
			this.player = new IRL_IG.classes.player('player_riku');

			window.setTimeout(function(){
				this.ground.registerMainPlayer(this.player);
			}.bind(this), 2000);

			var musicfile = false;
			if (musicfile = $$('#map #music')[0]) {
				window.setTimeout(function(){
					IRL_IG.snd.play_music(musicfile);
				}.bind(this), 3000);
			}
			
			var night = 1;
			var sunlight_interval;
			var increase_sunlight = function(){
				night = night - 0.02;

				if (night < 0) {
					clearInterval(sunlight_interval);
					$('sunlight').remove();
					console.log('Morning !');
					return;
				}

				$('sunlight').setAttributeNS(null, 'fill-opacity', night);
			}
			window.setTimeout(function(){
				sunlight_interval = window.setInterval(increase_sunlight, 2);
			}.bind(this), 200);
		}
	}

};

document.observe('load', IRL_IG.init)
