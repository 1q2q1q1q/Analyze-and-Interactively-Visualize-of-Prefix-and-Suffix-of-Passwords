# Analyze-and-Interactively-Visualize-of-Prefix-and-Suffix-of-Passwords
The Interactive Visualization to Analyze and Visualize the prefix and the suffix of passwords

This sample code is one part of my project.
1. view.html is to Prefix Postfix Passwords Graph(P3G)

2. In this view, the drop down list reads the words from dpDrop.csv

3. Users select one of the words in drop down list, and then it will call GetJsonFile1.php to retrieve all the 
	connecting prefix and postfix json data.
	
4. After view.html read the json data, a Forced Direct Graph is displayed. 

5. When user select on a specific node, view.html will call GetTipWords.php to get all the passwords that include this 
	selected node and key node. 

6. Sliding bar is designed in this visualization too, because it can filter the node link frequency to minimize the 
	the graph scale to ensure the convinient investigation.