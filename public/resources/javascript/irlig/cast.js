IRL_IG.classes.cast = Class.create({

	o: {
		spell_time: 100,         // the cast duration (affected by IRL_IG.player.spell_speed_factor)
		
		onStart:    Prototype.K, // called when the cast starts
		onFinish:   Prototype.K, // called when the cast ends (completed or aborted)
		onComplete: Prototype.K, // called when the cast is completed
		onAbort:    Prototype.K  // called when the cast is aborted
	},
	
	initialize: function(options) {
	
		this.o = Object.extend(this.o, options);
	
		// Get elements
		this.castbar_front_def = $$('#castbar .cast_bar_front')[0];
		this.castbar = IRL_IG.ground.addElement('use', { 'xlink:href':'#castbar' });
		var castbar_dims = castbar.getTBBox();

		// Place the cast bar
		this.castbar.translate(
			(document.documentElement.clientWidth / 2) - (castbar_dims.width / 2),
			(document.documentElement.clientHeight / 2) - (castbar_dims.height / 2)
		);
		
		// Set cast width to 0
		this.max_value = this.castbar_front_def.getAttributeNS(null, 'width');
		this.castbar_front_def.setAttributeNS(null, 'width', '0');

		this.time_start = new Date();
		this.o.onStart(this);
		this.timer = setTimeout(this.run.bind(this), 1);
	},

	run: function() {
		var now = new Date();

		// Spell is over
		if ((this.o.spell_time * IRL_IG.player.spell_speed_factor) - (now - this.time_start) <= 0) {
			this.castbar_front_def.setAttributeNS(null, 'width', this.max_value);
			this.castbar.remove();
			this.o.onFinish(this);
			this.o.onComplete(this);
			return;
		}

		var progress = parseInt(this.max_value * ((now - this.time_start) / this.o.spell_time));
		
		this.castbar_front_def.setAttributeNS(null, 'width',  progress);
		
		this.timer = setTimeout(this.run.bind(this), 50);
	},
	
	abort: function() {
		if (this.timer) clearTimeout(this.timer);
		this.o.onFinish(this);
		this.o.onAbort(this);
		this.castbar.remove();
		this.castbar_front_def.setAttributeNS(null, 'width', this.max_value);
	}

});