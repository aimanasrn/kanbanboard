<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Report - {{ $project->name }}</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #2F2F45; margin: 40px; background: #fff; }
        .header { border-bottom: 3px solid #6E63D9; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .logo { font-size: 24px; font-weight: 800; color: #6E63D9; }
        .title { font-size: 28px; font-weight: 800; margin: 0; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { background: #F8F5FF; border: 1px solid #ECE8F7; padding: 20px; border-radius: 16px; text-align: center; }
        .card-num { font-size: 32px; font-weight: 800; color: #6E63D9; }
        .card-lbl { font-size: 12px; font-weight: 700; color: #7A7A92; text-transform: uppercase; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #ECE8F7; text-align: left; font-size: 13px; }
        th { background: #F8F5FF; font-weight: 700; color: #2F2F45; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .print-btn { background: #6E63D9; color: white; border: none; padding: 10px 20px; border-radius: 9999px; font-weight: 700; cursor: pointer; float: right; }
        @media print { .print-btn { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">🖨️ Print / Save as PDF</button>
    
    <div class="header">
        <div>
            <div class="logo">KanbanFlow</div>
            <h1 class="title">{{ $project->name }}</h1>
            <p style="color: #7A7A92; font-size: 13px; margin-top: 4px;">Generated Executive Performance Report • {{ date('F d, Y') }}</p>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-num">{{ $stats['total'] }}</div>
            <div class="card-lbl">Total Tasks</div>
        </div>
        <div class="card">
            <div class="card-num">{{ $stats['completion_rate'] }}%</div>
            <div class="card-lbl">Completion Rate</div>
        </div>
        <div class="card">
            <div class="card-num" style="color: #FF6B81;">{{ $stats['urgent'] }}</div>
            <div class="card-lbl">Urgent Items</div>
        </div>
        <div class="card">
            <div class="card-num" style="color: #72D49A;">{{ $stats['done'] }}</div>
            <div class="card-lbl">Completed</div>
        </div>
    </div>

    <h2>Task Distribution Overview</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Task Title</th>
                <th>Column Status</th>
                <th>Priority</th>
                <th>Assignee</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project->tasks as $task)
                <tr>
                    <td>#{{ $task->id }}</td>
                    <td><strong>{{ $task->title }}</strong></td>
                    <td><span class="badge" style="background: #6E63D9/10; color: #6E63D9;">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</span></td>
                    <td><span class="badge" style="background: #FF6B81/15; color: #FF6B81;">{{ strtoupper($task->priority) }}</span></td>
                    <td>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</td>
                    <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
