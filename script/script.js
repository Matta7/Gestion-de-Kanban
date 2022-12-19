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
      var cardId = e.dataTransfer.getData('text/plain');
      var cardElement = document.getElementById(cardId);
      targetColumn.appendChild(cardElement);
      console.log(cardElement);
    }
  });
}

//console.log("ui");

//body.onload = dragTasks();