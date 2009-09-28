IRL_IG.classes.hud = Class.create();
Object.extend(IRL_IG.classes.hud.prototype, {

	element:null,

	elements: {
		character:null
	},

	initialize: function(id, options) {
		this.element = $(id);

	}

});