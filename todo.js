document.addEventListener('DOMContentLoaded', function() {

    // Define the URL to our CRUD server api
    const apiUrl = 'todo-api.php';


    const fetchTodos = () => {
        fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const todoList = document.getElementById('todo-list');
            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.title;
                todoList.appendChild(li);
            });
        });
    }

    fetchTodos();
});