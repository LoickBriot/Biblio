<!DOCTYPE html>
<html style="height:100%;">
	<head>
		<meta charset="UTF-8">
		<script src="../../web/jquery-1.7.2.min.js">  </script>
		<script src="../../lib/WebViewer.min.js"> </script>
	
		<script>
			$(function() {
				var viewerElement = document.getElementById('viewer');
				var myWebViewer = new PDFTron.WebViewer({
						path: "../../lib",
						initialDoc: "http://localhost/WebSearchViewer/src/viewer/accueil.pdf",
						showToolbarControl: false,
						enableReadOnlyMode: true
					}, viewerElement);	
							
				$('#mainRunSearchButton').on('click', function() {
					window.location.replace('search.php');
				});
									
			});
		</script>
	</head>

	<body style="width:70%;height:85%;margin:auto;padding:0px;overflow:hidden">              
		<div>
			<form method="post" action="search.php">
				Texte recherché : 
				<input type="text" name="fullSearch" id="#searchText" />
				<input id="mainRunSearchButton" type="submit" value="Lancer la recherche"/>
			</form>
		</div>
		
		<div id="viewer" style="height: 100%; overflow: hidden;"/>
	</body>
</html>