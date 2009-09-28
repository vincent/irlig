IRL_IG.classes.ground = Class.create();
Object.extend(IRL_IG.classes.ground.prototype, {

	element: null,

	initialize: function(id) {
		this.element = $(id);

	},

	registerMainPlayer: function(player) {
		if (!player) IRL_IG.debug('No player given');

		Event.observe(this.element, 'click', function(event){
			player.moveTo(event.pointerX(), event.pointerY());
		});
	}

});