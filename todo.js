document.addEventListener('DOMContentLoaded', function() {

    // Define the URL to our CRUD server api
    const apiUrl = 'todo-api.php';


    const getDeleteButton = (item) => {
        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Löschen';

        // Handle delete button click
        deleteButton.addEventListener('click', function() {
            fetch(apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: item.id })
            })
            .then(response => response.json())
            .then(() => {
                fetchTodos(); // Reload todo list
            });
        });

        return deleteButton;
    }

    const getCompleteButton = (item) => {
        const completeButton = document.createElement('button');
        completeButton.textContent = item.completed ? "Unerledigt" : "Erledigt";

        completeButton.addEventListener('click', function() {
            fetch(apiUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: item.id,
                    completed: item.completed ? 0 : 1
                })
            })
            .then(response => response.json())
            .then(updatedItem => {
                fetchTodos();
            });
        });

        return completeButton;
    }

    const getUpdateButton = (item) => {

        const updateButton = document.createElement('button');
        updateButton.textContent = 'Aktualisieren';

        // Handle update button click
        updateButton.addEventListener('click', function() {
            document.getElementById('todo-id').value = item.id;
            document.getElementById('todo-update-input').value = item.title;
            document.getElementById('todo-update-form').style.display = 'block';
        });

        return updateButton;
    }

    const fetchTodos = () => {
        fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            const todoList = document.getElementById('todo-list');
            todoList.innerHTML = "";
            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.title;
                li.appendChild(getDeleteButton(item));
                li.appendChild(getCompleteButton(item));
                li.appendChild(getUpdateButton(item));
                if (item.completed) {
                    li.className = 'completed';
                }
                todoList.appendChild(li);
            });
        });
    }

    document.getElementById('todo-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const inputElement = document.getElementById('todo-input');
        const todoInput = inputElement.value;
        inputElement.value = "";

        if(todoInput == "") {
            document.querySelector('input[type="text"]').className = "required";
            document.getElementById('error-message').innerText = `Bitte ausfüllen!`;
            return;
        }

        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ title: todoInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 'success') {
                fetchTodos(); // Reload todo list
            } else {
                document.getElementById('error-message').innerText = `${data.status}: ${data.message}`;
            }
        });
    });

    document.getElementById('todo-update-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const updateId = document.getElementById('todo-id').value;
        const updatedTitle = document.getElementById('todo-update-input').value;

        fetch(apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: updateId, title: updatedTitle })
        })
        .then(response => response.json())
        .then(updatedItem => {
            document.getElementById('todo-update-form').style.display = 'none';
            fetchTodos();
        });
    });

    fetchTodos();
});