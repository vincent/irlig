
<?php

//*****************************************************************************************************************************************************************
// Classe graphique pour montrer le chemin emprunté grace au Astar ainsi que pour montrer les valeurs utiles
//*****************************************************************************************************************************************************************
class RenduVisuel
{
	var $grilleaccessiblex = 90; // Taille de la grille en X 
	var $grilleaccessibley = 50; // Taille de la grille en Y (avec X = 91 et Y = 33 ça tient dans une resolution de 1280 - Si cela ne tient pas, on peut faire un "CTRL -" avec son navigateur pour reduire la taille d'affichage
	var $densitemax = 0; // En % represente la densité de mur (0 = vide 20 = tres tres rempli (bcp de chemin impossible) idéalement entre 5 et 15
	var $mur;
	var $chemin;
	
	//*****************************************************************************************************************************************************************
	// Delimitation de la grille par des bordure "mur" infranchissable (Sans borudre le chemin sera trouvé mais en dehors des limites visuelles de la grille)
	//*****************************************************************************************************************************************************************
	function bordureGrille()
	{
		// Ligne du Haut
		for ($x = 1; $x <= $this->grilleaccessiblex; $x++)
			{	$this->mur[$x][1] = 1;	}
		
		// Colonne de Droite
		for ($y = 1; $y <= $this->grilleaccessibley; $y++)
			{	$this->mur[$this->grilleaccessiblex][$y] = 1;	}
				
		// Colonne de Gauche
		for ($y = 1; $y <= $this->grilleaccessibley; $y++)
			{	$this->mur[1][$y] = 1;	}
			
		// Ligne du Bas
		for ($x = 1; $x <= $this->grilleaccessiblex; $x++)
			{	$this->mur[$x][$this->grilleaccessibley] = 1;	}
	}
	
	
	//*************************************************************************************************************************************************************************************************
	// Generateur de mur dans la grille pour former une sorte de labyrinthe : Choix d'un point, une longueur et d'une direction qu'on repete x fois pour tracer les murs
	//*************************************************************************************************************************************************************************************************
	function generationMur()
	{
		// Prise en compte de la densité du Labyrinthe
		$maxmur = intval(($this->grilleaccessiblex * $this->grilleaccessibley) * $this->densitemax / 100);
		$max = rand (0, $maxmur);
		// Boucle de generation des murs
		for ($nbmur = 1; $nbmur < $max; $nbmur++)
		{
			// Coeff d'Allongement du mur 
			$coef = rand(1, 2);
			// Definition de la longueur du mur en cours (ici 10% de la taille X de la grille * par le coefficient)
			$longx = intval(rand(1, $this->grilleaccessiblex) * 10 / 100) * $coef;
			$longy = intval(rand(1, $this->grilleaccessibley) * 10 / 100) * $coef;
			
			// On prend un point au hasard dans la grille
			$coordx = rand(1, $this->grilleaccessiblex);
			$coordy = rand(1, $this->grilleaccessibley);
			
			// On prend une direction aleatoire
			$direction = rand(1, 4);
			
			// Selon la direction on va "etirer" le mur
			switch ($direction)
			{
				// Le mur va vers le Haut 
				case 1:
					$maxy = $coordy - $longy;
					if ($maxy < 1) 
						{ 	$maxy = 1; }
					
					for ($y = $coordy; $y > $maxy; $y--) 
						{ 	$this->mur[$coordx][$y] = 1; }
				break;
				
				// Le mur va vers le Droite 
				case 2:
					$maxx = $coordx + $longx;
					if ($maxx > $this->grilleaccessiblex) 
						{ 	$maxx = $this->grilleaccessiblex; }
					
					for ($x = $coordx; $x < $maxx; $x++) 
						{ 	$this->mur[$x][$coordy] = 1; }
				break;
				
				// Le mur va vers le Bas
				case 3:
					$maxy = $coordy + $longy;
					if ($maxy > $this->grilleaccessibley) 
						{ $maxy = $this->grilleaccessibley; }
					
					for ($y = $coordy; $y < $maxy; $y++) 
						{ 	$this->mur[$coordx][$y] = 1; }
				break;
				
				// Le mur va vers la Gauche
				case 4:
					$maxx = $coordx - $longx;
					if ($maxx < 1) 
						{ $maxx = 1; }
					for ($x = $coordx; $x > $maxx; $x--) 
						{ 	$this->mur[$x][$coordy] = 1; }
				break;
			}
		}	
	}
	
	
	//*****************************************************************************************************************************************************************
	// Generation de la grille avec les informations utile comme les valeurs des couts F G H et les cases parentes plus le bon chemin
	//*****************************************************************************************************************************************************************
	function grille($departX, $departY, $destinationX, $destinationY, $obj_astar)
	{
		// Table HTML
		echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
		// Double boucle pour la creation de la grille, ici le sens Y
		for ($caseY = 1; $caseY <= $this->grilleaccessibley; $caseY++)
		{
			echo "<tr>";
			// Boucle des X de la grille
			for ($caseX = 1; $caseX <= $this->grilleaccessiblex; $caseX++)
			{
				$contenu = "";  // $contenu = "<font size = \"1\" color = \"purple\">$caseX $caseY</font><br>";     <<< Ajouter ce contenu pour avoir une numerotation des cases
				
				// ************** Cette section n'est utile que pour voir les valeurs du Astar pour analyse (choisir une grille de 20 * 20 pour que cela soit lisible ********************************************
				 	/*
				$G = ""; $H = ""; $F = ""; $blanc = ""; $parentX = ""; $parentY = "";
				
				// Si le cout F pour la case existe en l'ajoute dans le Contenu (Rappel : F = La somme de G et H)
				if (isset($obj_astar->coutF[$caseX][$caseY]))
					{	$F = $obj_astar->coutF[$caseX][$caseY];	$contenu = "<font size = \"1\">".$contenu.$F."</font><br>";	}
				
				// Si le cout G pour la case existe en l'ajoute dans le Contenu (Rappel : G = le cout de deplacement en cumul depuis la case parente (10 pour la ligne droite et une valeur superieure pour la diagonale (14 ou 34)))
				if (isset($obj_astar->coutG[$caseX][$caseY]))
					{	$G = $obj_astar->coutG[$caseX][$caseY];	$contenu = "<font size = \"1\">".$contenu.$G."</font> ";	}
				
				// Si le cout H pour la case existe en l'ajoute dans le Contenu (Rappel : H = le nombre de case qui separe l'arrivée en addtionnant X + Y)
				if (isset($obj_astar->coutH[$caseX][$caseY]))
					{	$H = $obj_astar->coutH[$caseX][$caseY];	$contenu = "<font size = \"1\">".$contenu.$H."</font><br>";	}				
				
				// Affiche les valeurs de la case parent de la case en cours 
				if (isset($obj_astar->listeparent[$caseX][$caseY][0]))
				{
					$parentX = $obj_astar->listeparent[$caseX][$caseY][0];
					$parentY = $obj_astar->listeparent[$caseX][$caseY][1];
					$contenu = "<font size = \"1\">".$contenu.$parentX." ".$parentY."</font>";
				}
					*/
				
				// Si c'est une Case du bon Chemin on pose une marque (par defaut une *)
				if (isset($this->chemin[$caseX][$caseY]))
					{	$contenu = $contenu."<font color = \"BLUE\">*</font>"; 		}
				// Affiche un blanc (case vide)
				else
					{	$contenu = "&nbsp;";	}
					
				// Si c'est la case Depart on montre un O vert
				if ($caseX == $departX && $caseY == $departY) 
					{	$contenu = "<font color = \"GREEN\">D</font>"; 		}
				// Sinon si c'est la case Arrivée on montre un O rouge
				elseif ($caseX == $destinationX && $caseY == $destinationY) 
					{	$contenu = "<font color = \"RED\">A</font>";		}
				
				// Si la case est un mur on rempli la case (par defaut un [])
				if (isset($this->mur[$caseX][$caseY]))
					{	$contenu = "<font color = \"BLACK\" size = \"3\" >[]</font>";		}
				
				// On crée la cellule avec le "Contenu"
				echo "<th>$contenu</th>";
			}
			
			echo "</tr>";
		}
		
		echo "</table>";
	}	
	
}


?>