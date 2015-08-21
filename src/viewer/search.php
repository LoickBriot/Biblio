<!DOCTYPE html>
<html style="height:100%;">
	<head>
		<meta charset="UTF-8">
		<script src="../../web/jquery-1.7.2.min.js"></script>
		<script src="../../lib/WebViewer.min.js"></script>
		
		<script language="javascript">
			// récupération de la variable
			var searchedWord = '<?php echo $_POST['fullSearch']; ?>';
			
			//génération du tableau de document et de mots similaires grâce à la variable searchedWord
			var documentList = new Array("document_patriote.pdf",  "test.pdf",  "these.pdf" ,"test2.pdf",  "test3.pdf");
			var wordList = new Array(searchedWord, "vous", "lous", "nous");
			var path = "http://localhost/WebSearchViewer/pdf/";	
			
			var i=0, currentLoad = documentList[i];
			var j=0, currentWord = wordList[j];
			 
			$(function() {
				
				var viewerElement = document.getElementById('viewer');
				var myWebViewer = new PDFTron.WebViewer({
						path: "../../lib",
						initialDoc: path + documentList[i],
						config: "config.js",
						custom: searchedWord,
						showToolbarControl: false,
						enableReadOnlyMode: true
					}, viewerElement);	
				
				$(viewerElement).on("toolModeChanged", function(event) {
					myWebViewer.setToolMode(PDFTron.WebViewer.ToolMode.Pan);
				});
						
				$('#nextDocumentButton').on('click', function() {
					if (++i > documentList.length-1 ) i--;
					else {
						myWebViewer.loadDocument(path + documentList[i]);
						currentLoad = documentList[i];
						SelectMenu.selectedIndex=i;
					}
				});
				
				$('#previousDocumentButton').on('click', function() {
					if (--i < 0) i++;
					else{
						myWebViewer.loadDocument(path + documentList[i]);
						currentLoad = documentList[i];
						SelectMenu.selectedIndex=i;
					}
				});
			
				$('form').on('click', function() {
					if (currentLoad != documentList[i]){
						myWebViewer.loadDocument(path + documentList[i]);
						currentLoad = documentList[i];	
						SelectMenu.selectedIndex=i;
					}
				});
				
				$('wordForm').on('click', function() {
					if (currentWord != wordList[j]){
						SelectWord.selectedIndex=j;
						currentWord = wordList[j];
						window.location.replace('search.php');						
					}
				});
			});
		</script>	
	</head>

	<body style="width:70%;height:85%;margin:auto;padding:0px;overflow:hidden">              	
		<div>
			<form name="form" id="form">
				Choisir un document contenant ce mot dans la liste déroulante:
				<select style="width: 200px" name='SelectMenu' id='SelectMenu' onChange='allerA(this.form)'>
					<script language="javascript">
						for(var k=0; k<documentList.length; k++){
							document.write("<option>" + documentList[k] + "</option>");
						}
					</script>
				</select>
			</form>
			
			<form name="form" id="wordForm" method="post" action="search.php">
				Choisir un mot similaire dans la liste déroulante :
				<select style="width: 200px" name='SelectWord' id='SelectWord' onChange='chooseWord(this.wordForm)'>
					<script language="javascript">
						for(var k=0; k<wordList.length; k++){
							document.write("<option>" + wordList[k] + "</option>");
						}
						document.write("<input type='hidden' name='fullSearch' id='#searchText' value='" + wordList[j] + "'/>" );
					</script>
				<input id="mainRunSearchButton" type="submit" value="Valider"/>
				</select>
			</form>
			
			<a href="index.php"> Revenir à la page d'accueil </a>
			<input id="previousDocumentButton" type="button" value="Précédent document"/>			
			<input id="nextDocumentButton" type="button" value="Prochain document"/>
		</div>	
		
		<script language="javascript">
			function allerA(form) {
				i = SelectMenu.selectedIndex;
			}
			function chooseWord(wordForm) {
				j = SelectWord.selectedIndex;
				document.getElementById('#searchText').value = wordList[j] ;
			 } 
		</script>
		<div id="viewer" style="height: 100%; overflow: hidden;"></div>
	</body>
</html>