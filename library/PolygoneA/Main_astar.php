
<?php

//************************************************************************************************************
// Main de l'Astar
//************************************************************************************************************
// On rend lisible le fichier du Labyrinthe et de l'algorithme Astar
require_once "Visuel_astar.php";
require_once "Algo_astar.php";	
	
// Instanciation de la classe visuel (Grille)
$obj_renduvisuel = new RenduVisuel;
// Instanciation de l'algorithme Astar
$obj_astar = new Astar;

// On crée un pseudo labyrinthe
// Pourtour en mur de la grille (Visuel_astar.php)
$obj_renduvisuel->bordureGrille();
// Generation des murs dans la grille (voir Visuel_astar.php pour changer la taille de la grille ou la densité des murs)
$obj_renduvisuel->generationMur();

// *****************************************************************************************************************************************************
// Découpage en nombre de tranches horizontales et verticales, plus le chiffre est élévé plus on tend vers un tracé avec peu variation
$nbzone = 5; // Valeur : 5, 7, 9, 11, 13, 15, 17, 21 (au delà le tracé tend à l'uniformité)
$ref = intval($nbzone / 2) + 1; // Valeur centrale de reference
$nbpoint = $ref * 4 - 4; // Nombre de points qui seront utlisés pour le tracé en fonction du nombre de zones

// Attribution à chaque zone d'un début en X et en Y
$n = 0;
$ecartx = intval($obj_renduvisuel->grilleaccessiblex / $nbzone);
$ecarty = intval($obj_renduvisuel->grilleaccessibley / $nbzone);
for ($y = 1; $y <= $nbzone; $y++)
{
	for ($x = 1; $x <= $nbzone; $x++)
	{	
		$n++;
		$zonex[$n] = $x * $ecartx; 
		$zoney[$n] = $y * $ecarty;	
	}	
}

// *************************************************************************************************************************************************************
// Début de la répartition des points
// Le but etant de former un diamant losange depuis la case centrale superieure en se deplaçant de points en points le long du diamant
// Avec 5 zones sur un damier (donc) de 25 cases on aura pour le chemin la Suite : 3 7 11 17 23 19 15 9
// *************************************************************************************************************************************************************

// On calcul quel est l'increment nécessaire pour passer à la valeur de la diagonale suivante selon la taille du decoupage en zone
$increment = $ref + $nbzone - 1 - $ref;
// Le nombre total de cases du damier
$nbcase = $nbzone * $nbzone;

// Il y a 3 ruptures centrale (à gauche, en bas et à droite) On determine la valeur de ces ruptures selon le découpage
$m1 = $nbcase - $nbzone + 1;
$milieu1 = (1 + $m1) / 2;

$m2 = $nbcase + $m1;
$milieu2 = $m2 / 2;

$m3 = $nbcase + $nbzone;
$milieu3 = $m3 / 2; $m2 = 0;

// On va lister la Suite qui va dessiner le diamant
$val = $ref;
for ($inc = 1; $inc <= $nbpoint; $inc++)
{
	// Changement de la valeur de l'increment selon le point de rupure (cheminement du haut du damier puis vers l'O puis vers le N puis vers l'E)  
	if ($val == $milieu1)
		{	$increment = $ref + $nbzone + 1 - $ref;			}
	elseif ($val == $milieu2)
		{	$increment = ($ref + $nbzone - 1 - $ref) * -1;	}
	elseif ($val == $milieu3)
		{	$increment = ($ref + $nbzone + 1 - $ref) * -1;	}
		
	$val = $val + $increment; $tab[$inc] = $val; 
}

// Pour toutes la valeurs clefs de la Suite on procede à un tirage au sort de points entre deux zones
// Ici les les deux Variables sont là pour forcer le diamant à casser un peu (selon le tirage) sa forme 
$p = 0; 
foreach ($tab as $id => $contenu)
{ 	
	$p++; $variable = 1; $variable2 = 1;
	
	// Milieu Gauche
	if ($contenu == $milieu1) 
		{ 	$variable = 2;	}
	
	// Milieu Droit
	if ($contenu == $milieu3) 
		{ 	$variable2 = 2;	}
	
	// Tirage au sort des points le long du "diamant"
	$tabarriveeX[$p] = rand ($zonex[$contenu] - $ecartx * $variable2 + 3, $zonex[$contenu] * $variable - 3);
	$tabarriveeY[$p] = rand ($zoney[$contenu] - $ecarty + 3, $zoney[$contenu] - 3);
}

// "Top Chrono" pour calculer le temps de resolution du chemin
$time_start = microtime(true);

// Le depart est le dernier point de la Liste (milieu haut)
$departX = $tabarriveeX[$nbpoint];
$departY = $tabarriveeY[$nbpoint];
unset($obj_renduvisuel->mur[$departX][$departY]); // On detruit le mur de la case de depart (au cas ou)

// La variable progression est la case en cours (pour l'initialisation on indique la case de Depart)
$obj_astar->progressionX = $departX;
$obj_astar->progressionY = $departY;

// ***********************************************************************************************************************
// Ici commence la boucle qui va effectuer le tracé, en fonction du nombre de points à relier
for ($i = 1; $i <= $nbpoint; $i++)
{
	// Si c'est le dernier point on donne à l'arrivée la valeur du dernier point (le premier de la Suite (3 dans un decoupage de 5 zones) afin de fermer la boucle
	if ($i == $nbpoint) 
	{ 
		$arriveeX = $tabarriveeX[$nbpoint]; 
		$arriveeY = $tabarriveeY[$nbpoint]; 
		unset($obj_renduvisuel->mur[$arriveeX][$arriveeY]); // On detruit le mur de la case de depart (au cas ou)
		unset ($obj_astar->listeferme[$arriveeX][$arriveeY]); // On retire cette case de la liste fermé pour la rendre accessible de nouveau
	}
	// Sinon on continue le tracé en prenant le point suivant pour le relier
	else
	{
		$arriveeX = $tabarriveeX[$i];
		$arriveeY = $tabarriveeY[$i];
		unset($obj_renduvisuel->mur[$arriveeX][$arriveeY]);  // On detruit le mur de la case d'arrivée (au cas ou)
	}
	
	// La case de départ entre en liste ouverte (initialisation)
	$obj_astar->listeouverte[$departX][$departY] = array($departX, $departY);
	// La case de départ à la valeur F la plus petite (initialisation)
	$obj_astar->coutF[$departX][$departY] = 0;	
	
//	echo "Depart : $departX $departY, Arrivée : $arriveeX $arriveeY<br>";
	
	// ***********************************************************************************************************************
	// Partie liée à l'algorithme Astar - Les boucles de recherche et de remonté du chemin final
	// ***********************************************************************************************************************
	// Boucle de Recherche de chemin (tant que l'arrivée n'est pas dans la liste fermée)
	// ***********************************************************************************************************************	
	while(!isset($obj_astar->listeferme[$arriveeX][$arriveeY]))
	{ 
	  	// On determine la case avec le F le plus petit (Algo_astar.php)
		$obj_astar->plusPetitF();
		
		// On lance l'analyse des cases environnantes de la case en cours de progression (Algo_astar.php)
	  	$obj_astar->neufCase($arriveeX, $arriveeY, $obj_renduvisuel, $time_start);
	  	
		// On retire la case en cours de la liste ouverte et on l'inclut dans la liste fermée (Algo_astar.php)
		$obj_astar->listeferme(); 
	}

	// *********************************************************************************************************************
	// Le chemin  a été trouvé (ou non). Si oui On procede à la mise en evidence du chemin le plus court (dans la plupart des cas, imprecision parfois à cause du calcul simple du Manhattan)
	// Boucle pour remonter le chemin depuis l'arrivée jusqu'au depart en interrogeant la case pour connaitre son parent
	// *********************************************************************************************************************
	$cheminX = $arriveeX;
	$cheminY = $arriveeY;

	Do
	{
		// On recupere les coordonées de la case Parent
		$memX = $obj_astar->listeparent[$cheminX][$cheminY][0];
		$memY = $obj_astar->listeparent[$cheminX][$cheminY][1];
		
		// Reinjection des coordonnées dans la nouvelle case Chemin
		$cheminX = $memX; $cheminY = $memY;
		
		// On "allume" la case du bon chemin
		$obj_renduvisuel->chemin[$cheminX][$cheminY] = 1;	
	}
		While (isset($obj_astar->listeparent[$cheminX][$cheminY][0]) && isset($obj_astar->listeparent[$cheminX][$cheminY][1]));

	
	// Une fois que le tracé précedent entre 2 point est fini on mémorise le tableau de points déjà traités pour le tracé
	$memtab = $obj_astar->listeferme; // (optionnel si on autorise le croisement)optionnel si on autorise le croisement)
	// On detruit toutes les variables en cours en détruisant l'objet de la classe Astar pour en refaire un "neuf"
	unset($obj_astar);
	$obj_astar = new Astar;
	// On indique à la liste fermée tous les point déjà traités précedement (cela empeche tout croisement, optionnel si on autorise le croisement)optionnel si on autorise le croisement)
	$obj_astar->listeferme = $memtab;
	
	// Nouveau point de départ (point d'arrivée précedent)
	$departX = $tabarriveeX[$i];
	$departY = $tabarriveeY[$i];
	unset($obj_renduvisuel->mur[$departX][$departY]); // On detruit le mur de la case de depart (au cas ou)	
}	


// Temps de resolution (hors affichage) et information sur la grille
$time_end = microtime(true);
$time = $time_end - $time_start;
$T = number_format ($time, 4);
echo "Nombre de points pour le tracé : $nbpoint<br>";
echo "Taille de la Grille $obj_renduvisuel->grilleaccessiblex * $obj_renduvisuel->grilleaccessibley (";
echo $obj_renduvisuel->grilleaccessiblex * $obj_renduvisuel->grilleaccessibley." cases)";
echo "<br>Resolution en <b>$T</b> seconde";


// *********************************************************************************************************************
// Rendu graphique de la Grille et du chemin (si il y a un chemin)
// *********************************************************************************************************************
$obj_renduvisuel->grille($departX, $departY, $arriveeX, $arriveeY, $obj_astar);

?>