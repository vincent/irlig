
<?php
	
//*****************************************************************************************************************************************************************
// A* ou AStar est un Algorithme de Pathfinding permettant de déterminer quel est le chemin le plus court (dans la plupart des cas) entre un point de départ et un point d'arrivée en tenant compte des "obstacles"
// Le principe étant d'analyser toutes cases autour de la case courante (la première étant le départ) et leur donner des valeurs qui permettront de savoir si elles interessantes pour etre examinés afin de trouver l'arrivée 
// Ces valeurs prennent en compte la distance qui sépare (grossièrement avec la méthode Manhattan - voir les explications dans le code) la case et l'arrivée et le coût d'un déplacement pour relier une case à une autre
// Le cumul des valeurs de déplacement et de distance indique une valeur de reference (la valeur F) et donne à chaque case une évaluation qui permet de chercher le chemin
// Les cases avec le F le plus bas sont potentiellement bonnes et sont celles qui seront les suivantes à etre analysées.
// A chaque nouvelle case analysée, une case parente est indiquée pour determiner d'ou elle vient. Une case peut changer de parent (tres important) si l'analyse depuis une nouvelle case Parent montre un coût plus intéressant que précédement
// Une fois qu'une analyse tombe sur la case d'arrivée c'est qu'un chemin a été trouvé. Il suffit de remonter les Parents de chaque case depuis l'arrivée jusqu'à la case parent originale, le départ.
// Pour savoir quelles sont les cases encore a traiter on fait entrer les cases en cours d'analyse dans une Liste Ouverte. Les cases Parents entre dans une Liste Fermée et sorte de la liste Ouverte.
// Tutorial de cet Algorithme en français : http://www.lostonthepath.com/nc/03.12.2008/trd.htm
//  L'auteur original se nomme Patrick Lester et l'article original se situe à l'adresse suivante : http://www.policyalmanac.org/games/aStarTutorial.htm
// Adaptation personnelle en PHP de cet Algorithme.
//*****************************************************************************************************************************************************************
class Astar
{
	var $listeouverte;
	var $listeparent;
	var $listeferme;
	var $coutG;
	var $coutF;
	var $progressionX;
	var $progressionY;
	var $diagonal = 14; // 14 pour une preference à la diagonale | 34 pour une preference à la ligne droite quand c'est possible
	
	
	//*****************************************************************************************************************************************************************
	// Analyse des 9 neuf cases depuis la case en cours et indique pour chacune le "poids" des cases (avec les valeurs G, H et F)
	//*****************************************************************************************************************************************************************
	function neufCase($destinationX, $destinationY, &$obj_renduvisuel, $time_start)
	{
		$cp = 0;
		// Double boucle pour analyser toutes les cases autour de la courante
		for ($caseY = $this->progressionY - 1; $caseY <= $this->progressionY + 1; $caseY++)
		{
			for ($caseX = $this->progressionX - 1; $caseX <= $this->progressionX + 1; $caseX++)
			{
				// Si la case en cours d'analyse n'est pas un mur et n'est pas dans la liste ouverte 
				if (!isset($obj_renduvisuel->mur[$caseX][$caseY]) && !isset($this->listeouverte[$caseX][$caseY]))
				{
					// Si la case en cours n'est pas une case déjà analysée (liste fermée) C'est donc une case vierge 
					// On procede à son entrée en listeouverte et on note ses valeurs
					if (!isset($this->listeferme[$caseX][$caseY])) 
					{	
						// La case entre en liste ouverte, c'est à dire qu'elle est traitée et qu'elle est dans la liste des cases "regardable"
						$this->listeouverte($caseX, $caseY);
						// On indique à la case en cours d'analyse de qui elle dépend (la case en cours 'progression')
						$this->listeparent($caseX, $caseY);
						// On calcul sa valeur de déplacement (en cumulé avec la case parent)
						$this->calculcoutG($caseX, $caseY);
						// On calcul la distance de la case en cours d'analyse avec l'arrivée en additionnant le nombre de case en X et en Y
						$this->calculManathan($caseX, $caseY, $destinationX, $destinationY);
						// On calcul le F de la case, c'est à dire la valeur cumulé de G et H qui determine la qualité de la case (Un F petit est meilleur)
						$this->calculF($caseX, $caseY);
					}
				}
				// La case a déjà été analysée, elle est dans la liste ouverte (et donc) n'a jamais été une une case "Parent" 
				// On regarde si depuis la case en cours ('progression') elle est plus interessante que l'analyse précedente
				elseif (isset($this->listeouverte[$caseX][$caseY]))
				{	
					// On regarde si la case en cours d'analyse est en ligne droite ou en diagonale depuis la case en cours 'progression'
					$G = $this->testDirection($caseX, $caseY, $this->progressionX, $this->progressionY);
					
					// On verifie si depuis la case en cours 'progression' le cout en deplacement est meilleurs que celui qui existe sur cette case
					// Si c'est le cas, on recalcule le cout G, H et F et on change le parent de la case en cours d'analyse
					if ($this->coutG[$this->progressionX][$this->progressionY] + $G < $this->coutG[$caseX][$caseY])
					{	
						// La case en cours d'analyse devient Parent de la case nouvelle case 'progression'
						$this->listeparent($caseX, $caseY);
						// On racalcul la valeur G
						$this->calculcoutG($caseX, $caseY);
						// On indique la nouvelle valeur F pour la case
						$this->calculF($caseX, $caseY);
					}
				}
				
				// Verification si il reste un chemin possible
				if (!isset($this->listeouverte[$caseX][$caseY])) 
				{ 
					$cp++;
					if ($cp == 9) 
					{ 
						echo "pas de chemin possible<br>";
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						$T = number_format ($time, 4);
						echo "<br>Resolution en <b>$T</b> seconde";
						exit();	
					}
				}
			}
		}
	}
	
	
 	//***********************************************************************************************************************
	// Si une case "Parent" est traitée elle entre dans la liste fermée et elle sort de la liste ouverte
	//***********************************************************************************************************************
	function listeferme()
	{	
		$this->listeferme[$this->progressionX][$this->progressionY] = array($this->progressionX, $this->progressionY);
		unset($this->listeouverte[$this->progressionX][$this->progressionY]);
	}
	
	
	//*****************************************************************************************************************************************************************
	// Fait entrer la case en cours d'analyse dans une Liste Ouverte
	//*****************************************************************************************************************************************************************
	function listeouverte($caseX, $caseY)
	{
		$this->listeouverte[$caseX][$caseY] = array($caseX, $caseY);
	}
	
	
	//************************************************************
	// Donne à une case les coordonnées de sa case parent
	//************************************************************
	function listeparent($caseX, $caseY)
	{
		$this->listeparent[$caseX][$caseY] = array ($this->progressionX, $this->progressionY);	
	}	

	
	//*****************************************************************************************************************************************************************
	// Calcul du deplacement en valeur cumulé
	//*****************************************************************************************************************************************************************
	function calculcoutG($caseX, $caseY)
	{
		// On retrouve les coordonées de la case Parent de la case en cours de traitement
		$parentX = $this->listeparent[$caseX][$caseY][0];
		$parentY = $this->listeparent[$caseX][$caseY][1];
		
		// On regarde si c'est une case en diagonal ou en ligne de droite depuis la case parent.
		$G = $this->testDirection($caseX, $caseY, $parentX, $parentY);
			
		// Si la case en cours d'anamyse n'a jamais été visité, elle prend zero pour valeur par défaut
		if (!isset($this->coutG[$caseX][$caseY]))
			{ 	$this->coutG[$caseX][$caseY] = 0;		}
			
		// Si la case parent n'a jamais eu de cout, elle prend zero comme valeur par defaut
		if (!isset($this->coutG[$parentX][$parentY]))
			{ 	$this->coutG[$parentX][$parentY] = 0;	}
			
		// On donne à la case en cours d'analyse le cumul de la case Parent + la valeur de direction
		$this->coutG[$caseX][$caseY] = $this->coutG[$parentX][$parentY] + $G;
	}
	
	
	//*****************************************************************************************************************************************************************
	// Si la case Parent à une valeur commune avec ses coordonées X ou Y c'est que c'est une droite, autrement c'est une diagonale
	//*****************************************************************************************************************************************************************
	function testDirection($caseX, $caseY, $parentX, $parentY)
	{
		if ($parentX == $caseX || $parentY == $caseY)
			{	$G = 10;	}
		else
			{	$G = $this->diagonal;	}  
		
		return $G;
	}
	
	
	//*****************************************************************************************************************************************************************
	// On determine la valeur de H par la methode dite de Manhattan c'est à dire le cumul de la distance X et Y (sans passer par une diagonale) depuis la case jusqu'à l'arrivée
	//*****************************************************************************************************************************************************************
	function calculManathan($caseX, $caseY, $destinationX, $destinationY)
	{
		// Difference entre la case en cours et la case d'arrivée
		$distx = abs($destinationX - $caseX);
		$disty = abs($destinationY - $caseY);
		
		// Cumul des 2 valeurs
		$H = $distx + $disty;
		// On multiplie le resultat pour le rendre parfaitement distinct sensible
		$this->coutH[$caseX][$caseY] = $H * 10;
	}
	
	
	//******************************************************************************************************************************************************************************
	// On donne à la case une valeur F qui est le cumul entre G et H. Cette valeur servira de reference pour savoir qu'elle doit etre la prochaine case à prendre pour la progression
	//******************************************************************************************************************************************************************************
	function calculF($caseX, $caseY)
	{
		$this->coutF[$caseX][$caseY] = $this->coutH[$caseX][$caseY] + $this->coutG[$caseX][$caseY];
	}
	
	//***********************************************************************************************************************************************************
	// Pour connaitre quelle case va etre la suivante pour tester un chemin, on determine celle qui le poid le plus petit (donc potentiellement la plus proche de l'arrivée)
	//***********************************************************************************************************************************************************
	function plusPetitF()
	{
		$distance = 9999999; // Valeur au dessus de toute par defaut
		// Pour toutes les case avec une valeur F on extrait les coordonées X et Y ($id1 et îd2)
		foreach ($this->coutF as $id1 => $contenu)
		{
			foreach ($contenu as $id2 => $contenu2)
			{
				// On garde uniquement les coordonées de la valeur F la plus petite
				if ($this->coutF[$id1][$id2] < $distance && isset($this->listeouverte[$id1][$id2]))
				{	
					// Nouvelle plus petite distance en cours de verification 
					$distance = $this->coutF[$id1][$id2]; 
					// Nouvelle coordonnées de la case "gagnante" en cours de verification 
					$this->progressionX = $id1; 
					$this->progressionY = $id2;	
				}
			}
		}
	}
}

?>