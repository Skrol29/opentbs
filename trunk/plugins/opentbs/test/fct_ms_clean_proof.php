<?php

/* this function is a work for trying to clean Docx contents from Proof tags.
The problem is that the proff feature of Ms Word makes the XML cnotents to be split by <w:proofErr> elements.
When such an element is added inside a texte content, it also split and duplicate the layout flow. 

Example:

<w:r><w:rPr><w:b/></w:rPr><w:t>
  I am [b.nom;block=table] ok.
</w:t></w:r>

will become:

<w:r><w:rPr><w:b/></w:rPr><w:t>
  I am [
</w:t></w:r><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:b/></w:rPr><w:t>
  b.nom;block=table] ok.
</w:t></w:r>

Proof tags can be (not exaustive):

<w:proofErr w:type="spellStart"/>
<w:proofErr w:type="spellEnd"/>
<w:proofErr w:type="gramStart"/>
<w:proofErr w:type="spellStart"/>

*/

if (!isset($TBS)) {

	include_once('tbs_class_php5.php');

	/*
	$z = 'haha<w:r><w:rPr><w:b/></w:rPr><w:t> I am [ </w:t></w:r><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:b/></w:rPr><w:t> b.nom;block=table] ok. </w:t></w:r>hihi';
	f_CleanProof($z);
	echo $z;
	exit;
	*/

	$z = '<w:r><w:t xml:space="preserve">Ok, les </w:t></w:r><w:r w:rsidR="005B104D"><w:t>(je corrige ici</w:t></w:r><w:r w:rsidR="005B104D"><w:t xml:space="preserve">) </w:t></w:r>';
	f_CleanRsID($z);
	echo $z;

}

function f_CleanDuplicatedLayout(&$Txt) {
	
	$wro = '<w:r';
	$wro_len = strlen($wro);
	
	$wrc = '</w:r';
	$wrc_len = strlen($wrc);

	$wto = '<w:t';
	$wto_len = strlen($wto);

	$wtc = '</w:t';
	$wtc_len = strlen($wtc);
	
	$nbr = 0;
	$wro_p = 0;
	while ( ($wro_p=f_FoundTag($Txt, $wro, $wro_p))!==false ) {
		$wto_p = f_FoundTag($Txt,$wto,$wro_p); if ($wto_p===false) return false; // error in the structure of the <w:r> element
		$first = true;
		do {
			$ok = false;
			$wtc_p = f_FoundTag($Txt,$wtc,$wto_p); if ($wtc_p===false) return false; // error in the structure of the <w:r> element
			$wrc_p = f_FoundTag($Txt,$wrc,$wro_p); if ($wrc_p===false) return false; // error in the structure of the <w:r> element
			if ( ($wto_p<$wrc_p) && ($wtc_p<$wrc_p) ) { // if the found <w:t> is actually included in the <w:r> element
				if ($first) {
					$superflous = '</w:t></w:r>'.substr($Txt, $wro_p, ($wto_p+$wto_len)-$wro_p); // should be like: '</w:t></w:r><w:r>....<w:t'
					$superflous_len = strlen($superflous);
					$first = false;
				}
				$x = substr($Txt, $wtc_p+$superflous_len,1);
				if ( (substr($Txt, $wtc_p, $superflous_len)===$superflous) && (($x===' ') || ($x==='>')) ) {
					// if the <w:r> layout is the same same the next <w:r>, then we join it
					$p_end = strpos($Txt, '>', $wtc_p+$superflous_len); //
					if ($p_end===false) return false; // error in the structure of the <w:t> tag
					$Txt = substr_replace($Txt, '', $wtc_p, $p_end-$wtc_p+1);
					$nbr++;
					$ok = true;
				}
			}
		} while ($ok);
		
		$wro_p = $wro_p + $wro_len;
		
	}
	
	return $nbr; // number of replacements
	
}



function f_CleanRsID(&$Txt) {
// delete attributes relative to log of user modifications, because they split the text
/*  <w:r><w:t xml:space="preserve">Ok, les </w:t></w:r><w:r w:rsidR="005B104D"><w:t>(je corrige ici</w:t></w:r><w:r w:rsidR="005B104D"><w:t xml:space="preserve">) </w:t></w:r>
*/
/*
 <w:p>      paragraph
   <w:r>    common layout
     <w:t>  text part
*/

	$rs_lst = array('w:rsidR', 'w:rsidRPr');

	$nbr_del = 0;
	foreach ($rs_lst as $rs) {

		$rs_att = ' '.$rs.'="';
		$rs_len = strlen($rs_att);

		$p = 0;
		while ($p!==false) {
			// search the attribute
			$ok = false;
			$p = strpos($Txt, $rs_att, $p);
			if ($p!==false) {
				// attribute found, now seach tag bounds
				$po = strpos($Txt, '<', $p);
				$pc = strpos($Txt, '>', $p);
				if ( ($pc!==false) && ($po!==false) && ($pc<$po) ) { // means that the attribute is actually inside a tag
					$p2 = strpos($Txt, '"', $p+$rs_len); // position of the delimiter that closes the attribute's value
					if ( ($p2!==false) && ($p2<$pc) ) {
						// delete the attribute
						$Txt = substr_replace($Txt, '', $p, $p2 -$p +1);
						$ok = true;
						$nbr_del++;
					}
				}
				if (!$ok) $p = $p + $rs_len;
			}
		}

	}

	// delete empty tags
	$Txt = str_replace('<w:rPr></w:rPr>', '', $Txt);
	$Txt = str_replace('<w:pPr></w:pPr>', '', $Txt);
	
	return $nbr_del;
	
}

function f_FoundTag($Txt, $Tag, $PosBeg) {
// found the next tag of the asked type.
	$len = strlen($Tag);
	$p = $PosBeg;
	while ($p!==false) {
		$p = strpos($Txt, $Tag, $p);
		if ($p===false) return false;
		$x = substr($Txt, $p+$len, 1);
		if (($x===' ') || ($x==='/') || ($x==='>') ) {
			return $p;
		} else {
			$p = $p+$len;
		}
	}
	return false;
}

function f_CleanTag(&$Txt, $TagLst) {
// delete all tags of the types listed in the list.
	$nbr_del = 0;
	foreach ($TagLst as $tag) {
		$p = 0;
		while (($p=f_FoundTag($Txt, $tag, $p))!==false) {
			// get the end of the tag
			$pe = strpos($Txt, '>', $p);
			if ($pe===false) return false; // arror in the XML formating
			// delete the tag
			$Txt = substr_replace($Txt, '', $p, $pe-$p+1);
		} 
	}
	return $nbr_del;
}

function f_CleanProof(&$Txt) {
	return f_CleanTag($Txt, array('<w:proofErr', '<w:noProof'));
}
function f_CleanMisc(&$Txt) {
	$nbr = f_CleanTag($Txt, array('<w:lang'));
}



