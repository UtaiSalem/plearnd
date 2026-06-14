<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

function layout(string $title, array $user, string $content, ?array $flash = null): void
{
    $nav = match ($user['role']) {
        'staff' => [
            '/?page=staff' => 'Queue',
            '/?page=staff_rooms' => 'Rooms',
        ],
        'admin' => [
            '/?page=admin_rooms' => 'Rooms',
            '/?page=admin_users' => 'Users',
        ],
        default => [
            '/?page=dashboard' => 'Dashboard',
            '/?page=bookings' => 'My Bookings',
            '/?page=new_booking' => 'New Booking',
        ],
    };

    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h(page_title($title)) ?></title>
        <style>
            :root { --bg:#f4f7fb; --card:#ffffff; --line:#d8e0ec; --text:#132238; --muted:#5d6b80; --primary:#1f4fa3; --success:#15803d; --warning:#b45309; --danger:#b91c1c; --info:#0f766e; }
            * { box-sizing:border-box; } body { margin:0; font-family:Segoe UI,Arial,sans-serif; background:var(--bg); color:var(--text); }
            a { color:inherit; text-decoration:none; } .shell { display:grid; grid-template-columns:240px 1fr; min-height:100vh; }
            .sidebar { background:#0f274e; color:#fff; padding:24px 18px; } .brand { font-size:24px; font-weight:700; margin-bottom:24px; }
            .sidebar a { display:block; padding:12px 14px; border-radius:12px; color:rgba(255,255,255,.85); margin-bottom:6px; }
            .sidebar a:hover, .sidebar a.active { background:rgba(255,255,255,.12); color:#fff; }
            .sidebar .user { margin-top:24px; padding:14px; border-radius:12px; background:rgba(255,255,255,.08); font-size:14px; }
            .main { padding:24px; } .top { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; margin-bottom:24px; }
            .top h1 { margin:0; font-size:28px; } .top p { margin:8px 0 0; color:var(--muted); }
            .actions { display:flex; gap:10px; flex-wrap:wrap; }
            .btn, button { border:0; border-radius:12px; padding:11px 16px; font:inherit; cursor:pointer; background:var(--primary); color:#fff; }
            .btn.secondary, button.secondary { background:#fff; color:var(--text); border:1px solid var(--line); }
            .btn.warn, button.warn { background:var(--warning); } .btn.danger, button.danger { background:var(--danger); } .btn.success, button.success { background:var(--success); }
            .grid { display:grid; gap:16px; } .grid.stats { grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); }
            .grid.two { grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); } .grid.three { grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); }
            .card { background:var(--card); border:1px solid var(--line); border-radius:18px; padding:18px; box-shadow:0 6px 18px rgba(20,34,56,.04); }
            .card h3, .card h2 { margin:0 0 10px; } .muted { color:var(--muted); } .badge { display:inline-flex; align-items:center; border-radius:999px; padding:6px 10px; font-size:12px; font-weight:600; }
            .badge.primary { background:#dbe8ff; color:var(--primary); } .badge.success { background:#dcfce7; color:var(--success); } .badge.warning { background:#fef3c7; color:var(--warning); } .badge.danger { background:#fee2e2; color:var(--danger); } .badge.info, .badge.muted { background:#dff6f4; color:var(--info); }
            .table { width:100%; border-collapse:collapse; } .table th,.table td { padding:12px 10px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; } .table th { color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.04em; }
            .field { margin-bottom:14px; } .field label { display:block; font-size:13px; color:var(--muted); margin-bottom:6px; } .field input,.field select,.field textarea { width:100%; border:1px solid var(--line); border-radius:12px; padding:12px 14px; font:inherit; background:#fff; }
            .field textarea { min-height:108px; resize:vertical; } .alert { padding:14px 16px; border-radius:14px; margin-bottom:16px; } .alert.success { background:#dcfce7; color:#166534; } .alert.error { background:#fee2e2; color:#991b1b; } .alert.info { background:#dbeafe; color:#1d4ed8; }
            .stack { display:flex; flex-direction:column; gap:14px; } .row { display:flex; gap:12px; flex-wrap:wrap; } .split { display:grid; grid-template-columns:2fr 1fr; gap:16px; } .small { font-size:13px; } .pill-list { display:flex; flex-wrap:wrap; gap:8px; }
            @media (max-width: 920px) { .shell { grid-template-columns:1fr; } .sidebar { padding:18px; } .main { padding:18px; } .split { grid-template-columns:1fr; } }
        </style>
    </head>
    <body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">LabReserve</div>
            <?php foreach ($nav as $href => $label): ?>
                <a href="<?= h($href) ?>" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', trim($href, '/')) ? 'active' : '' ?>"><?= h($label) ?></a>
            <?php endforeach; ?>
            <a href="/?page=profile">Profile</a>
            <a href="/?page=logout">Sign out</a>
            <div class="user">
                <strong><?= h($user['name']) ?></strong><br>
                <span><?= h($user['role']) ?> • <?= h($user['department']) ?></span>
            </div>
        </aside>
        <main class="main">
            <?php if ($flash): ?>
                <div class="alert <?= $flash['type'] === 'error' ? 'error' : ($flash['type'] === 'success' ? 'success' : 'info') ?>">
                    <?= h($flash['message']) ?>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
    </body>
    </html>
    <?php
}

function login_page(?string $error = null): void
{
    $flash = pull_flash();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h(page_title('Login')) ?></title>
        <style>
            body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:linear-gradient(135deg,#0f274e,#1f4fa3);min-height:100vh;display:grid;place-items:center;color:#132238}
            .panel{background:#fff;border-radius:24px;padding:32px;max-width:420px;width:calc(100% - 32px);box-shadow:0 20px 60px rgba(0,0,0,.18)}
            .panel h1{margin:0 0 8px}.panel p{color:#5d6b80}.field{margin-bottom:14px}.field label{display:block;margin-bottom:6px;color:#5d6b80;font-size:13px}
            .field input{width:100%;padding:12px 14px;border:1px solid #d8e0ec;border-radius:12px;font:inherit}.btn{width:100%;padding:12px 14px;border:0;border-radius:12px;background:#1f4fa3;color:#fff;font:inherit;cursor:pointer}
            .demo{margin:14px 0;padding:14px;border-radius:14px;background:#f4f7fb}.alert{padding:12px 14px;border-radius:12px;margin-bottom:14px}.error{background:#fee2e2;color:#991b1b}.success{background:#dcfce7;color:#166534}
            .row{display:flex;gap:8px;flex-wrap:wrap}.pill{padding:8px 10px;border-radius:999px;background:#eef4ff;font-size:12px}
        </style>
    </head>
    <body>
    <form class="panel" method="post" action="/?page=login">
        <h1>LabReserve</h1>
        <p>Compact PHP + SQLite lab room booking system.</p>
        <?php if ($flash): ?><div class="alert success"><?= h($flash['message']) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
        <div class="demo">
            <strong>Demo accounts</strong>
            <div class="row" style="margin-top:8px">
                <span class="pill">researcher</span>
                <span class="pill">staff</span>
                <span class="pill">admin</span>
                <span class="pill">password: <?= h((string) app_config('demo_password')) ?></span>
            </div>
        </div>
        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
        <div class="field">
            <label>Username or email</label>
            <input name="login" value="researcher" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" value="<?= h((string) app_config('demo_password')) ?>" required>
        </div>
        <button class="btn" type="submit">Sign in</button>
    </form>
    </body>
    </html>
    <?php
}

function bookings_summary(array $bookings): array
{
    return [
        'pending' => count(array_filter($bookings, fn ($booking) => $booking['status'] === 'pending')),
        'approved' => count(array_filter($bookings, fn ($booking) => $booking['status'] === 'approved')),
        'rejected' => count(array_filter($bookings, fn ($booking) => $booking['status'] === 'rejected')),
    ];
}

$page = $_GET['page'] ?? 'dashboard';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        if ($page === 'login') {
            $user = find_user_by_login(trim((string) ($_POST['login'] ?? '')));
            $password = (string) ($_POST['password'] ?? '');

            if (!$user || !password_verify($password, $user['password'])) {
                login_page('Invalid username or password.');
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            flash('success', 'Signed in successfully.');
            redirect_to(home_path($user));
        }

        $user = require_login();

        if ($page === 'new_booking') {
            $roomId = (int) ($_POST['room_id'] ?? 0);
            $room = find_room($roomId);
            $startAt = sprintf('%sT%s:00', $_POST['date'] ?? '', $_POST['start_time'] ?? '');
            $endAt = sprintf('%sT%s:00', $_POST['date'] ?? '', $_POST['end_time'] ?? '');

            if (!$room) {
                throw new RuntimeException('Please choose a valid room.');
            }
            if (strtotime($endAt) <= strtotime($startAt)) {
                throw new RuntimeException('End time must be after start time.');
            }
            if ($room['status'] === 'maintenance') {
                throw new RuntimeException('This room is under maintenance.');
            }
            if (booking_conflict($roomId, $startAt, $endAt)) {
                throw new RuntimeException('This time slot conflicts with another booking.');
            }

            create_booking($user, [
                'room_id' => $roomId,
                'start_at' => date('c', strtotime($startAt)),
                'end_at' => date('c', strtotime($endAt)),
                'attendees' => max(1, (int) ($_POST['attendees'] ?? 1)),
                'purpose' => trim((string) ($_POST['purpose'] ?? '')),
                'requirements' => trim((string) ($_POST['requirements'] ?? '')),
            ]);

            flash('success', 'Booking submitted.');
            redirect_to('/?page=bookings');
        }

        if ($page === 'staff_review') {
            require_role(['staff', 'admin']);
            $id = (int) ($_POST['booking_id'] ?? 0);
            $status = (string) ($_POST['status'] ?? 'pending');
            $reason = trim((string) ($_POST['reason'] ?? ''));
            update_booking_review($id, $status, $reason ?: 'Room or schedule not available.');
            flash('success', $status === 'approved' ? 'Booking approved.' : 'Booking rejected.');
            redirect_to('/?page=staff');
        }

        if ($page === 'staff_status') {
            require_role(['staff', 'admin']);
            update_booking_staff_status((int) ($_POST['booking_id'] ?? 0), (string) ($_POST['staff_status'] ?? 'scheduled'));
            flash('success', 'Operational status updated.');
            redirect_to('/?page=staff');
        }

        if ($page === 'admin_room_save') {
            require_role(['admin']);
            save_room([
                'id' => ($_POST['id'] ?? '') !== '' ? (int) $_POST['id'] : null,
                'name' => trim((string) ($_POST['name'] ?? '')),
                'building' => trim((string) ($_POST['building'] ?? '')),
                'floor' => trim((string) ($_POST['floor'] ?? '')),
                'category' => trim((string) ($_POST['category'] ?? '')),
                'capacity' => max(1, (int) ($_POST['capacity'] ?? 1)),
                'manager' => trim((string) ($_POST['manager'] ?? '')),
                'contact' => trim((string) ($_POST['contact'] ?? '')),
                'status' => trim((string) ($_POST['status'] ?? 'available')),
                'open_hours' => trim((string) ($_POST['open_hours'] ?? '')),
                'summary' => trim((string) ($_POST['summary'] ?? '')),
                'equipment' => trim((string) ($_POST['equipment'] ?? '')),
            ]);
            flash('success', 'Room saved successfully.');
            redirect_to('/?page=admin_rooms');
        }

        if ($page === 'reset_demo') {
            require_role(['admin']);
            reset_database();
            $_SESSION = ['csrf_token' => csrf_token(), 'user_id' => $user['id']];
            flash('success', 'Demo database has been reset.');
            redirect_to('/?page=admin_rooms');
        }
    }

    if ($page === 'logout') {
        session_destroy();
        session_start();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
        flash('success', 'Signed out.');
        redirect_to('/?page=login');
    }

    if ($page === 'login') {
        login_page();
        exit;
    }

    $user = require_login();
    $flash = pull_flash();

    ob_start();

    if ($page === 'dashboard') {
        $bookings = all_bookings('requester', (int) $user['id']);
        $summary = bookings_summary($bookings);
        $next = null;
        foreach ($bookings as $booking) {
            if ($booking['status'] !== 'rejected' && strtotime($booking['start_at']) >= time()) {
                $next = $booking;
                break;
            }
        }
        ?>
        <div class="top">
            <div><h1>Researcher Dashboard</h1><p>Overview of your lab room requests.</p></div>
            <div class="actions"><a class="btn" href="/?page=new_booking">New Booking</a></div>
        </div>
        <div class="grid stats">
            <div class="card"><div class="muted small">Pending</div><h2><?= $summary['pending'] ?></h2></div>
            <div class="card"><div class="muted small">Approved</div><h2><?= $summary['approved'] ?></h2></div>
            <div class="card"><div class="muted small">Rejected</div><h2><?= $summary['rejected'] ?></h2></div>
        </div>
        <div class="split" style="margin-top:16px">
            <div class="card">
                <h3>Next Booking</h3>
                <?php if ($next): $badge = status_badge($next['status'], $next['staff_status']); ?>
                    <div class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></div>
                    <p><strong><?= h($next['room_name']) ?></strong></p>
                    <p class="muted"><?= h(fmt_datetime($next['start_at'])) ?> • <?= h(fmt_time_range($next['start_at'], $next['end_at'])) ?></p>
                    <p><?= h($next['purpose']) ?></p>
                <?php else: ?>
                    <p class="muted">No upcoming booking yet.</p>
                <?php endif; ?>
            </div>
            <div class="card">
                <h3>Rooms Available</h3>
                <div class="stack">
                    <?php foreach (array_slice(all_rooms(), 0, 3) as $room): $badge = room_badge($room['status']); ?>
                        <div>
                            <strong><?= h($room['name']) ?></strong><br>
                            <span class="muted small"><?= h($room['building']) ?> • Floor <?= h($room['floor']) ?></span><br>
                            <span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    } elseif ($page === 'bookings') {
        $bookings = all_bookings('requester', (int) $user['id']);
        ?>
        <div class="top">
            <div><h1>My Bookings</h1><p>All room reservations submitted by you.</p></div>
            <div class="actions"><a class="btn" href="/?page=new_booking">New Booking</a></div>
        </div>
        <div class="card">
            <table class="table">
                <thead><tr><th>Status</th><th>Room</th><th>Date</th><th>Time</th><th>Purpose</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($bookings as $booking): $badge = status_badge($booking['status'], $booking['staff_status']); ?>
                    <tr>
                        <td><span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span></td>
                        <td><?= h($booking['room_name']) ?></td>
                        <td><?= h(fmt_date($booking['start_at'])) ?></td>
                        <td><?= h(fmt_time_range($booking['start_at'], $booking['end_at'])) ?></td>
                        <td><?= h($booking['status'] === 'rejected' && $booking['rejection_reason'] ? $booking['rejection_reason'] : $booking['purpose']) ?></td>
                        <td><a class="btn secondary" href="/?page=booking_view&id=<?= (int) $booking['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    } elseif ($page === 'new_booking') {
        require_role(['requester', 'admin']);
        $rooms = all_rooms();
        $selectedId = (int) ($rooms[0]['id'] ?? 0);
        ?>
        <div class="top">
            <div><h1>New Booking</h1><p>Reserve a scientific lab room.</p></div>
        </div>
        <div class="split">
            <form class="card" method="post" action="/?page=new_booking">
                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                <div class="field"><label>Room</label><select name="room_id"><?php foreach ($rooms as $room): ?><option value="<?= (int) $room['id'] ?>"<?= $selectedId === (int) $room['id'] ? ' selected' : '' ?>><?= h($room['name']) ?></option><?php endforeach; ?></select></div>
                <div class="row">
                    <div class="field" style="flex:1"><label>Date</label><input type="date" name="date" value="<?= h(date('Y-m-d')) ?>" required></div>
                    <div class="field" style="flex:1"><label>Attendees</label><input type="number" name="attendees" value="4" min="1" required></div>
                </div>
                <div class="row">
                    <div class="field" style="flex:1"><label>Start</label><input type="time" name="start_time" value="09:00" required></div>
                    <div class="field" style="flex:1"><label>End</label><input type="time" name="end_time" value="11:00" required></div>
                </div>
                <div class="field"><label>Purpose</label><textarea name="purpose" required></textarea></div>
                <div class="field"><label>Special requirements</label><textarea name="requirements"></textarea></div>
                <button type="submit">Submit Booking</button>
            </form>
            <div class="card">
                <h3>Room Directory</h3>
                <div class="stack">
                    <?php foreach ($rooms as $room): $badge = room_badge($room['status']); ?>
                        <div>
                            <strong><?= h($room['name']) ?></strong><br>
                            <span class="muted small"><?= h($room['building']) ?> • Floor <?= h($room['floor']) ?> • <?= h($room['category']) ?></span><br>
                            <span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span>
                            <p class="muted small"><?= h($room['summary']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    } elseif ($page === 'booking_view') {
        $booking = find_booking((int) ($_GET['id'] ?? 0));
        if (!$booking) {
            throw new RuntimeException('Booking not found.');
        }
        $badge = status_badge($booking['status'], $booking['staff_status']);
        ?>
        <div class="top">
            <div><h1>Booking Detail</h1><p>Reservation summary and room information.</p></div>
            <div class="actions"><a class="btn secondary" href="/?page=bookings">Back</a></div>
        </div>
        <div class="split">
            <div class="card">
                <span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span>
                <h2><?= h($booking['room_name']) ?></h2>
                <p class="muted"><?= h(fmt_datetime($booking['start_at'])) ?> • <?= h(fmt_time_range($booking['start_at'], $booking['end_at'])) ?></p>
                <p><strong>Requester:</strong> <?= h($booking['requester_name']) ?> • <?= h($booking['department']) ?></p>
                <p><strong>Attendees:</strong> <?= (int) $booking['attendees'] ?></p>
                <p><strong>Purpose:</strong><br><?= nl2br(h($booking['purpose'])) ?></p>
                <p><strong>Requirements:</strong><br><?= nl2br(h($booking['requirements'] ?: '-')) ?></p>
                <?php if ($booking['rejection_reason']): ?><div class="alert error"><?= h($booking['rejection_reason']) ?></div><?php endif; ?>
            </div>
            <div class="card">
                <h3>Room Profile</h3>
                <p><strong><?= h($booking['room_name']) ?></strong></p>
                <p class="muted"><?= h($booking['building']) ?> • Floor <?= h($booking['floor']) ?></p>
                <p><?= h($booking['summary']) ?></p>
                <p><strong>Equipment:</strong><br><?= h($booking['equipment']) ?></p>
            </div>
        </div>
        <?php
    } elseif ($page === 'staff') {
        require_role(['staff', 'admin']);
        $bookings = all_bookings();
        $pending = array_values(array_filter($bookings, fn ($booking) => $booking['status'] === 'pending'));
        $today = date('Y-m-d');
        $todayApproved = array_values(array_filter($bookings, fn ($booking) => $booking['status'] === 'approved' && str_starts_with($booking['start_at'], $today)));
        ?>
        <div class="top">
            <div><h1>Lab Queue</h1><p>Approve requests and update room readiness.</p></div>
        </div>
        <div class="grid stats">
            <div class="card"><div class="muted small">Pending Approval</div><h2><?= count($pending) ?></h2></div>
            <div class="card"><div class="muted small">Approved Today</div><h2><?= count($todayApproved) ?></h2></div>
            <div class="card"><div class="muted small">Rooms</div><h2><?= count(all_rooms()) ?></h2></div>
        </div>
        <div class="card" style="margin-top:16px">
            <h3>Pending Requests</h3>
            <div class="stack">
                <?php foreach ($pending as $booking): ?>
                    <div class="card" style="background:#fbfdff">
                        <strong><?= h($booking['room_name']) ?></strong><br>
                        <span class="muted small"><?= h($booking['requester_name']) ?> • <?= h(fmt_date($booking['start_at'])) ?> • <?= h(fmt_time_range($booking['start_at'], $booking['end_at'])) ?></span>
                        <p><?= h($booking['purpose']) ?></p>
                        <div class="row">
                            <form method="post" action="/?page=staff_review">
                                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <input type="hidden" name="status" value="approved">
                                <button class="success" type="submit">Approve</button>
                            </form>
                            <form method="post" action="/?page=staff_review" style="display:flex;gap:8px;flex-wrap:wrap">
                                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <input type="hidden" name="status" value="rejected">
                                <input name="reason" placeholder="Reason" style="padding:11px 12px;border:1px solid #d8e0ec;border-radius:12px">
                                <button class="danger" type="submit">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!$pending): ?><p class="muted">No pending requests.</p><?php endif; ?>
            </div>
        </div>
        <div class="card" style="margin-top:16px">
            <h3>Today's Approved Sessions</h3>
            <table class="table">
                <thead><tr><th>Room</th><th>Requester</th><th>Time</th><th>Status</th><th>Update</th></tr></thead>
                <tbody>
                <?php foreach ($todayApproved as $booking): $badge = status_badge($booking['status'], $booking['staff_status']); ?>
                    <tr>
                        <td><?= h($booking['room_name']) ?></td>
                        <td><?= h($booking['requester_name']) ?></td>
                        <td><?= h(fmt_time_range($booking['start_at'], $booking['end_at'])) ?></td>
                        <td><span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span></td>
                        <td>
                            <form method="post" action="/?page=staff_status" class="row">
                                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <button class="secondary" name="staff_status" value="ready" type="submit">Ready</button>
                                <button class="secondary" name="staff_status" value="in_use" type="submit">In Use</button>
                                <button class="secondary" name="staff_status" value="cleanup" type="submit">Cleaning</button>
                                <button class="secondary" name="staff_status" value="issue" type="submit">Issue</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$todayApproved): ?><tr><td colspan="5" class="muted">No approved session today.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    } elseif ($page === 'staff_rooms') {
        require_role(['staff', 'admin']);
        $rooms = all_rooms();
        $bookings = all_bookings();
        ?>
        <div class="top">
            <div><h1>Room Board</h1><p>Operational status of all lab rooms.</p></div>
        </div>
        <div class="grid three">
            <?php foreach ($rooms as $room): $badge = room_badge($room['status']); ?>
                <div class="card">
                    <span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span>
                    <h3><?= h($room['name']) ?></h3>
                    <p class="muted"><?= h($room['building']) ?> • Floor <?= h($room['floor']) ?></p>
                    <p><?= h($room['summary']) ?></p>
                    <p class="small muted">Equipment: <?= h($room['equipment']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    } elseif ($page === 'admin_rooms') {
        require_role(['admin']);
        $rooms = all_rooms();
        $edit = isset($_GET['edit']) ? find_room((int) $_GET['edit']) : null;
        ?>
        <div class="top">
            <div><h1>Rooms</h1><p>Manage scientific lab rooms and reset demo data.</p></div>
            <div class="actions">
                <a class="btn secondary" href="/?page=admin_rooms">New Form</a>
                <form method="post" action="/?page=reset_demo" onsubmit="return confirm('Reset demo database?')">
                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                    <button class="warn" type="submit">Reset Demo DB</button>
                </form>
            </div>
        </div>
        <div class="split">
            <div class="card">
                <h3><?= $edit ? 'Edit Room' : 'Create Room' ?></h3>
                <form method="post" action="/?page=admin_room_save">
                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= h((string) ($edit['id'] ?? '')) ?>">
                    <div class="row">
                        <div class="field" style="flex:1"><label>Name</label><input name="name" value="<?= h($edit['name'] ?? '') ?>" required></div>
                        <div class="field" style="flex:1"><label>Category</label><input name="category" value="<?= h($edit['category'] ?? '') ?>" required></div>
                    </div>
                    <div class="row">
                        <div class="field" style="flex:1"><label>Building</label><input name="building" value="<?= h($edit['building'] ?? '') ?>" required></div>
                        <div class="field" style="flex:1"><label>Floor</label><input name="floor" value="<?= h($edit['floor'] ?? '') ?>" required></div>
                    </div>
                    <div class="row">
                        <div class="field" style="flex:1"><label>Capacity</label><input type="number" name="capacity" value="<?= h((string) ($edit['capacity'] ?? 8)) ?>" required></div>
                        <div class="field" style="flex:1"><label>Status</label><select name="status"><?php foreach (['available','limited','maintenance'] as $status): ?><option value="<?= h($status) ?>"<?= ($edit['status'] ?? '') === $status ? ' selected' : '' ?>><?= h($status) ?></option><?php endforeach; ?></select></div>
                    </div>
                    <div class="row">
                        <div class="field" style="flex:1"><label>Manager</label><input name="manager" value="<?= h($edit['manager'] ?? '') ?>" required></div>
                        <div class="field" style="flex:1"><label>Contact</label><input name="contact" value="<?= h($edit['contact'] ?? '') ?>" required></div>
                    </div>
                    <div class="field"><label>Open Hours</label><input name="open_hours" value="<?= h($edit['open_hours'] ?? '') ?>" required></div>
                    <div class="field"><label>Summary</label><textarea name="summary" required><?= h($edit['summary'] ?? '') ?></textarea></div>
                    <div class="field"><label>Equipment (comma separated)</label><textarea name="equipment"><?= h($edit['equipment'] ?? '') ?></textarea></div>
                    <button type="submit">Save Room</button>
                </form>
            </div>
            <div class="card">
                <h3>Current Rooms</h3>
                <div class="stack">
                    <?php foreach ($rooms as $room): $badge = room_badge($room['status']); ?>
                        <div>
                            <span class="badge <?= h($badge['class']) ?>"><?= h($badge['label']) ?></span><br>
                            <strong><?= h($room['name']) ?></strong><br>
                            <span class="muted small"><?= h($room['building']) ?> • Floor <?= h($room['floor']) ?> • <?= h($room['category']) ?></span><br>
                            <a class="btn secondary" style="margin-top:8px;display:inline-block" href="/?page=admin_rooms&edit=<?= (int) $room['id'] ?>">Edit</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    } elseif ($page === 'admin_users') {
        require_role(['admin']);
        $users = all_users();
        ?>
        <div class="top">
            <div><h1>Users</h1><p>Demo accounts available in this workspace.</p></div>
        </div>
        <div class="card">
            <table class="table">
                <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Department</th><th>Role</th></tr></thead>
                <tbody>
                <?php foreach ($users as $entry): ?>
                    <tr>
                        <td><?= h($entry['name']) ?></td>
                        <td><?= h($entry['username']) ?></td>
                        <td><?= h($entry['email']) ?></td>
                        <td><?= h($entry['department']) ?></td>
                        <td><span class="badge muted"><?= h($entry['role']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    } elseif ($page === 'profile') {
        ?>
        <div class="top">
            <div><h1>Profile</h1><p>Current signed-in account.</p></div>
        </div>
        <div class="card">
            <p><strong>Name:</strong> <?= h($user['name']) ?></p>
            <p><strong>Username:</strong> <?= h($user['username']) ?></p>
            <p><strong>Email:</strong> <?= h($user['email']) ?></p>
            <p><strong>Department:</strong> <?= h($user['department']) ?></p>
            <p><strong>Role:</strong> <?= h($user['role']) ?></p>
        </div>
        <?php
    } else {
        redirect_to(home_path($user));
    }

    layout(match ($page) {
        'dashboard' => 'Dashboard',
        'bookings' => 'My Bookings',
        'new_booking' => 'New Booking',
        'booking_view' => 'Booking Detail',
        'staff' => 'Lab Queue',
        'staff_rooms' => 'Room Board',
        'admin_rooms' => 'Rooms',
        'admin_users' => 'Users',
        'profile' => 'Profile',
        default => 'LabReserve',
    }, $user, ob_get_clean(), $flash);
} catch (Throwable $e) {
    if (current_user()) {
        flash('error', $e->getMessage());
        redirect_to($_SERVER['HTTP_REFERER'] ?? home_path(current_user()));
    }
    login_page($e->getMessage());
}
