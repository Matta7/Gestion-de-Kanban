
function createXHRObject(){
  if(window.XMLHttpRequest){
    return new XMLHttpRequest();
  }
  if(window.ActiveXObject){
    var names = [
      "Msxml2.XMLHTTP.6.0" ,
      "Msxml2.XMLHTTP.3.0" ,
      "Msxml2.XMLHTTP" ,
      "Microsoft.XMLHTTP"
    ];
    for(var i in names) {
      try {
        return new ActiveXObject(names[i]);
      } catch(e){}
    }
  }
  return null; // not supported
}

var addtaskXHR = createXHRObject();
addtaskXHR.onreadystatechange = function(){
  if(addtaskXHR.readyState == 4)
  if(addtaskXHR.status == 200){
    // process here
    //console.log(addtaskXHR.responseText);
  } else {
    alert("Erreur : "+addtaskXHR.statusText);
  }
}

function addTask(idCol) {
  var idHTML = "colonne-" + idCol;
  var descTache = prompt("Description de la tâche :");
  var taskElement = document.createElement("div");
  var kanban = document.querySelector('.kanban');
  var idKanban = kanban.id.substring(7);

  taskElement.classList.add("tache");
  taskElement.setAttribute('draggable', true);
  taskElement.innerText = descTache; // onclick="window.location.href = 'index.php?action=affectation&id=15&idTache=35'"
  document.getElementById(idHTML).appendChild(taskElement);

  //Sending info to server
  addtaskXHR.open("GET", "index.php?function=addTask&idCol=" + idCol + "&descTache=" + descTache + "&id=" + idKanban, false);
  addtaskXHR.setRequestHeader("Content-Type", "text/plain");
  /*var req = "col=" + idCol +"\n";
  req += "taskName="+descTache+"\n";*/
  addtaskXHR.send();
  // process here
  if(addtaskXHR.status == 200) {
    var response = addtaskXHR.responseText;
    taskElement.id = "tache-" + response;
    taskElement.innerHTML += "<button onclick=\"window.location.href = 'index.php?action=affectation&id=" + idKanban + "&idTache=" + response + "'\">Affecter</button>"


    taskElement.addEventListener('dragstart', function(e) {
      e.dataTransfer.setData("text/plain", this.id);
    });
  }
}

var dragtaskXHR = createXHRObject();
dragtaskXHR.onreadystatechange = function(){
  if(dragtaskXHR.readyState == 4)
  if(dragtaskXHR.status == 200){
    // process here
    //console.log(dragtaskXHR.responseText);
  } else {
    alert("Erreur : "+dragtaskXHR.statusText);
  }
}

function dragTasks() {
  var kanban = document.querySelector('.kanban');
  var idKanban = kanban.id.substring(7);
  var tasks = document.querySelectorAll('.tache');

  // Make the cards draggable
  tasks.forEach(function(tasks) {
      tasks.addEventListener('dragstart', function(e) {
        e.dataTransfer.setData("text/plain", this.id);
    });
  });

  // Allow the columns to accept dropped cards
  kanban.addEventListener('dragover', function(e) {
    e.preventDefault();
  });

  kanban.addEventListener('drop', function(e) {
    e.preventDefault();
    var targetColumn = e.target.closest('.colonne');
    if (targetColumn) {
      var taskId = e.dataTransfer.getData('text/plain');
      var taskElement = document.getElementById(taskId);
      targetColumn.appendChild(taskElement);
    }
    // Sync with server
    dragtaskXHR.open("GET", "index.php?function=dragTasks&idCol=" + targetColumn.id.substring(8) + "&idTache=" + taskElement.id.substring(6) + "&id=" + idKanban, true);
    dragtaskXHR.setRequestHeader("Content-Type", "text/plain");
    /*var req = "col=" + targetColumn.id.substring(8) +"\n";
    req += "taskId="+taskElement.id.substring(6)+"\n";*/
    dragtaskXHR.send();
  });
}