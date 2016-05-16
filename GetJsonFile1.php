/*
* Author: Xiaoying Yu
* Date:03/2016
*Input Val: $selValAry: an array of values was selected in front-end
*Output:phpOut.json file
*How to: According to the input array, retrieve the connect information of each
*		element in the array.
* Data Structure: Associate array, because the large searched file size and speed.
*/
<?php
$selValAry=$_POST['selectedVal'];
//$selValAry=["america"];
ini_set('memory_limit', '2000M');
//$nodesLines=file("nodes1.csv");

//$linksLines=file("links1.csv");

//$indexLines=file("nodesIndex1.csv");

$nodesLines=file("dp5000pacNodes.csv");

$linksLines=file("dp5000pacLinks.csv");

$indexLines=file("dp5000pacNodesIndex.csv");
//store the input node information as  the nodes map

$NodesNum=count($nodesLines);

$NodeHash=array();

for($i=0; $i<$NodesNum; $i++)

{
	$eachNodeAry=explode("\"",$nodesLines[$i]);

	$key=substr($eachNodeAry[0], 0, strlen($eachNodeAry[0])-1);

	$group=substr($eachNodeAry[2], 1);

	$value=$eachNodeAry[1].$group;

	$NodeHash[$key]=$value;

}

//store the input link information into the links map

$LinksNum=count($linksLines);

$LinkHash=array();

for($i=0; $i<$LinksNum; $i++)

{
	$eachLinkAry=explode("\"",$linksLines[$i]);

	$key=substr($eachLinkAry[0], 0, strlen($eachLinkAry[0])-1);

	$value=$eachLinkAry[1];

	$LinkHash[$key]=$value;

}
//store the index of nodes into the nodesIndex map

//echo "link map  memory used: "+ memory_get_usage();
$IndexNum=count($indexLines);

$IndexHash=array();

for($i=0; $i<$IndexNum; $i++)
{        
	$eachIndexAry=explode("\"",$indexLines[$i]);

	$key=substr($eachIndexAry[0], 0, strlen($eachIndexAry[0])-1);

	$value=$eachIndexAry[1];

	$IndexHash[$key]=$value;

}

$MainConAry=array();//this array is for storing the each node json object.
$nodesAry=array();//store all nodes in visualization view
$SelNodesIndAry=array();//SelNodesIndAry is a map for storing old node index
//make the selected node and connected nodes json file
for($j=0; $j<count($selValAry); $j++){

	$selVal=$selValAry[$j];
	$selConVal=$NodeHash[$selVal];

	$group=substr($selConVal, strlen($selConVal)-2, 1);

	$ConStr=substr($selConVal, 0, strlen($selConVal)-3);

	$conAry=explode("'", $ConStr);

	$conLen=count($conAry);

	if(!(array_key_exists($IndexHash[$selVal], $SelNodesIndAry))){
		$SelNodesIndAry[$IndexHash[$selVal]]=$selVal;
		array_push($nodesAry, $selVal);
	}

	for($i=0; $i<$conLen; $i++){
		if($i%2 != 0 and !(array_key_exists($IndexHash[$conAry[$i]], $SelNodesIndAry))){
			array_push($nodesAry, $conAry[$i]);
			if (array_key_exists($conAry[$i], $IndexHash)){
				$SelNodesIndAry[$IndexHash[$conAry[$i]]]=$conAry[$i];
			}
		}
	}
}

$NewNodesHash=array();//NewNodesHash is for storing the new nodes and new index
$NodesLength=count($nodesAry);
for($i=0; $i<$NodesLength; $i++){
	$NewNodesHash[$nodesAry[$i]]=$i;
}

foreach($SelNodesIndAry as $value){
	array_push($MainConAry, makeNodeObject($NodeHash, $value, $NewNodesHash)); 
} 
function makeNodeObject($NodeHash, $eachNodeName, $NewNodesHash){
	
//make the small json file
$selConVal1=$NodeHash[$eachNodeName];
$group1=substr($selConVal1, strlen($selConVal1)-2, 1);
$ConStr1=substr($selConVal1, 0, strlen($selConVal1)-3);
$conAry1=explode("'", $ConStr1);
$conLen1=count($conAry1);
$conElementsAry1=array();
for($i=0; $i<$conLen1; $i++){
	if($i%2 != 0){
		array_push($conElementsAry1, $conAry1[$i]);
	}
}

$TempAry=array("name"=> $eachNodeName, "group"=> intval($group1), "cc"=>$conElementsAry1, "index1"=>$NewNodesHash[$eachNodeName]);
return json_encode($TempAry);
}

$MainLinkAry=array();
foreach($SelNodesIndAry as $key => $value){
	if (array_key_exists($key, $LinkHash)){
	
	$linkVal=$LinkHash[$key];
	$linkAry=preg_split("/[\]\s,\[]+/", $linkVal);
	$linkNo=count($linkAry);
	unset($linkAry[0]);
	unset($linkAry[$linkNo-1]);
	$linkAry=array_values($linkAry);
	$node1=$value;
	$oneInd=$NewNodesHash[$node1];
	for($i=0; $i<$linkNo-2; $i=$i+2){
		if(array_key_exists($linkAry[$i],$SelNodesIndAry)){
			$node2=$SelNodesIndAry[$linkAry[$i]];
			$index2=$NewNodesHash[$node2];
			$eachLinkObject=makeLinkObject($oneInd, $index2, $linkAry[$i+1]);
			array_push($MainLinkAry, $eachLinkObject);
		}
	}
	}
}

echo "read link infomation successfully";
function makeLinkObject($selIndex, $a, $b){
	$tempLinkAry=array("source"=>intval($selIndex), "target"=>intval($a), "value"=>intval($b));
	return json_encode($tempLinkAry);
}
$MainAry=array("nodes"=>$MainConAry, "links"=>$MainLinkAry);
$file=fopen("phpOut.json","w");
fwrite($file, json_encode($MainAry));
fclose($file);
echo "write to the file successfully\n";
echo "total memory used: "+ memory_get_usage();
?>
