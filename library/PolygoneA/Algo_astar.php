
<?php
	
//*****************************************************************************************************************************************************************
// A* ou AStar est un Algorithme de Pathfinding permettant de d�terminer quel est le chemin le plus court (dans la plupart des cas) entre un point de d�part et un point d'arriv�e en tenant compte des "obstacles"
// Le principe �tant d'analyser toutes cases autour de la case courante (la premi�re �tant le d�part) et leur donner des valeurs qui permettront de savoir si elles interessantes pour etre examin�s afin de trouver l'arriv�e 
// Ces valeurs prennent en compte la distance qui s�pare (grossi�rement avec la m�thode Manhattan - voir les explications dans le code) la case et l'arriv�e et le co�t d'un d�placement pour relier une case � une autre
// Le cumul des valeurs de d�placement et de distance indique une valeur de reference (la valeur F) et donne � chaque case une �valuation qui permet de chercher le chemin
// Les cases avec le F le plus bas sont potentiellement bonnes et sont celles qui seront les suivantes � etre analys�es.
// A chaque nouvelle case analys�e, une case parente est indiqu�e pour determiner d'ou elle vient. Une case peut changer de parent (tres important) si l'analyse depuis une nouvelle case Parent montre un co�t plus int�ressant que pr�c�dement
// Une fois qu'une analyse tombe sur la case d'arriv�e c'est qu'un chemin a �t� trouv�. Il suffit de remonter les Parents de chaque case depuis l'arriv�e jusqu'� la case parent originale, le d�part.
// Pour savoir quelles sont les cases encore a traiter on fait entrer les cases en cours d'analyse dans une Liste Ouverte. Les cases Parents entre dans une Liste Ferm�e et sorte de la liste Ouverte.
// Tutorial de cet Algorithme en fran�ais : http://www.lostonthepath.com/nc/03.12.2008/trd.htm
//  L'auteur original se nomme Patrick Lester et l'article original se situe � l'adresse suivante : http://www.policyalmanac.org/games/aStarTutorial.htm
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
	var $diagonal = 14; // 14 pour une preference � la diagonale | 34 pour une preference � la ligne droite quand c'est possible
	
	
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
					// Si la case en cours n'est pas une case d�j� analys�e (liste ferm�e) C'est donc une case vierge 
					// On procede � son entr�e en listeouverte et on note ses valeurs
					if (!isset($this->listeferme[$caseX][$caseY])) 
					{	
						// La case entre en liste ouverte, c'est � dire qu'elle est trait�e et qu'elle est dans la liste des cases "regardable"
						$this->listeouverte($caseX, $caseY);
						// On indique � la case en cours d'analyse de qui elle d�pend (la case en cours 'progression')
						$this->listeparent($caseX, $caseY);
						// On calcul sa valeur de d�placement (en cumul� avec la case parent)
						$this->calculcoutG($caseX, $caseY);
						// On calcul la distance de la case en cours d'analyse avec l'arriv�e en additionnant le nombre de case en X et en Y
						$this->calculManathan($caseX, $caseY, $destinationX, $destinationY);
						// On calcul le F de la case, c'est � dire la valeur cumul� de G et H qui determine la qualit� de la case (Un F petit est meilleur)
						$this->calculF($caseX, $caseY);
					}
				}
				// La case a d�j� �t� analys�e, elle est dans la liste ouverte (et donc) n'a jamais �t� une une case "Parent" 
				// On regarde si depuis la case en cours ('progression') elle est plus interessante que l'analyse pr�cedente
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
	// Si une case "Parent" est trait�e elle entre dans la liste ferm�e et elle sort de la liste ouverte
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
	// Donne � une case les coordonn�es de sa case parent
	//************************************************************
	function listeparent($caseX, $caseY)
	{
		$this->listeparent[$caseX][$caseY] = array ($this->progressionX, $this->progressionY);	
	}	

	
	//*****************************************************************************************************************************************************************
	// Calcul du deplacement en valeur cumul�
	//*****************************************************************************************************************************************************************
	function calculcoutG($caseX, $caseY)
	{
		// On retrouve les coordon�es de la case Parent de la case en cours de traitement
		$parentX = $this->listeparent[$caseX][$caseY][0];
		$parentY = $this->listeparent[$caseX][$caseY][1];
		
		// On regarde si c'est une case en diagonal ou en ligne de droite depuis la case parent.
		$G = $this->testDirection($caseX, $caseY, $parentX, $parentY);
			
		// Si la case en cours d'anamyse n'a jamais �t� visit�, elle prend zero pour valeur par d�faut
		if (!isset($this->coutG[$caseX][$caseY]))
			{ 	$this->coutG[$caseX][$caseY] = 0;		}
			
		// Si la case parent n'a jamais eu de cout, elle prend zero comme valeur par defaut
		if (!isset($this->coutG[$parentX][$parentY]))
			{ 	$this->coutG[$parentX][$parentY] = 0;	}
			
		// On donne � la case en cours d'analyse le cumul de la case Parent + la valeur de direction
		$this->coutG[$caseX][$caseY] = $this->coutG[$parentX][$parentY] + $G;
	}
	
	
	//*****************************************************************************************************************************************************************
	// Si la case Parent � une valeur commune avec ses coordon�es X ou Y c'est que c'est une droite, autrement c'est une diagonale
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
	// On determine la valeur de H par la methode dite de Manhattan c'est � dire le cumul de la distance X et Y (sans passer par une diagonale) depuis la case jusqu'� l'arriv�e
	//*****************************************************************************************************************************************************************
	function calculManathan($caseX, $caseY, $destinationX, $destinationY)
	{
		// Difference entre la case en cours et la case d'arriv�e
		$distx = abs($destinationX - $caseX);
		$disty = abs($destinationY - $caseY);
		
		// Cumul des 2 valeurs
		$H = $distx + $disty;
		// On multiplie le resultat pour le rendre parfaitement distinct sensible
		$this->coutH[$caseX][$caseY] = $H * 10;
	}
	
	
	//******************************************************************************************************************************************************************************
	// On donne � la case une valeur F qui est le cumul entre G et H. Cette valeur servira de reference pour savoir qu'elle doit etre la prochaine case � prendre pour la progression
	//******************************************************************************************************************************************************************************
	function calculF($caseX, $caseY)
	{
		$this->coutF[$caseX][$caseY] = $this->coutH[$caseX][$caseY] + $this->coutG[$caseX][$caseY];
	}
	
	//***********************************************************************************************************************************************************
	// Pour connaitre quelle case va etre la suivante pour tester un chemin, on determine celle qui le poid le plus petit (donc potentiellement la plus proche de l'arriv�e)
	//***********************************************************************************************************************************************************
	function plusPetitF()
	{
		$distance = 9999999; // Valeur au dessus de toute par defaut
		// Pour toutes les case avec une valeur F on extrait les coordon�es X et Y ($id1 et �d2)
		foreach ($this->coutF as $id1 => $contenu)
		{
			foreach ($contenu as $id2 => $contenu2)
			{
				// On garde uniquement les coordon�es de la valeur F la plus petite
				if ($this->coutF[$id1][$id2] < $distance && isset($this->listeouverte[$id1][$id2]))
				{	
					// Nouvelle plus petite distance en cours de verification 
					$distance = $this->coutF[$id1][$id2]; 
					// Nouvelle coordonn�es de la case "gagnante" en cours de verification 
					$this->progressionX = $id1; 
					$this->progressionY = $id2;	
				}
			}
		}
	}
}

?>