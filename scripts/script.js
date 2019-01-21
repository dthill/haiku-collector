let haikus = document.getElementById("haikus");
let editor = document.getElementById("editor");
let deleted = document.getElementById("deleted");
let addButton = document.getElementsByClassName("add");
let showDeleted = document.getElementsByClassName("show-deleted");
let showHaikus = document.getElementsByClassName("show-haikus");
let editorForm = document.getElementById("editor-form");
let poemTextarea = document.getElementById("poem-textarea");
let haikusContent = document.getElementById("haikus-content");
let deletedContent = document.getElementById("deleted-content");

const haikuTemplate = document.getElementById("haiku-template");
const deletedTemplate = document.getElementById("deleted-template");
const rowTemplate = document.getElementById("row-template");

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

function postAjax(url, data, callback) {
	let xhr = new XMLHttpRequest()
	xhr.open('POST', url);
	xhr.onreadystatechange = function() {
		if (xhr.readyState > 3 && xhr.status == 200){ 
			callback(xhr.response); 
		}
	};
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.send(data);
	return xhr;
}

function toTemplate(htmlTemplate, dataObject){
	htmlTemplate = htmlTemplate.innerHTML
	Object.keys(dataObject).forEach(function(dataItem){
		itemRegExp = new RegExp("{{\\s*" + dataItem + "\\s*}}", "igm");
		htmlTemplate = htmlTemplate.replace(itemRegExp, dataObject[dataItem]);
	});
	return htmlTemplate;
}

function getHaikus(){
	getAjax("get.php/?haikus=all", function(response){
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

function getDeleted(){
	getAjax("get.php/?haikus=deleted", function(response){
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

Array.from(addButton).forEach(function(addButton){
	addButton.addEventListener("click", function(event){
		event.preventDefault();
		editor.classList.remove("w3-hide");
		editor.classList.add("w3-animate-bottom");
		haikus.classList.add("w3-hide");
		deleted.classList.add("w3-hide");
	});
});

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

window.addEventListener("load", function(){
	getHaikus();
	setInterval(getHaikus, 2000);
	setInterval(getDeleted, 8000);
});

editorForm.addEventListener("submit", function(event){
	event.preventDefault();
	let poemText = "poem=" + encodeURIComponent(poemTextarea.value);
	postAjax("post.php", poemText , function(response){
		console.log(response);
		if(response === "success"){
			location.href = "index.php";
		} else {
			alert("This haiku exists already in the collection");
		}
	});
});

haikusContent.addEventListener("click", function(event){
	if(event.target.classList.contains("report-button")){
		event.preventDefault();
		if(confirm("Are you sure you want to report this Haiku? It will be deleted if other users also report it.")){
			let poem = "poem=";
			poem += encodeURIComponent(event.target.parentElement.previousElementSibling.innerHTML);
			postAjax("delete.php", poem, function(response){
				if(response === "true"){
					getHaikus();
				} else {
					alert("You have already reported this Haiku. It will be deleted if other users also report it.")
				}
			});
		}
	}
});

deletedContent.addEventListener("click", function(event){
	if(event.target.classList.contains("yes-no-button")){
		event.preventDefault();
		let poem = "poem=";
		poem += encodeURIComponent(event.target.parentElement.previousElementSibling.innerHTML);
		poem += "&value=" + event.target.dataset.value;
		postAjax("put.php", poem, function(response){
			if(response === "true"){
					getDeleted();
					alert("Your vote has been counted.");
				} else {
					alert("You have already voted for this Haiku. It will be restored or deleted if other users also vote for it.");
				}
		});
	}
});