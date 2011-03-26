<?php

/* Fix ODF files of the OpenTBS package.
2011-03-26: un internaute me fait ermarque que le fichier modèle ODT n'est pas valide selon les outils ODF validators
  http://tools.services.openoffice.org/odfvalidator/
 Cela vient du fait que certaines version d'OpenOffice ne respecte les standard strcits d'ODf et aussi est buggé par d'autre moments.
 Ce script corrige les problèmes connus afin que els fichiers passent positivement le test du validateur.
 */

if (file_exists('tbszip.php')) {
	include_once('tbszip.php');
} else {
	include_once('../tbs_plugin_opentbs.php');
}

$file_lst = array(
  'demo_oo_text.odt'
, 'demo_oo_formula.odf'
, 'demo_oo_graph.odg'
, 'demo_oo_master.odm'
, 'demo_oo_presentation.odp'
, 'demo_oo_spreadsheet.ods'
);


$version = '1.2'; // valeur par défaut à mettre dans le manifest
$manifest = 'META-INF/manifest.xml';
$styles = 'styles.xml';

$odf = new clsTbsZip();

foreach ($file_lst as $file) {

	$odf->Open($file);
	echo "<h4>Étude du fichier '$file'.</h4>\r\n";

	$extension = strtolower(substr($file, -4));
	
	if (!$odf->FileExists($manifest)) {
		echo "Le fichier manifest n'a pas été trouvé.<br>\r\n";
		continue;
	}

	/* debug
	if (file_exists('manifest.xml')) {
		$odf->FileReplace($manifest, 'manifest.xml', TBSZIP_FILE);
		$temp = $file.'.tmp.odt';
		$odf->Flush(TBSZIP_FILE, $temp);
		echo "Fichier fanifest remplacé par le fichier 'manifest.xml' trouvé dans le répertoire.<br>\r\n";
		exit;
	}
	*/

	$txt = $odf->FileRead($manifest);

	// des fois la version figure dans un élément <manifest:file-entry>, lé cas échéant, on va chercher la bonne version du manifeste.
	$att = ' manifest:version="';
	$p = strpos($txt, $att);
	if ($p!==false) {
		$p = $p + strlen($att);
		$p2 = strpos($txt, '"', $p);
		$version = substr($txt, $p, $p2 - $p);
		echo "La version du manifest a été trouvée : ".$version."<br>\r\n";
	} else {
		echo "La version du manifest est absente.<br>\r\n";
	}

	$replace_manifest = 0;

	if (($extension=='.zzz')){
		echo "Pas de retouche de la version du manifest car c'est un fichier ODG.<br>\r\n"; // osbolète
	} else {
		// recherche de la version dans l'élément <manifest:manifest>
		$p = strpos($txt, '<manifest:manifest ');
		if ($p!==false) {
			$p2 = strpos($txt, '>', $p);
			if ($p2!==false) {
				$elem = substr($txt, $p, $p2 - $p + 1);
				if (strpos($elem, $att)===false) {
					// on ajoute la version du manifest
					$txt = substr_replace($txt, $att.$version.'"', $p2, 0);
					$replace_manifest++;
					echo "Le version du manifest a été ajouté à l'élément <b>manifest:manifest</b> avec la valeur ".$version."<br>\r\n";
				} else {
					echo "Le version du manifest est déjà présente dans l'élément <i>manifest:manifest</i><br>\r\n";
				}
			}
		}
	}
	
	// recherche ds déclaration de répertoire : il faut les retirer
	$elem = '<manifest:file-entry manifest:media-type="" manifest:full-path="';
	$p = 0;
	while (($p=strpos($txt, $elem, $p))!==false) {
		$pe = $p + strlen($elem);
		$pc = strpos($txt, '"', $pe);
		if (substr($txt, $pc-1, 1)==='/') {
			// c'est un répertoire vide, il faut le supprimer
			$pe = strpos($txt, '>', $pc);
			if (substr($txt, $p-1,1)===' ') $p--;
			if (substr($txt, $p-1,1)==="\n") $p--;
			// $x = substr($txt, $p, $pe - $p +1);
			$txt = substr_replace($txt, '', $p, $pe - $p +1);
			$replace_manifest++;
		} else {
			// item suivant
			$p = $pc;
		}
	}

	if ($replace_manifest>0) {
		$odf->FileReplace($manifest, $txt, TBSZIP_STRING); // si on ne compresse pas ça fait un bug !! à vérifier pourquoi
		echo "<span style='color:red;'>$replace_manifest modifications ont été apportées au fichier manifest</span><br>\r\n";
	}

	// recherche du bug dans le fichier style
	$replace_style = 0;
	if ($odf->FileExists($styles)) {
		$txt = $odf->FileRead($styles);
		$item = ' fo:font-size="0pt"';
		if (strpos($txt, $item)!==false) {
			$item2 = str_replace('"0pt"', '"14pt"', $item);
			$txt = str_replace($item, $item2, $txt);
			$odf->FileReplace($styles, $txt, TBSZIP_STRING); // si on ne compresse pas ça fait un bug !! à vérifier pourquoi
			echo "<span style='color:red;'>une modification a été apportée au fichier styles</span><br>\r\n";
			$replace_style++;
		}
	}

	if (($replace_manifest+$replace_style)>0) {
		$temp = $file.'.tmp';
		$odf->Flush(TBSZIP_FILE, $temp);
		$odf->OutputClose(); // à ajouter car bug si TbsZip <= 2.3
		$odf->Close();
		unlink($file);
		rename($temp, $file);
	} else {
		echo "<span style='color:green;'>L'archive n'a pas été modifiée.</span><br>\r\n";
	}
	
}	
	
//echo $txt;
