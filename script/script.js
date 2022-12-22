
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
request.onreadystatechange = function(){
  if(addtaskXHR.readyState == 4)
  if(addtaskXHR.status == 200){
    // process here
  } else {
    alert("Erreur : "+request.statusText);
  }
}

function addTask(idCol) {
  var idHTML = "colonne-" + idCol;
  var descTache = prompt("Description de la tâche :");
  var taskElement = document.createElement("div");

  taskElement.classList.add("tache");
  taskElement.setAttribute('draggable', true);
  taskElement.innerText = descTache;
  document.getElementById(idHTML).appendChild(taskElement);

  //Sending info to server
  addtaskXHR.open("POST", "index.php?function=addTask", true);
  addtaskXHR.setRequestHeader("Content-Type", "text/plain");
  var req = "col=" + idCol +"\n";
  req += "taskName="+descTache+"\n";
  addtaskXHR.send(req);
}

function dragTasks() {
  var kanban = document.querySelector('.kanban');
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
  });
}