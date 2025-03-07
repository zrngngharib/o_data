# o_data Project

## Overview
The o_data project is a task management application built using PHP and MySQL. It allows users to manage tasks efficiently, providing functionalities such as adding, editing, deleting, and viewing tasks based on their status (pending, in progress, completed).

## File Structure
```
o_data
├── includes
│   └── db.php                # Database connection logic
├── tasks
│   ├── add_task.php          # Logic for adding a new task
│   ├── bulk_action.php       # Processes bulk actions on tasks
│   ├── completed_tasks.php    # Displays completed tasks
│   ├── copy_task.php         # Allows copying of existing tasks
│   ├── delete_task.php       # Handles task deletion
│   ├── edit_task.php         # Provides editing functionality for tasks
│   ├── pending_tasks.php      # Displays pending tasks
│   └── report.php            # Generates task-related reports
├── views
│   └── tasks.php             # Main view for displaying tasks
├── styles.css                # CSS styles for layout and design
└── README.md                 # Project documentation
```

## Setup Instructions
1. **Clone the Repository**: Clone this repository to your local machine using `git clone <repository-url>`.
2. **Database Configuration**: Update the `db.php` file in the `includes` directory with your database credentials.
3. **Run the Application**: Use a local server environment (like XAMPP or MAMP) to run the application. Place the project folder in the server's root directory (e.g., `htdocs` for XAMPP).
4. **Access the Application**: Open your web browser and navigate to `http://localhost/o_data/views/tasks.php` to start using the application.

## Usage
- **Adding Tasks**: Click on the "➕ زیاد كردن" button to add a new task.
- **Viewing Tasks**: Navigate through the different task views (pending, completed) using the respective buttons.
- **Editing and Deleting Tasks**: Use the provided options next to each task to edit or delete them.
- **Bulk Actions**: Select multiple tasks and perform bulk actions like delete or complete.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue for any enhancements or bug fixes.

## License
This project is open-source and available under the MIT License.