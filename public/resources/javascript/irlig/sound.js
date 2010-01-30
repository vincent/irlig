IRL_IG.snd = {

	music_volume: 50,
	effect_volume: 50,
	
	loaded: {},
	
	play_effect: function(filename) {
		var loaded = this.loaded.get(filename);
		if (!loaded) {
			this.loaded.set(filename, soundManager.createSound({
			    id: filename,
			    url: filename,
			    volume: this.effect_volume
			}));
			loaded = this.loaded.get(filename);
		}
		loaded.play();
	},
	
	play_music: function(filename) {
		var loaded = this.loaded.get(filename);
		if (!loaded) {
			this.loaded.set(filename, soundManager.createSound({
			    id: filename,
			    url: filename,
			    volume: this.music_volume
			}));
			loaded = this.loaded.get(filename);
		}
		loaded.play();
	}

}