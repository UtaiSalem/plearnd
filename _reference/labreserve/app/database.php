<?php

declare(strict_types=1);

function ensure_storage(): void
{
    $databaseDir = __DIR__ . '/../database';
    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0777, true);
    }

    $sessionDir = $databaseDir . '/sessions';
    if (!is_dir($sessionDir)) {
        mkdir($sessionDir, 0777, true);
    }
}

function db(): PDO
{
    global $labreservePdo;

    if ($labreservePdo instanceof PDO) {
        return $labreservePdo;
    }

    $labreservePdo = new PDO('sqlite:' . __DIR__ . '/../database/labreserve.sqlite');
    $labreservePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $labreservePdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $labreservePdo->exec('PRAGMA foreign_keys = ON');

    return $labreservePdo;
}

function ensure_database(): void
{
    $pdo = db();
    $exists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetchColumn();
    if ($exists) {
        return;
    }

    $sql = file_get_contents(__DIR__ . '/../database/migrations/001_lab_reserve.sql');
    if ($sql === false) {
        throw new RuntimeException('Migration file is missing.');
    }

    $pdo->exec($sql);

    $password = password_hash((string) app_config('demo_password'), PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (username, name, email, password, role, department) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(['researcher', 'Dr. Narin Wong', 'researcher@lab.local', $password, 'requester', 'Chemistry']);
    $stmt->execute(['staff', 'Lab Operations', 'staff@lab.local', $password, 'staff', 'Central Laboratory']);
    $stmt->execute(['admin', 'System Admin', 'admin@lab.local', $password, 'admin', 'Digital Services']);

    $pdo->exec("
        INSERT INTO rooms (name, building, floor, category, capacity, manager, contact, status, open_hours, summary, equipment)
        VALUES
        ('Analytical Chemistry Lab', 'Science Building A', '3', 'Wet Lab', 16, 'Lab Operations', '081-000-1001', 'available', '08:30-17:00', 'Suitable for titration, sample preparation, and chemical analysis.', 'Fume hood, Analytical balance, pH meter'),
        ('Microbiology Lab', 'Science Building B', '2', 'Bio Safety', 12, 'Lab Operations', '081-000-1002', 'limited', '09:00-16:30', 'Controlled room for culturing and microbial testing.', 'Autoclave, Laminar flow, Incubator'),
        ('Instrument Room', 'Science Building C', '1', 'Instrument', 8, 'Lab Operations', '081-000-1003', 'maintenance', '10:00-16:00', 'Shared room for precision instruments and supervised access.', 'GC-MS, UV-Vis, HPLC')
    ");

    $researcherId = (int) $pdo->query("SELECT id FROM users WHERE username = 'researcher'")->fetchColumn();
    $chemRoomId = (int) $pdo->query("SELECT id FROM rooms WHERE name = 'Analytical Chemistry Lab'")->fetchColumn();
    $bioRoomId = (int) $pdo->query("SELECT id FROM rooms WHERE name = 'Microbiology Lab'")->fetchColumn();
    $instRoomId = (int) $pdo->query("SELECT id FROM rooms WHERE name = 'Instrument Room'")->fetchColumn();

    $stmt = $pdo->prepare('
        INSERT INTO bookings (room_id, user_id, requester_name, department, start_at, end_at, attendees, purpose, requirements, status, staff_status, rejection_reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([$chemRoomId, $researcherId, 'Dr. Narin Wong', 'Chemistry', date('c', strtotime('+1 day 09:00')), date('c', strtotime('+1 day 11:00')), 6, 'Prepare samples for water quality testing.', 'Need fume hood and analytical balance.', 'approved', 'scheduled', null]);
    $stmt->execute([$bioRoomId, $researcherId, 'Dr. Narin Wong', 'Chemistry', date('c', strtotime('+2 day 09:00')), date('c', strtotime('+2 day 11:00')), 4, 'Run contamination screening on swab samples.', 'Need incubator access for 2 hours.', 'pending', 'scheduled', null]);
    $stmt->execute([$instRoomId, $researcherId, 'Dr. Narin Wong', 'Chemistry', date('c', strtotime('+3 day 10:00')), date('c', strtotime('+3 day 11:30')), 2, 'Use GC-MS for solvent purity verification.', 'Need supervised access.', 'rejected', 'issue', 'Room is under maintenance this week.']);
}

function reset_database(): void
{
    global $labreservePdo;
    $file = __DIR__ . '/../database/labreserve.sqlite';
    $labreservePdo = null;
    if (file_exists($file)) {
        unlink($file);
    }
    ensure_database();
}

function find_user(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, username, name, email, role, department FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function find_user_by_login(string $login): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->execute([$login, $login]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function all_users(): array
{
    return db()->query('SELECT id, username, name, email, role, department FROM users ORDER BY role, name')->fetchAll();
}

function all_rooms(): array
{
    return db()->query('SELECT * FROM rooms ORDER BY name')->fetchAll();
}

function find_room(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM rooms WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function save_room(array $input): void
{
    $pdo = db();
    if (!empty($input['id'])) {
        $stmt = $pdo->prepare('
            UPDATE rooms
            SET name = ?, building = ?, floor = ?, category = ?, capacity = ?, manager = ?, contact = ?, status = ?, open_hours = ?, summary = ?, equipment = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ');
        $stmt->execute([
            $input['name'], $input['building'], $input['floor'], $input['category'], $input['capacity'], $input['manager'], $input['contact'],
            $input['status'], $input['open_hours'], $input['summary'], $input['equipment'], $input['id'],
        ]);
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO rooms (name, building, floor, category, capacity, manager, contact, status, open_hours, summary, equipment)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $input['name'], $input['building'], $input['floor'], $input['category'], $input['capacity'], $input['manager'], $input['contact'],
            $input['status'], $input['open_hours'], $input['summary'], $input['equipment'],
        ]);
    }
}

function all_bookings(?string $role = null, ?int $userId = null): array
{
    $sql = '
        SELECT b.*, r.name AS room_name, u.username
        FROM bookings b
        INNER JOIN rooms r ON r.id = b.room_id
        INNER JOIN users u ON u.id = b.user_id
    ';
    $params = [];

    if ($role === 'requester' && $userId) {
        $sql .= ' WHERE b.user_id = ?';
        $params[] = $userId;
    }

    $sql .= ' ORDER BY b.start_at DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function find_booking(int $id): ?array
{
    $stmt = db()->prepare('
        SELECT b.*, r.name AS room_name, r.building, r.floor, r.category, r.open_hours, r.summary, r.equipment
        FROM bookings b
        INNER JOIN rooms r ON r.id = b.room_id
        WHERE b.id = ?
    ');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function booking_conflict(int $roomId, string $startAt, string $endAt, ?int $ignoreId = null): bool
{
    $sql = '
        SELECT COUNT(*)
        FROM bookings
        WHERE room_id = ?
          AND status != ?
          AND start_at < ?
          AND end_at > ?
    ';

    $params = [$roomId, 'rejected', $endAt, $startAt];

    if ($ignoreId) {
        $sql .= ' AND id != ?';
        $params[] = $ignoreId;
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function create_booking(array $user, array $input): void
{
    $stmt = db()->prepare('
        INSERT INTO bookings (room_id, user_id, requester_name, department, start_at, end_at, attendees, purpose, requirements, status, staff_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $input['room_id'],
        $user['id'],
        $user['name'],
        $user['department'],
        $input['start_at'],
        $input['end_at'],
        $input['attendees'],
        $input['purpose'],
        $input['requirements'],
        'pending',
        'scheduled',
    ]);
}

function update_booking_review(int $id, string $status, ?string $reason = null): void
{
    $stmt = db()->prepare('UPDATE bookings SET status = ?, rejection_reason = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute([$status, $status === 'rejected' ? $reason : null, $id]);
}

function update_booking_staff_status(int $id, string $staffStatus): void
{
    $stmt = db()->prepare('UPDATE bookings SET staff_status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute([$staffStatus, $id]);
}
