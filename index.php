<!DOCTYPE html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Haiku Collector</title>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> 
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<!-- All Haikus -->
	<div id="haikus">
		<nav class="w3-center">
			<a class="w3-button w3-black w3-center add" href="#">+ Add Haiku</a>
			<a class="w3-button w3-red w3-center show-deleted" href="#">Deleted Haikus</a>
		</nav>
		<h1 class="w3-center">Haiku Collector</h1>
		<div class="w3-container w3-margin-top" id="haikus-content">
			<h3 class="w3-center">Loading...</h3>
		</div>
	</div>

	<!-- Deleted Haikus -->
	<div class="w3-hide" id="deleted">
		<nav class="w3-center">
			<a class="w3-button w3-black w3-center add" href="#">+ Add Haiku</a>
			<a class="w3-button w3-red w3-center show-haikus" href="#">All Haikus</a>
		</nav>
		<h1 class="w3-center">Reported Haikus</h1>
		<div class="w3-container w3-margin-top" id="deleted-content">
			<h3 class="w3-center">Loading...</h3>
		</div>
	</div>

	<!-- Editor / Add Haiku -->
	<div class="w3-mobile w3-hide" id="editor">
		<nav class="w3-center">
			<a class="w3-button w3-black w3-center show-haikus" href="#">All Haikus</a>
			<a class="w3-button w3-red w3-center show-deleted" href="#">Deleted Haikus</a>
		</nav>
		<h1 class="w3-center">Add Haiku</h1>
		<div class="w3-container w3-mobile w3-margin-top">
			<div class="w3-container w3-mobile w3-center w3-card-2">
				<form id="editor-form">
					<textarea name="poem" rows="3" maxlength="120" class="w3-center" placeholder="Remember, be nice!" id="poem-textarea"></textarea>
					<input type="submit" value="Send" name="submit" class="w3-button w3-gray w3-section">
				</form>
			</div>
		</div>
	</div>

	<!-- Templates -->
	<script type="text/x-template" id="haiku-template">
		<div class="cols w3-card-2">
			<p class="poem-text">{{poem}}</p>
			<p class="w3-tiny w3-margin-top">
				Date Created: {{dateCreated}}<br>
				<a href="#" class="report-button">Report</a>
			</p>
		</div>
	</script>
	<script type="text/x-template" id="deleted-template">
		<div class="cols w3-card-2">
			<p class="poem-text">{{poem}}</p>
			<p class="w3-tiny w3-margin-top">
				Days remaining: {{daysRemaining}}<br>
				Restore this Haiku?
				<a href="#" class="yes-no-button" data-value="1">Yes</a>
				<a href="#" class="yes-no-button" data-value="-1">No</a>
			</p>
		</div>
	</script>
	<script type="text/x-template" id="row-template">
		<div class="row w3-center">{{haiku}}</div>
	</script>

	<script src="scripts/script.js"></script>
</body>
</html>