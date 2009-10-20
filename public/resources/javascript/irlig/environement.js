IRL_IG.environement = {

	animWater: function(water_pattern_id, amplitude) {
		var pat = $(water_pattern_id);

		pat.amplitude_max = parseInt(amplitude);
		pat.amplitude_current = 0;
		pat.amplitude_direction = -1;

		var animate = function() {

			this.setAttributeNS(null, 'x', this.amplitude_current);
			//pat.setAttributeNS(null, 'height', h-current);

			if (Math.abs(this.amplitude_current) > this.amplitude_max)
				this.amplitude_direction = -this.amplitude_direction;

			this.amplitude_current += this.amplitude_direction;
		}

		setInterval(animate.bind(pat), 100);
	}


};