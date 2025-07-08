<?php

return [
    // Roles
    'roles' => [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'member' => 'Member',
    ],

    // Task statuses
    'task_statuses' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'canceled' => 'Canceled',
    ],

    // Task priorities
    'task_priorities' => [
        'high' => 'High',
        'medium' => 'Medium',
        'low' => 'Low',
    ],

    // Validation messages
    'validation_messages' => [
        'task' => [
            'title_required' => 'Task title is required',
            'title_max' => 'Task title must not exceed 255 characters',
            'status_required' => 'Task status is required',
            'status_invalid' => 'Invalid task status',
            'priority_required' => 'Task priority is required',
            'priority_invalid' => 'Invalid task priority',
            'due_date_date' => 'Due date must be a valid date',
            'assigned_user_required' => 'Task assignee is required',
            'assigned_user_exists' => 'Selected user does not exist',
            'team_required' => 'Team is required',
            'team_exists' => 'Selected team does not exist',
            'user_not_in_team' => 'Selected user does not belong to the selected team',
        ],
        'team' => [
            'name_required' => 'Team name is required',
            'name_max' => 'Team name must not exceed 255 characters',
            'description_max' => 'Team description must not exceed 1000 characters',
        ],
    ],

    // Success messages
    'success_messages' => [
        'task' => [
            'created' => 'Task created successfully',
            'updated' => 'Task updated successfully',
            'deleted' => 'Task deleted successfully',
            'status_updated' => 'Task status updated successfully',
        ],
        'team' => [
            'created' => 'Team created successfully',
            'updated' => 'Team updated successfully',
            'deleted' => 'Team deleted successfully',
        ],
        'notification' => [
            'marked_read' => 'Notification marked as read',
            'marked_all_read' => 'All notifications marked as read',
            'deleted' => 'Notification deleted successfully',
            'deleted_all' => 'All notifications deleted successfully',
        ],
    ],

    // Error messages
    'error_messages' => [
        'unauthorized' => 'You are not authorized to access this page',
        'forbidden' => 'You are not allowed to perform this action',
        'not_found' => 'The requested item was not found',
        'validation_failed' => 'Validation failed',
    ],
]; 