IRL_IG.classes.hud = Class.create({

	element:null,

	elements: {
	},

	initialize: function(id, options) {
		this.element = $(id);
	},
	
	message: function(text, options) {
		var textElement = document.createElement('text');
		textElement.setAttribute(null, 'fill', 'red');
		textElement.setAttribute(null, 'font-size', '55');
		textElement.update(text);

		textElement.translate(
			(document.documentElement.clientWidth / 2) - (textElement.width / 2),
			(document.documentElement.clientHeight / 2) - (textElement.height / 2)
		);
		return textElement;
	}

});