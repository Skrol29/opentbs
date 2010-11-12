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

include('tbs_class_php5.php');

$z = 'haha<w:r><w:rPr><w:b/></w:rPr><w:t> I am [ </w:t></w:r><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:b/></w:rPr><w:t> b.nom;block=table] ok. </w:t></w:r>hihi';
f_CleanProof($z);
echo $z;

function f_CleanProof($Txt) {
/*                                         xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    <w:r><w:rPr><w:b/></w:rPr><w:t> I am [ </w:t></w:r><w:proofErr w:type="spellStart"/><w:r><w:rPr><w:b/></w:rPr><w:t> b.nom;block=table] ok. </w:t></w:r>
0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
          1         2         3         4         5         6         7         8         9         10        11        12        13        14        15  
    |                              |       |           |                               | 
    $pi                            $ptb    $pte        $p                              $pe
*/													
	
	$proof_tag = '<w:proofErr ';
	$proof_len = strlen($proof_tag);
	
	$p = 0;
	while ($p!==false) {

		$p = strpos($Txt, $proof_tag, $p);
		
		if ($p!==false) {
		
			$ok = false;
		
			// get the end of the tag
			$pe = strpos($Txt, '>', $p + $proof_len );
			if ($pe===false) return false;
			
			// read closing tags backward ; only those who are very next each other ; the llop stop when it reaches the humain text contents
			$tag1 = false;
			$x = array('pos'=>$p);
			do {
				$pte = $x['pos'];
				$TxtCut = substr($Txt, 0, $pte);
				$x = f_CleanProof_VeryPreviousTag($TxtCut, $pte);
				if ( ($x!==false) && ($tag1===false) ) $tag1 = $x['tag'];
			} while ($x!==false);

			// get the last first tag backward, in order to find the end of the text part
			if ($tag1!==false) {
				$loc = clsTinyButStrong::f_Xml_FindTag($TxtCut,$tag1,true,$pte-1,false,false,false);
				if ($loc!==false) {
					$pi = $loc->PosBeg;
					$ptb = $pi;
					do {
						$x = strpos($TxtCut, '>', $ptb);
						if ($x!==false) {
							$x++; 
							if ( ($x<$pte) && ($Txt[$x]==='<') ) $ptb = $x;
						}
					} while ($ptb===$x);
					$len = ($ptb - $pi);
					if ( ($len>0) && (substr($Txt,$pi,$len)===substr($Txt,$pe+1,$len)) ) {
						// if the layout has been repeated after the proof element, then we can deleted the block (closing layout + proof + layout)
						$Txt = substr_replace($Txt, '', $pte, $pe - $pte + 1 + $len);
					}
				}
			}
			
			if ($ok) {
				$p = $pte;
			} else {
				// delete only the proof element
				$Txt = substr_replace($Txt, '', $p, $proof_len);
			}
			
		}

	} 
	

}

function f_debug($nom, $txt,$pos,$len=10) {
	echo 'ok '.$nom.'='.$pos.' : {'.substr($txt,$pos,$len).'}<br>'."\r\n"; 
}

function f_CleanProof_VeryPreviousTag($TxtCut, $pte) {
// return the name and the position of the very previous tag, but only if it's a closing tag. Return false otherwise.
	$pp = strrpos($TxtCut, '<');
	if ( ($pp!==false) && ($TxtCut[$pp+1]==='/') ) {
		$tag = substr($TxtCut, $pp+2, $pte - $pp - 3);
		$i = strpos($tag, ' '); // take og attributes, even if it should be impossible that a clisong tag has attributes
		if ($i!==false) $tag = substr($tag, $i);
		return array('tag'=>$tag, 'pos'=>$pp);
	} else {
		return false;
	}
	
}

