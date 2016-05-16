/*
* Author: Xiaoying Yu
* Date:03/2016
*Input Val: $selWord1: a key word that selected in drop down menu in front end.
*			$selWord: an array of clicked words in graph in front end
*Output:the word json
*How to: According to the input array and key word, retrieve all the passwords that include each
*		element in the array.
* Data Structure: Associate array, because the large searched file size and speed.
*/
<?php

$selWord1=$_POST['selectedWord'];
$selWord=$_POST['selectedAry'];
if(in_array($selWord1,$selWord)){
}else{ 
	array_push($selWord, $selWord1);
}

ini_set('memory_limit','1000M');
//$selWord=["abandon","as"];
$words=file("ToPasswordsDP5000pac");
$wordNum=count($words);
$wordsHash=array();
$j=0;
$selWordNum=count($selWord);

for($i=0; $i<$wordNum; $i++){
	$inAword=true;
	$eachWord=ltrim($words[$i]);
	$eachWord=rtrim($eachWord);
	for($k=0; $k<$selWordNum; $k++){
		if(strpos($eachWord, $selWord[$k])===false){
			$inAword=false;
			break;
		}
	}
	if($inAword==true){
		$wordsHash[$j]=$eachWord;	
		$j=$j+1;
	}	
}
asort($wordsHash);
echo json_encode(array_values($wordsHash));
?>
