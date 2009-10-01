IRL_IG = {
	classes: {},

	ground: null,
	player: null,

	debug: function() {
		console.log(arguments[0],arguments[1],arguments[2],arguments[3]);
		//console.apply(console, [ 'log', 'plop' ])
	},

	init: function(name, grid) {

		/* No Firebug ? */
		if (typeof(console) == 'undefined')
			console = { log:function(){ new Error(arguments) } };

		window.IRL_IG = this;
		IRL_IG.debug('Game loaded %o', window.IRL_IG);

		this.ground = new IRL_IG.classes.ground('map', name, grid);
		this.hud = new IRL_IG.classes.hud('hud');
		this.player = new IRL_IG.classes.player('player_buddy');

		//false &&
		window.setTimeout(function(){
			this.ground.registerMainPlayer(this.player);
		}.bind(this), 5000);
	},

	swapChildren:  function(c1, c2) {
		var parent = c1.parentNode;
		var tmp = c1.cloneNode( true );
		parent.replaceChild( tmp, c2 );
		parent.replaceChild( c2, c1 );
		return tmp;
	}

};

document.observe('load', IRL_IG.init)
