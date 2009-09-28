IRL_IG.classes.player = Class.create();
Object.extend(IRL_IG.classes.player.prototype, {

	element: null,

	position: {
		x:0,
		y:0
	},

	initialize: function(id, options) {
		this.element = $(id);

	},

	moveTo: function(x, y) {
		IRL_IG.debug('player %o goes (%s,%s)', this, x, y);
	}

});