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

function f_CleanProof($Txt) {
	
	$proof_tag = '<w:proofErr ';
	$proof_len = strlen($proof_tag);
	$layout_lst = array();
	
	$p = strpos($Txt, $proof_tag);
	if ($p!==false) {
	
		// get the end of the tag
		$pe = strpos($Txt, '>', $p + $proof_len );
		if ($pe===false) return false;

		// read closing tags backward ; only those who are very next each other ; the llop stop when it reaches the humain text contents
		$tag1 = false;
		$x = array('pos'=>$p);
		do {
			$pte = $x['pos'];
			$TxtCut = substr($TxtCut, $pte);
			$x = f_CleanProof_VeryPreviousTag($TxtCut, $pte);
			if ( ($x!==false) && ($tag1===false) ) $tag1 = $x['tag'];
		} while ($x!==false);

		// get the last first tag backward, in order to find the end of the text part
		if ($tag1!==false) {
			$x = $tag;
		}
		
		
		
	}
	
}

function f_CleanProof_VeryPreviousTag($TxtCut, $p0) {
// return the name of the very previous tag, but only if it's a closing tag. Return false otherwise.
	$pp = strrpos($TxtCut, '<');
	if ( ($pp!==false) && ($TxtCut,[$pp+1]==='/') ) {
		$tag = substr($TxtCut,, $pp+2, $p - $pp - 5);
		$i = strpos($tag, ' '); // take og attributes, even if it should be impossible that a clisong tag has attributes
		if ($i!==false) $tag = substr($tag, $i);
		return array('tag'=>$tag, 'pos'=>$pp);
	} else {
		return false;
	}
	
}