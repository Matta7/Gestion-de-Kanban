function addTask(idCol) {
  var idHTML = "colonne-" + idCol;
  var descTache = prompt("Description de la tâche :");
  var taskElement = document.createElement("div");

  taskElement.classList.add("tache");
  taskElement.setAttribute('draggable', true);
  taskElement.innerText = descTache;
  document.getElementById(idHTML).appendChild(taskElement);
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