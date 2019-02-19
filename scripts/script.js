/////////////////
//DOM variables//
/////////////////

//main haiku section tab
let haikus = document.getElementById("haikus");
//editor tab to add/create new Haiku
let editor = document.getElementById("editor");
//deleted section tab
let deleted = document.getElementById("deleted");
//button to get to editor
let addButton = document.getElementsByClassName("add");
//button to get to deleted tab
let showDeleted = document.getElementsByClassName("show-deleted");
//button to get to main haikus tab
let showHaikus = document.getElementsByClassName("show-haikus");
//editor form used to create/post new Haikus
let editorForm = document.getElementById("editor-form");
//textarea where the Haiku poem is
let poemTextarea = document.getElementById("poem-textarea");
//content area of the main haiku tab (haikus will be inserted here)
let haikusContent = document.getElementById("haikus-content");
//content area of the deleted haiku tab (haikus will be inserted here)
let deletedContent = document.getElementById("deleted-content");

//normal haiku template used for diplaying single haikus
const haikuTemplate = document.getElementById("haiku-template");
//template used to display single deleted haikus
const deletedTemplate = document.getElementById("deleted-template");
//row template used for diplays deleted and normal haikus 3 in a row
const rowTemplate = document.getElementById("row-template");

////////////////////
//Helper Functions//
////////////////////

//AJAX get request helper
function getAjax(url, callback) {
	let xhr = new XMLHttpRequest();
	xhr.open("GET", url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState > 3 && xhr.status == 200){
			return callback(xhr.response);
		}
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.send();
}

//AJAX post request helper
function postAjax(url, data, callback) {
	let xhr = new XMLHttpRequest()
	xhr.open('POST', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState > 3 && xhr.status == 200){ 
			callback(xhr.response); 
		} else if (){
			
		}
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(data);
	return xhr;
}

//Convert data object into html with a template
function toTemplate(htmlTemplate, dataObject){
	htmlTemplate = htmlTemplate.innerHTML
	Object.keys(dataObject).forEach(function(dataItem){
		itemRegExp = new RegExp("{{\\s*" + dataItem + "\\s*}}", "igm");
		htmlTemplate = htmlTemplate.replace(itemRegExp, dataObject[dataItem]);
	});
	return htmlTemplate;
}

//get all normal Haikus
function getHaikus(){
	getAjax("api/get.php/?haikus=all", function(response){
		response = JSON.parse(response);
		let result = "";
		let row = "";
		for(let i = 0, length = response.length; i < response.length; i++){
			row += toTemplate(haikuTemplate, {
				poem: response[i][0],
				dateCreated: response[i][1]
			});
			if((i+1) % 3 === 0){
				result += toTemplate(rowTemplate, {
					haiku: row
				});
				row = "";
			}
		}
		if(row !== ""){
			result += toTemplate(rowTemplate, {
				haiku: row
			}); 
		}
		if(haikusContent.innerHTML !== result){
			haikusContent.innerHTML = result;
		}
	});
}


//get all deleted Haikus
function getDeleted(){
	getAjax("api/get.php/?haikus=deleted", function(response){
		response = JSON.parse(response);
		let result = "";
		let row = "";
		for(let i = 0, length = response.length; i < response.length; i++){
			row += toTemplate(deletedTemplate, {
				poem: response[i][0],
				daysRemaining: response[i][1]
			});
			if((i+1) % 3 === 0){
				result += toTemplate(rowTemplate, {
					haiku: row
				});
				row = "";
			}
		}
		if(row !== ""){
			result += toTemplate(rowTemplate, {
				haiku: row
			}); 
		}
		if(deletedContent.innerHTML !== result){
			deletedContent.innerHTML = result;
		}
	});
}

///////////////////
//Event Listeners//
///////////////////

//show editor
Array.from(addButton).forEach(function(addButton){
	addButton.addEventListener("click", function(event){
		event.preventDefault();
		editor.classList.remove("w3-hide");
		editor.classList.add("w3-animate-bottom");
		haikus.classList.add("w3-hide");
		deleted.classList.add("w3-hide");
	});
});

//show main haiku tab
Array.from(showHaikus).forEach(function(showHaikus){
	showHaikus.addEventListener("click", function(event){
		event.preventDefault();
		getHaikus();
		haikus.classList.remove("w3-hide");
		haikus.classList.add("w3-animate-bottom");
		editor.classList.add("w3-hide");
		deleted.classList.add("w3-hide");
	});
});

//show deleted haikus tab
Array.from(showDeleted).forEach(function(showDeleted){
	showDeleted.addEventListener("click", function(event){
		event.preventDefault();
		getDeleted();
		deleted.classList.remove("w3-hide");
		deleted.classList.add("w3-animate-bottom");
		haikus.classList.add("w3-hide");
		editor.classList.add("w3-hide");
	});
});

//load haikus and deleted haikus
window.addEventListener("load", function(){
	getHaikus();
	setInterval(getHaikus, 2000);
	setInterval(getDeleted, 8000);
});

//post/submit new Haiku
editorForm.addEventListener("submit", function(event){
	event.preventDefault();
	let poemText = "poem=" + encodeURIComponent(poemTextarea.value);
	postAjax("api/post.php", poemText , function(response){
		if(response === "success"){
			location.href = "index.php";
		} else {
			alert("This haiku exists already in the collection");
		}
	});
});

//add report vote to Haiku
haikusContent.addEventListener("click", function(event){
	if(event.target.classList.contains("report-button")){
		event.preventDefault();
		if(confirm("Are you sure you want to report this Haiku? It will be deleted if other users also report it.")){
			let poem = "poem=";
			poem += encodeURIComponent(event.target.parentElement.previousElementSibling.innerHTML);
			postAjax("api/delete.php", poem, function(response){
				if(response === "true"){
					getHaikus();
				} else {
					alert("You have already reported this Haiku. It will be deleted if other users also report it.")
				}
			});
		}
	}
});

// add report/delete vote or restore vote to a haiku
deletedContent.addEventListener("click", function(event){
	if(event.target.classList.contains("yes-no-button")){
		event.preventDefault();
		let poem = "poem=";
		poem += encodeURIComponent(event.target.parentElement.previousElementSibling.innerHTML);
		poem += "&value=" + event.target.dataset.value;
		postAjax("api/put.php", poem, function(response){
			if(response === "true"){
					getDeleted();
					alert("Your vote has been counted.");
				} else {
					alert("You have already voted for this Haiku. It will be restored or deleted if other users also vote for it.");
				}
		});
	}
});